<?php

$addon = rex_addon::get('offeneohren_portal');
$csrf = rex_csrf_token::factory('offeneohren_portal_moderation');

if (rex_post('moderate', 'bool')) {
    if (!$csrf->isValid()) {
        echo rex_view::error(rex_i18n::msg('csrf_token_invalid') ?? 'CSRF Token ungültig.');
    } else {
        $submissionId = rex_post('submission_id', 'int', 0);
        $status = rex_post('status', 'string', 'in_review');
        $note = rex_post('editor_note', 'string', '');
        
        $sql = rex_sql::factory();
        
        // Payload-Update falls Daten im Modal angepasst wurden
        $payloadEdit = rex_post('payload_edit', 'array', []);
        if (!empty($payloadEdit)) {
            $sqlCheck = rex_sql::factory();
            $sqlCheck->setQuery('SELECT payload_json FROM ' . rex::getTable('oo_submission') . ' WHERE id = ? LIMIT 1', [$submissionId]);
            if ($sqlCheck->getRows()) {
                $existingPayload = json_decode((string) $sqlCheck->getValue('payload_json'), true);
                if (!is_array($existingPayload)) {
                    $existingPayload = [];
                }
                
                foreach ($payloadEdit as $k => $v) {
                    if (isset($existingPayload[$k]) && is_array($existingPayload[$k])) {
                        // Ursprünglich ein Array (z.B. Multiselect-IDs: "1, 2, 3")
                        $arrVals = array_filter(array_map('trim', explode(',', $v)), function($item) { return $item !== ''; });
                        $existingPayload[$k] = array_map('intval', $arrVals);
                    } else {
                        // Casts beibehalten, wenn möglich
                        if (isset($existingPayload[$k]) && is_int($existingPayload[$k]) && is_numeric($v)) {
                            $existingPayload[$k] = (int)$v;
                        } else {
                            $existingPayload[$k] = $v;
                        }
                    }
                }
                
                $sqlUpdate = rex_sql::factory();
                $sqlUpdate->setTable(rex::getTable('oo_submission'));
                $sqlUpdate->setWhere(['id' => $submissionId]);
                $sqlUpdate->setValue('payload_json', json_encode($existingPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                $sqlUpdate->update();
            }
        }

        if ($submissionId > 0 && rex_offeneohren_portal_submission_service::transitionStatus($submissionId, $status, $note)) {
            echo rex_view::success('Status erfolgreich aktualisiert.');
        } else {
            echo rex_view::error('Status konnte nicht aktualisiert werden.');
        }
    }
}

$statusFilter = rex_get('status', 'string', 'new');
$allowed = ['new', 'in_review', 'approved', 'rejected', 'all'];
if (!in_array($statusFilter, $allowed, true)) {
    $statusFilter = 'new';
}

$statusLabels = [
    'new' => 'Neu',
    'in_review' => 'In Prüfung',
    'approved' => 'Akzeptiert',
    'rejected' => 'Abgelehnt',
    'all' => 'Alle'
];

$typeLabels = [
    'new' => 'Neuer Eintrag',
    'change' => 'Änderungsvorschlag',
    'issue' => 'Problem / Fehler',
];

$where = '';
$params = [];
if ('all' !== $statusFilter) {
    $where = ' WHERE s.status = ?';
    $params[] = $statusFilter;
}

$sql = rex_sql::factory();
$rows = $sql->getArray(
    'SELECT s.*, svc.name AS service_name, svc.id AS sid
     FROM ' . rex::getTable('oo_submission') . ' s
     LEFT JOIN ' . rex::getTable('yf_service') . ' svc ON svc.id = s.service_id
     ' . $where . '
     ORDER BY s.createdate DESC
     LIMIT 500',
    $params
);

$compareFields = [
    'city' => 'Ort / Stadt',
    'name' => 'Name',
    'description' => 'Beschreibung',
    'phone' => 'Telefon',
    'email' => 'E-Mail',
    'office_hours' => 'Sprechzeiten',
    'focus' => 'Schwerpunkt',
    'carer_qualification' => 'Qualifikation',
    'url' => 'Website',
    'url_chat' => 'Chat-URL',
    'district_id' => 'Landkreis',
    'group_ids' => 'Themen',
    'language_ids' => 'Sprachen',
];

$districts = rex_offeneohren_portal_service_finder::districtOptions();
$groups = rex_offeneohren_portal_service_finder::groupOptions();
$languages = rex_offeneohren_portal_service_finder::languageOptions();

$formatValue = static function ($value, $field) use ($districts, $groups, $languages): string {
    if (is_array($value) || $field === 'district_id' || $field === 'group_ids' || $field === 'language_ids') {
        $arr = is_array($value) ? $value : ('' !== (string)$value ? explode(',', (string)$value) : []);
        if ([] === $arr) {
            return '<span class="text-muted">-</span>';
        }
        
        $labels = [];
        foreach ($arr as $id) {
            $id = (int) $id;
            if ($field === 'district_id') {
                $labels[] = $districts[$id] ?? "ID: $id";
            } elseif ($field === 'group_ids') {
                $labels[] = $groups[$id] ?? "ID: $id";
            } elseif ($field === 'language_ids') {
                $labels[] = $languages[$id] ?? "ID: $id";
            } else {
                $labels[] = $id;
            }
        }
        return rex_escape(implode(', ', $labels));
    }
    
    if (null === $value || '' === (string) $value) {
        return '<span class="text-muted">-</span>';
    }
    
    $valueStr = (string)$value;
    $escaped = nl2br(rex_escape($valueStr));
    
    // Für URL und Chat-URL einen klickbaren Link zum Prüfen einbauen
    if ($field === 'url' || $field === 'url_chat') {
        if (preg_match('#^https?://#i', $valueStr)) {
            $escaped .= '<br><a href="' . rex_escape($valueStr) . '" target="_blank" class="btn btn-default btn-xs" style="margin-top: 5px;"><i class="rex-icon rex-icon-open-external"></i> Link prüfen</a>';
        }
    }
    
    return $escaped;
};

$tabs = '';
foreach ($allowed as $statusKey) {
    $url = rex_url::currentBackendPage(['status' => $statusKey]);
    $active = $statusFilter === $statusKey ? ' class="active"' : '';
    $tabs .= '<li' . $active . '><a href="' . $url . '">' . rex_escape($statusLabels[$statusKey]) . '</a></li>';
}

$body = '<ul class="nav nav-tabs" style="margin-bottom:15px;">' . $tabs . '</ul>';

if (!$rows) {
    $body .= '<div class="alert alert-info">Keine Meldungen in diesem Status vorhanden.</div>';
} else {
    // Listendarstellung (Table)
    $body .= '<table class="table table-hover table-striped">';
    $body .= '<thead>';
    $body .= '<tr>';
    $body .= '<th>ID</th>';
    $body .= '<th>Typ</th>';
    $body .= '<th>Einrichtung</th>';
    $body .= '<th>Absender</th>';
    $body .= '<th>Datum / Verlauf</th>';
    $body .= '<th class="rex-table-action">Aktionen</th>';
    $body .= '</tr>';
    $body .= '</thead>';
    $body .= '<tbody>';

    $modals = ''; // Sammelt alle Modals für den Output unten

    foreach ($rows as $row) {
        $sid = (int)$row['id'];
        $payload = json_decode((string) $row['payload_json'], true) ?: [];
        $serviceLabel = $row['sid'] ? ('#' . (int) $row['sid'] . ' ' . (string) ($row['service_name'] ?? '')) : '-';
        $typeLbl = $typeLabels[$row['type']] ?? rex_escape((string) $row['type']);
        $statusLbl = $statusLabels[$row['status']] ?? rex_escape((string) $row['status']);

        $verlaufStr = 'Eingang: ' . rex_formatter::intlDateTime((string) $row['createdate'], [IntlDateFormatter::SHORT, IntlDateFormatter::MEDIUM]);
        if (!empty($row['updateuser']) && $row['updateuser'] !== 'frontend') {
            $verlaufStr .= '<br><small class="text-muted">Bearbeitet: ' . rex_formatter::intlDateTime((string) $row['updatedate'], [IntlDateFormatter::SHORT, IntlDateFormatter::MEDIUM]) . ' von <strong>' . rex_escape((string)$row['updateuser']) . '</strong></small>';
        }

        // Row in der Liste
        $rowHtml = '<tr>';
        $rowHtml .= '<td>' . $sid . '</td>';
        $rowHtml .= '<td><span class="label label-default">' . $typeLbl . '</span></td>';
        $rowHtml .= '<td>' . rex_escape($serviceLabel) . '</td>';
        $rowHtml .= '<td>' . rex_escape($row['reporter_name']) . '</td>';
        $rowHtml .= '<td>' . $verlaufStr . '</td>';
        $rowHtml .= '<td class="rex-table-action"><button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#oo-modal-' . $sid . '"><i class="rex-icon rex-icon-view"></i> Prüfen / Details</button></td>';
        $rowHtml .= '</tr>';

        // Begründung in Liste einfügen, falls vorhanden (insb. bei rejected)
        if ($row['editor_note']) {
            $rowHtml .= '<tr class="active"><td colspan="6"><small class="text-danger"><strong>Letzte Notiz / Begründung:</strong> ' . rex_escape($row['editor_note']) . '</small></td></tr>';
        }
        $body .= $rowHtml;

        // Modal für diesen Eintrag aufbauen
        $serviceEditUrl = '';
        $currentData = [];

        if ((int) $row['sid'] > 0) {
            $_csrf_key = 'table_field-rex_yf_service';
            $_csrf_params = rex_csrf_token::factory($_csrf_key)->getUrlParams();
            $serviceEditUrl = rex_url::backendPage('yform/manager/data_edit', array_merge([
                'table_name' => 'rex_yf_service',
                'data_id' => (int) $row['sid'],
                'func' => 'edit',
            ], $_csrf_params), false);
            $current = rex_offeneohren_portal_service::get((int) $row['sid']);
            if ($current) {
                $currentData = $current->getData();
            }
        }

        $compareRows = '';
        foreach ($compareFields as $field => $fieldLabel) {
            $old = $currentData[$field] ?? null;
            $new = $payload[$field] ?? null;
            $hasNewValue = array_key_exists($field, $payload);

            $oldStr = is_array($old) ? implode(',', $old) : (string) $old;
            $newStr = is_array($new) ? implode(',', $new) : (string) $new;

            // Keine Skip-Logik mehr: Wir zeigen IMMER alle im $compareFields definierten Felder an, 
            // damit der Redakteur bei jedem Datensatz ein vollständiges Bild der Live-Daten hat.

            $changed = $hasNewValue && ($oldStr !== $newStr);
            $newMarkup = $hasNewValue ? $formatValue($new, $field) : '<span class="text-muted">(keine Änderung)</span>';
            $tdNewStyle = $changed ? ' style="width:40%; background-color:#eef9f1; color:#0f5132;"' : ' style="width:40%;"';

            $compareRows .= '<tr>';
            $compareRows .= '<th style="width:20%;">' . rex_escape($fieldLabel) . '</th>';
            $compareRows .= '<td style="width:40%;">' . $formatValue($old, $field) . '</td>';
            $compareRows .= '<td' . $tdNewStyle . '>' . $newMarkup . '</td>';
            $compareRows .= '</tr>';
        }

        $compareTable = '';
        if ('' !== $compareRows) {
            $compareTable .= '<p><strong>Vergleich (Live-Daten vs. Vorschlag)</strong></p>';
            $compareTable .= '<div class="table-responsive"><table class="table table-bordered table-condensed">';
            $compareTable .= '<thead><tr><th>Feld</th><th>Live-Daten</th><th>Vorschlag</th></tr></thead><tbody>' . $compareRows . '</tbody></table></div>';
        }

        $payloadJsonString = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $modalHtml = '
        <div class="modal fade" id="oo-modal-' . $sid . '" tabindex="-1" role="dialog" aria-labelledby="oo-modal-title-' . $sid . '">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form method="post">
                        ' . $csrf->getHiddenField() . '
                        <input type="hidden" name="moderate" value="1">
                        <input type="hidden" name="submission_id" value="' . $sid . '">
                        
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Schließen"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="oo-modal-title-' . $sid . '">Submission #' . $sid . ' - ' . $typeLbl . ' <span class="label label-default">' . $statusLbl . '</span></h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Einrichtung:</strong> ' . rex_escape($serviceLabel) . '</p>
                                    ' . ($serviceEditUrl ? '<p><a class="btn btn-default btn-xs" target="_blank" href="' . rex_escape($serviceEditUrl) . '">Original-Datensatz im YForm Manager öffnen <i class="rex-icon rex-icon-open-external"></i></a></p>' : '') . '
                                </div>
                                <div class="col-md-6 text-right">
                                    <p><strong>Absender:</strong> ' . rex_escape((string) $row['reporter_name']) . '<br><a href="mailto:' . rex_escape((string) $row['reporter_email']) . '">' . rex_escape((string) $row['reporter_email']) . '</a></p>
                                    <p>
                                        <strong>Eingang:</strong> ' . rex_formatter::intlDateTime((string) $row['createdate'], [IntlDateFormatter::SHORT, IntlDateFormatter::MEDIUM]) . '
                                        ' . (!empty($row['updateuser']) && $row['updateuser'] !== 'frontend' ? '<br><strong>Bearbeitet:</strong> ' . rex_formatter::intlDateTime((string) $row['updatedate'], [IntlDateFormatter::SHORT, IntlDateFormatter::MEDIUM]) . ' von <strong>' . rex_escape((string)$row['updateuser']) . '</strong>' : '') . '
                                    </p>
                                </div>
                            </div>
                            <hr>
                            <div class="alert alert-warning">
                                <strong>Nachricht des Absenders:</strong><br>' . nl2br(rex_escape((string) $row['message'])) . '
                            </div>
                            
                            ' . $compareTable . '
                            
                            <hr>
                            <div class="panel panel-default" style="margin-bottom: 0;">
                                <div class="panel-heading"><strong class="panel-title">Daten-Vorschlag bearbeiten</strong> <small class="text-muted">(z.B. um Tippfehler vor Übernahme zu korrigieren)</small></div>
                                <div class="panel-body">
                                    <div class="row">';
        
        $editFieldsHtml = '';
        $relationFields = ['district_id', 'group_ids', 'language_ids'];
        
        foreach ($payload as $key => $val) {
            // Relationen im Formular überspringen
            if (in_array($key, $relationFields, true)) {
                continue;
            }

            $fieldName = $compareFields[$key] ?? current(explode('_', ucfirst($key)));
            $isArr = is_array($val);
            $valStr = $isArr ? implode(', ', $val) : (string) $val;
            
            $editFieldsHtml .= '<div class="col-md-6"><div class="form-group">';
            $editFieldsHtml .= '<label><strong>' . rex_escape($fieldName) . '</strong> <small class="text-muted">(' . rex_escape($key) . ')</small></label>';
            
            if (strpos($valStr, "\n") !== false || $key === 'description' || $key === 'office_hours' || $key === 'focus' || strlen($valStr) > 40) {
                $editFieldsHtml .= '<textarea class="form-control" name="payload_edit[' . rex_escape($key) . ']" rows="3">' . rex_escape($valStr) . '</textarea>';
            } else {
                $editFieldsHtml .= '<input type="text" class="form-control" name="payload_edit[' . rex_escape($key) . ']" value="' . rex_escape($valStr) . '" />';
            }
            $editFieldsHtml .= '</div></div>';
        }
        
        $relationHint = '<div class="col-md-12"><div class="alert alert-info" style="padding:10px; margin-bottom:15px;"><i class="rex-icon rex-icon-info"></i> <strong>Tipp zu Zuweisungen:</strong> Verknüpfungen (Landkreis, Sprachen, Themen) lassen sich hier aus technischen Gründen nicht direkt bearbeiten. Bitte prüfen und korrigieren Sie diese bei Bedarf nach der Übernahme direkt über den YForm-Manager.</div></div>';

        if ($editFieldsHtml === '') {
            $editFieldsHtml = $relationHint . '<div class="col-md-12"><p class="text-muted" style="margin:0;">Keine bearbeitbaren reinen Textfelder im Vorschlag vorhanden.</p></div>';
        } else {
            $editFieldsHtml = $relationHint . $editFieldsHtml;
        }
        
        $modalHtml .= $editFieldsHtml . '
                                    </div>
                                </div>
                            </div>
                            
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status_' . $sid . '">Entscheidung / Neuer Status:</label>
                                        <select class="form-control" id="status_' . $sid . '" name="status">';
        
        foreach (['in_review' => 'In Prüfung', 'approved' => 'Akzeptieren & Übernehmen', 'rejected' => 'Ablehnen'] as $sVal => $sLbl) {
            $sel = $row['status'] === $sVal ? ' selected' : '';
            $modalHtml .= '<option value="' . rex_escape($sVal) . '"' . $sel . '>' . rex_escape($sLbl) . '</option>';
        }
        
        $modalHtml .= '         </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="editor_note_' . $sid . '">Begründung / Interne Notiz:</label>
                                        <input class="form-control" type="text" id="editor_note_' . $sid . '" name="editor_note" value="' . rex_escape((string)$row['editor_note']) . '" placeholder="Wird z.B. bei Ablehnung angezeigt">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Abbrechen</button>
                            <button type="submit" class="btn btn-primary">Speichern & ausführen</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>';

        $modals .= $modalHtml;
    }

    $body .= '</tbody></table>';
    
    // Modals ganz unten im DOM, damit sie das Table-Layout nicht stören
    $body .= $modals;
}

$fragment = new rex_fragment();
$fragment->setVar('title', 'Moderation', false);
$fragment->setVar('body', $body, false);
echo $fragment->parse('core/page/section.php');
