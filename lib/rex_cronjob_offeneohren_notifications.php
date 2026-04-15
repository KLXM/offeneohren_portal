<?php

class rex_cronjob_offeneohren_notifications extends rex_cronjob
{
    public function execute()
    {
        $lastRun = rex_config::get('offeneohren_portal', 'last_notification_run');
        // Falls noch nie gelaufen, nehmen wir die letzten 24 Stunden als initialen Fallback
        if (!$lastRun) {
            $lastRun = date('Y-m-d H:i:s', strtotime('-24 hours'));
        }

        $now = date('Y-m-d H:i:s');
        $sql = rex_sql::factory();

        // Alle neuen / geänderten / genehmigten / abgelehnten Submissions abrufen
        $query = 'SELECT * FROM ' . rex::getTable('oo_submission') . ' 
                  WHERE (createdate >= ? AND status = "in_review") 
                  OR (updatedate >= ? AND status IN ("approved", "rejected"))';
        
        $submissions = $sql->getArray($query, [$lastRun, $lastRun]);

        if (count($submissions) === 0) {
            // Nichts zu tun
            rex_config::set('offeneohren_portal', 'last_notification_run', $now);
            $this->setMessage('Keine neuen Submissions oder Änderungen gefunden.');
            return true;
        }

        // Benutzer-Präferenzen holen (nur aktive Redakteure mit gesetzter E-Mail)
        $userSql = rex_sql::factory();
        $users = $userSql->getArray('
            SELECT u.email, u.name, p.notify_new, p.notify_change, p.notify_approved, p.notify_rejected
            FROM ' . rex::getTable('user') . ' u
            JOIN ' . rex::getTable('oo_notification_preferences') . ' p ON u.id = p.user_id
            WHERE u.status = 1 AND u.email != ""
        ');

        if (count($users) === 0) {
            rex_config::set('offeneohren_portal', 'last_notification_run', $now);
            $this->setMessage('Keine Empfänger mit aktiven Benachrichtigungs-Einstellungen gefunden.');
            return true;
        }

        $mailsSent = 0;

        foreach ($users as $user) {
            $htmlBlocks = [];
            
            // Neu-Eingänge (Neu)
            $news = array_filter($submissions, fn($s) => $s['status'] === 'in_review' && $s['type'] === 'new' && $s['createdate'] >= $lastRun);
            if (!empty($news) && (bool) $user['notify_new']) {
                $html = '<h2>Neue Einrichtungsvorschläge</h2>';
                foreach ($news as $n) {
                    $date = rex_formatter::intlDateTime($n['createdate']);
                    $html .= '<div class="item-card">';
                    $html .= '<h3><span class="badge badge-new">Neu</span> ' . rex_escape($n['reporter_name']) . '</h3>';
                    $html .= '<p>Eingereicht am: <strong>' . $date . '</strong><br>Email: ' . rex_escape($n['reporter_email']) . '</p>';
                    $html .= '</div>';
                }
                $htmlBlocks[] = $html;
            }

            // Neu-Eingänge (Änderung)
            $changes = array_filter($submissions, fn($s) => $s['status'] === 'in_review' && $s['type'] === 'change' && $s['createdate'] >= $lastRun);
            if (!empty($changes) && (bool) $user['notify_change']) {
                $html = '<h2>Neue Änderungsvorschläge</h2>';
                foreach ($changes as $c) {
                    $date = rex_formatter::intlDateTime($c['createdate']);
                    $html .= '<div class="item-card">';
                    $html .= '<h3><span class="badge badge-change">Änderung</span> Service-ID: ' . $c['service_id'] . '</h3>';
                    $html .= '<p>Eingereicht von: <strong>' . rex_escape($c['reporter_name']) . '</strong> am ' . $date . '</p>';
                    $html .= '</div>';
                }
                $htmlBlocks[] = $html;
            }

            // Genehmigt
            $approved = array_filter($submissions, fn($s) => $s['status'] === 'approved' && $s['updatedate'] >= $lastRun);
            if (!empty($approved) && (bool) $user['notify_approved']) {
                $html = '<h2>Freigegebene Vorschläge (Approved)</h2>';
                foreach ($approved as $a) {
                    $date = rex_formatter::intlDateTime($a['updatedate']);
                    $html .= '<div class="item-card">';
                    $html .= '<h3><span class="badge badge-approved">Freigegeben</span> ' . rex_escape($a['reporter_name']) . '</h3>';
                    $html .= '<p>Bearbeitet durch: <strong>' . rex_escape($a['updateuser'] ?? 'System') . '</strong> am ' . $date . '</p>';
                    $html .= '</div>';
                }
                $htmlBlocks[] = $html;
            }

            // Abgelehnt
            $rejected = array_filter($submissions, fn($s) => $s['status'] === 'rejected' && $s['updatedate'] >= $lastRun);
            if (!empty($rejected) && (bool) $user['notify_rejected']) {
                $html = '<h2>Abgelehnte Vorschläge (Rejected)</h2>';
                foreach ($rejected as $r) {
                    $date = rex_formatter::intlDateTime($r['updatedate']);
                    $html .= '<div class="item-card">';
                    $html .= '<h3><span class="badge badge-rejected">Abgelehnt</span> ' . rex_escape($r['reporter_name']) . '</h3>';
                    $html .= '<p>Bearbeitet durch: <strong>' . rex_escape($r['updateuser'] ?? 'System') . '</strong> am ' . $date . '</p>';
                    $html .= '</div>';
                }
                $htmlBlocks[] = $html;
            }

            if (!empty($htmlBlocks)) {
                $content = implode('<hr>', $htmlBlocks);
                $content .= '<p style="text-align:center;margin-top:30px;"><a href="' . rex::getServer() . 'redaxo/index.php?page=offeneohren_portal/moderation" style="border-radius:4px;padding:10px 20px;background:#2933F0;color:#fff;text-decoration:none;display:inline-block;font-weight:bold;">Zum Moderations-Backend</a></p>';
                
                $subject = rex::getServerName() . ' - Neue Redaktions-Aktivitäten (' . count($submissions) . ' Einträge)';
                
                if (rex_offeneohren_portal_notification_service::sendDigest($user['email'], $content, $subject)) {
                    $mailsSent++;
                }
            }
        }

        rex_config::set('offeneohren_portal', 'last_notification_run', $now);
        $this->setMessage($mailsSent . ' Benachrichtigungs-Details per E-Mail versendet.');
        
        return true;
    }

    public function getTypeName()
    {
        return 'Offene Ohren Portal - Redaktions-Benachrichtigungen (Digest)';
    }
}