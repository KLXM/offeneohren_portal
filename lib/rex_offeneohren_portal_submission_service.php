<?php

class rex_offeneohren_portal_submission_service
{
    /**
     * @param array<string, mixed> $post
     * @return array{success:bool,message:string}
     */
    public static function handlePublicSubmission(array $post): array
    {
        $csrf = rex_csrf_token::factory('oo_submission_form');
        if (!$csrf->isValid()) {
            return ['success' => false, 'message' => 'Sicherheitspruefung fehlgeschlagen.'];
        }

        if (!self::isAntiBotValid($post)) {
            return ['success' => false, 'message' => 'Eingabe konnte nicht verarbeitet werden.'];
        }

        if (self::isRateLimited()) {
            return ['success' => false, 'message' => 'Zu viele Anfragen in kurzer Zeit. Bitte spaeter erneut versuchen.'];
        }

        $type = (string) ($post['submission_type'] ?? 'change');
        if (!in_array($type, ['change', 'issue', 'new'], true)) {
            $type = 'change';
        }

        $serviceId = (int) ($post['service_id'] ?? 0);
        if ($serviceId <= 0) {
            $serviceId = null;
        }

        $payload = [
            'city' => trim(strip_tags((string) ($post['city'] ?? ''))),
            'name' => trim(strip_tags((string) ($post['name'] ?? ''))),
            'description' => trim(strip_tags((string) ($post['description'] ?? ''))),
            'phone' => trim(strip_tags((string) ($post['phone'] ?? ''))),
            'email' => trim(strip_tags((string) ($post['email'] ?? ''))),
            'url' => trim(strip_tags((string) ($post['url'] ?? ''))),
            'url_chat' => trim(strip_tags((string) ($post['url_chat'] ?? ''))),
            'office_hours' => trim(strip_tags((string) ($post['office_hours'] ?? ''))),
            'focus' => trim(strip_tags((string) ($post['focus'] ?? ''))),
            'carer_qualification' => trim(strip_tags((string) ($post['carer_qualification'] ?? ''))),
            'district_id' => implode(',', array_map('intval', (array) ($post['district_ids'] ?? (isset($post['district_id']) ? (is_array($post['district_id']) ? $post['district_id'] : [$post['district_id']]) : [])))),
            'group_ids' => array_map('intval', (array) ($post['group_ids'] ?? [])),
            'language_ids' => array_map('intval', (array) ($post['language_ids'] ?? [])),
        ];

        $payload = array_filter($payload, static function ($value): bool {
            if (is_array($value)) {
                return !empty($value);
            }
            if (is_int($value)) {
                return $value > 0;
            }
            return '' !== (string) $value;
        });

        $message = trim(strip_tags((string) ($post['message'] ?? '')));
        $reporterName = trim(strip_tags((string) ($post['reporter_name'] ?? '')));
        $reporterEmail = trim(strip_tags((string) ($post['reporter_email'] ?? '')));

        if ('new' === $type && (!isset($payload['city'], $payload['name'], $payload['description']))) {
            return ['success' => false, 'message' => 'Fuer neue Einrichtungen sind Ort, Name und Beschreibung erforderlich.'];
        }

        if (in_array($type, ['change', 'issue'], true) && null === $serviceId) {
            return ['success' => false, 'message' => 'Bitte waehlen Sie eine bestehende Einrichtung aus.'];
        }

        if ([] === $payload && '' === $message) {
            return ['success' => false, 'message' => 'Bitte beschreiben Sie mindestens eine Aenderung oder ein Problem.'];
        }

        $allTextContent = implode(' ', array_filter($payload, 'is_string')) . ' ' . $message . ' ' . $reporterName;
        if (self::containsSpam($allTextContent)) {
            // Um Spammer nicht dazu zu ermutigen, ihre Taktik sofort anzupassen,
            // geben wir in der Regel eine generische "Erfolg"-Meldung oder Block-Meldung zurück.
            // Aus Nutzersicht fairerweise ein Hinweis:
            return ['success' => false, 'message' => 'Ihre Eingabe wurde als möglicher Spam blockiert. Bitte überprüfen Sie Ihre Eingabewerte.'];
        }

        $now = date('Y-m-d H:i:s');
        $ipHash = self::ipHash();

        $sql = rex_sql::factory();
        $sql->setTable(rex::getTable('oo_submission'));
        $sql->setValue('type', $type);
        $sql->setValue('status', 'new');
        $sql->setValue('service_id', $serviceId);
        $sql->setValue('payload_json', json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $sql->setValue('message', $message);
        $sql->setValue('reporter_name', $reporterName);
        $sql->setValue('reporter_email', $reporterEmail);
        $sql->setValue('ip_hash', $ipHash);
        $sql->setValue('user_agent', substr((string) rex_server('HTTP_USER_AGENT', 'string', ''), 0, 255));
        $sql->setValue('createdate', $now);
        $sql->setValue('updatedate', $now);
        $sql->setValue('createuser', 'frontend');
        $sql->setValue('updateuser', 'frontend');
        $sql->insert();

        $submissionId = (int) $sql->getLastId();
        self::addEvent($submissionId, 'submitted', 'Public submission received', 'frontend');

        return ['success' => true, 'message' => 'Vielen Dank. Die Meldung wurde gespeichert und wird redaktionell geprueft.'];
    }

    public static function transitionStatus(int $submissionId, string $status, string $editorNote = ''): bool
    {
        if (!in_array($status, ['in_review', 'approved', 'rejected'], true)) {
            return false;
        }

        $submission = self::getSubmission($submissionId);
        if (!$submission) {
            return false;
        }

        if ('approved' === $status) {
            if (!self::applySubmission($submission)) {
                return false;
            }
        }

        $editor = rex::getUser() ? rex::getUser()->getLogin() : 'console';
        $now = date('Y-m-d H:i:s');

        $sql = rex_sql::factory();
        $sql->setTable(rex::getTable('oo_submission'));
        $sql->setWhere(['id' => $submissionId]);
        $sql->setValue('status', $status);
        $sql->setValue('editor_login', $editor);
        $sql->setValue('editor_note', $editorNote);
        
        if (in_array($status, ['approved', 'rejected'], true)) {
            $sql->setValue('reporter_name', 'Anonymisiert');
            $sql->setValue('reporter_email', 'anonymisiert');
            $sql->setValue('ip_hash', '');
        }

        $sql->setValue('reviewedate', $now);
        $sql->setValue('updatedate', $now);
        $sql->setValue('updateuser', $editor);
        $sql->update();

        self::addEvent($submissionId, 'status_' . $status, $editorNote, $editor);
        return true;
    }

    /**
     * @return null|array<string, mixed>
     */
    public static function getSubmission(int $id): ?array
    {
        $sql = rex_sql::factory();
        $rows = $sql->getArray('SELECT * FROM ' . rex::getTable('oo_submission') . ' WHERE id = ? LIMIT 1', [$id]);
        return $rows[0] ?? null;
    }

    /**
     * @param array<string, mixed> $submission
     */
    private static function applySubmission(array $submission): bool
    {
        $type = (string) $submission['type'];
        $payload = json_decode((string) $submission['payload_json'], true);
        if (!is_array($payload)) {
            $payload = [];
        }

        if ('issue' === $type) {
            return true;
        }

        if ('change' === $type) {
            $serviceId = (int) ($submission['service_id'] ?? 0);
            $service = rex_offeneohren_portal_service::get($serviceId);
            if (!$service) {
                return false;
            }

            foreach (self::allowedFields() as $field) {
                if (array_key_exists($field, $payload)) {
                    $service->setValue($field, $payload[$field]);
                }
            }

            if (!$service->save()) {
                return false;
            }

            self::markAppliedService((int) $submission['id'], $service->getId());
            return true;
        }

        if ('new' === $type) {
            $service = rex_offeneohren_portal_service::create();
            foreach (self::allowedFields() as $field) {
                if (array_key_exists($field, $payload)) {
                    $service->setValue($field, $payload[$field]);
                }
            }

            if (!$service->hasValue('status')) {
                $service->setValue('status', 1);
            }

            if (!$service->save()) {
                return false;
            }

            self::markAppliedService((int) $submission['id'], $service->getId());
            return true;
        }

        return false;
    }

    private static function markAppliedService(int $submissionId, int $serviceId): void
    {
        $editor = rex::getUser() ? rex::getUser()->getLogin() : 'console';
        $sql = rex_sql::factory();
        $sql->setTable(rex::getTable('oo_submission'));
        $sql->setWhere(['id' => $submissionId]);
        $sql->setValue('applied_service_id', $serviceId);
        $sql->setValue('updatedate', date('Y-m-d H:i:s'));
        $sql->setValue('updateuser', $editor);
        $sql->update();
    }

    private static function addEvent(int $submissionId, string $eventType, string $eventNote, string $actor): void
    {
        $sql = rex_sql::factory();
        $sql->setTable(rex::getTable('oo_submission_event'));
        $sql->setValue('submission_id', $submissionId);
        $sql->setValue('event_type', $eventType);
        $sql->setValue('event_note', $eventNote);
        $sql->setValue('actor_login', $actor);
        $sql->setValue('createdate', date('Y-m-d H:i:s'));
        $sql->insert();
    }

    private static function containsSpam(string $text): bool
    {
        $textLower = mb_strtolower($text, 'UTF-8');
        
        // Block 1: Massive Anzahl an Links (oft bei Spambots üblich)
        if (substr_count($textLower, 'http') > 3) {
            return true;
        }
        
        // Block 2: Kyrillische, arabische oder chinesische Zeichen (typisch für SPAM-Kommentare in DE/DACH)
        // \p{Cyrillic}, \p{Han} (Chinesisch), \p{Arabic}
        if (preg_match('/[\p{Cyrillic}\p{Han}\p{Arabic}]/u', $text)) {
            return true;
        }

        // Block 3: Simple Blacklist mit harten Spam-Keywords
        $badwords = [
            'viagra', 'cialis', 'porn', 'sex', 'casino', 'bitcoin', 'crypto', 
            'seo service', 'seo-agentur', 'gewinnspiel', 'roulette', 'krypto',
            'escort', 'dating', 'milfs', 'nackt', 'fuck'
        ];

        foreach ($badwords as $word) {
            if (str_contains($textLower, $word)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * @param array<string, mixed> $post
     */
    private static function isAntiBotValid(array $post): bool
    {
        $honeypot = trim((string) ($post['website'] ?? ''));
        if ('' !== $honeypot) {
            return false;
        }

        $startedAt = (int) ($post['started_at'] ?? 0);
        if ($startedAt <= 0) {
            return false;
        }

        $delta = time() - $startedAt;
        return $delta >= 3 && $delta <= 7200;
    }

    private static function isRateLimited(): bool
    {
        $ipHash = self::ipHash();
        if ('' === $ipHash) {
            return false;
        }

        $sql = rex_sql::factory();
        $rows = $sql->getArray(
            'SELECT COUNT(*) AS cnt FROM ' . rex::getTable('oo_submission') . ' WHERE ip_hash = ? AND createdate >= DATE_SUB(NOW(), INTERVAL 10 MINUTE)',
            [$ipHash]
        );

        $count = isset($rows[0]['cnt']) ? (int) $rows[0]['cnt'] : 0;
        return $count >= 5;
    }

    private static function ipHash(): string
    {
        $ip = trim((string) rex_server('REMOTE_ADDR', 'string', ''));
        if ('' === $ip) {
            return '';
        }

        return hash('sha256', $ip);
    }

    /**
     * @return string[]
     */
    private static function allowedFields(): array
    {
        return [
            'status',
            'district_id',
            'city',
            'name',
            'description',
            'phone',
            'email',
            'office_hours',
            'focus',
            'carer_qualification',
            'url',
            'url_chat',
        ];
    }
}
