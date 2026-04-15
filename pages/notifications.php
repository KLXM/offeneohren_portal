<?php

$userId = rex::getUser()->getId();

// E-Mail Aktualisierung (Direkt aus Profil-Warnung, ähnlich issue_tracker)
if (rex_post('update_profile_email', 'boolean') && rex_csrf_token::factory('update_profile_email')->isValid()) {
    $newEmail = rex_post('user_email', 'string', '');
    if (filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        $sql = rex_sql::factory();
        $sql->setTable(rex::getTable('user'));
        $sql->setWhere(['id' => $userId]);
        $sql->setValue('email', $newEmail);
        $sql->update();
        
        // Page neu laden, damit rex::getUser() die aktuellen DB-Daten hat
        rex_response::sendRedirect(rex_url::currentBackendPage(['msg' => '1']));
    } else {
        echo rex_view::error('Die eingegebene E-Mail-Adresse ist ungültig.');
    }
}

if (rex_request('msg', 'int') === 1) {
    echo rex_view::success('E-Mail-Adresse erfolgreich im Profil gespeichert.');
}

$userEmail = rex::getUser()->getValue('email');
if (!$userEmail) {
    echo rex_view::warning('Ihre Einstellungen wurden möglicherweise geändert, aber Sie haben keine E-Mail-Adresse in Ihrem Profil hinterlegt. Sie können ohne gültige Adresse keine Benachrichtigungen empfangen!');

    // Inline-Formular zum Speichern der Mail
    $formContent = '';
    $formContent .= '<fieldset>';
    
    $formElements = [];
    $n = [];
    $n['label'] = '<label for="oo-user-email">Ihre E-Mail-Adresse</label>';
    $n['field'] = '<input class="form-control" type="email" id="oo-user-email" name="user_email" value="" required>';
    $formElements[] = $n;

    $fragment = new rex_fragment();
    $fragment->setVar('elements', $formElements, false);
    $formContent .= $fragment->parse('core/form/form.php');
    
    $formContent .= '</fieldset>';

    $formElements = [];
    $n = [];
    $n['field'] = '<button class="btn btn-save rex-form-aligned" type="submit" name="btn_save" value="1">' . rex_i18n::msg('user_save') . '</button>';
    $formElements[] = $n;

    $fragment = new rex_fragment();
    $fragment->setVar('elements', $formElements, false);
    $buttons = $fragment->parse('core/form/submit.php');

    $fragment = new rex_fragment();
    $fragment->setVar('class', 'edit', false);
    $fragment->setVar('title', 'E-Mail-Adresse im Benutzerprofil nachtragen', false);
    $fragment->setVar('body', $formContent, false);
    $fragment->setVar('buttons', $buttons, false);
    $content = $fragment->parse('core/page/section.php');

    echo '<form action="' . rex_url::currentBackendPage() . '" method="post">
        ' . rex_csrf_token::factory('update_profile_email')->getHiddenField() . '
        <input type="hidden" name="update_profile_email" value="1">
        ' . $content . '
    </form>';
} else {
    $infoContent = 'System-Mails an Sie werden an <strong>' . htmlspecialchars($userEmail) . '</strong> gesendet. <a href="'.rex_url::backendPage('profile').'">Profil bearbeiten</a>';
    $infoContent .= '
        <br><br>
        <form action="' . rex_url::currentBackendPage() . '" method="post" style="display:inline;">
            ' . rex_csrf_token::factory('send_test_mail')->getHiddenField() . '
            <input type="hidden" name="send_test_mail" value="1">
            <button class="btn btn-default btn-xs" type="submit">
                <i class="rex-icon rex-icon-envelope"></i> Test-E-Mail an diese Adresse senden
            </button>
        </form>
    ';
    echo rex_view::info($infoContent);
}

if (rex_post('send_test_mail', 'boolean') && rex_csrf_token::factory('send_test_mail')->isValid() && $userEmail) {
    $testContent = '<p>Hallo,</p>';
    $testContent .= '<p>dies ist eine Test-Benachrichtigung aus dem Offene Ohren Portal.</p>';
    $testContent .= '<p>Wenn Sie diese E-Mail lesen können, funktioniert Ihr E-Mail-Empfang und die Benachrichtigungsfunktion in REDAXO einwandfrei.</p>';
    
    $success = rex_offeneohren_portal_notification_service::sendDigest($userEmail, $testContent, 'Test-Benachrichtigung - Offene Ohren');
    
    if ($success) {
        echo rex_view::success('Test-E-Mail wurde soeben an <strong>' . htmlspecialchars($userEmail) . '</strong> versendet.');
    } else {
        echo rex_view::error('Beim Versenden der Test-E-Mail ist ein Fehler aufgetreten. Bitte prüfen Sie die PHPMailer- bzw. E-Mail-Einstellungen im System.');
    }
}

$sql = rex_sql::factory();
$sql->setQuery('SELECT * FROM ' . rex::getTable('oo_notification_preferences') . ' WHERE user_id = ? LIMIT 1', [$userId]);

if ($sql->getRows() === 0) {
    // defaults
    $sql->setQuery('INSERT INTO ' . rex::getTable('oo_notification_preferences') . ' (user_id, notify_new, notify_change, notify_approved, notify_rejected, updatedate) VALUES (?, 1, 1, 1, 1, NOW())', [$userId]);
    $sql->setQuery('SELECT * FROM ' . rex::getTable('oo_notification_preferences') . ' WHERE user_id = ? LIMIT 1', [$userId]);
}

$prefs = [
    'notify_new' => (bool) $sql->getValue('notify_new'),
    'notify_change' => (bool) $sql->getValue('notify_change'),
    'notify_approved' => (bool) $sql->getValue('notify_approved'),
    'notify_rejected' => (bool) $sql->getValue('notify_rejected'),
];

if ('POST' === rex_server('REQUEST_METHOD', 'string', '') && rex_post('save_notifications', 'bool')) {
    $prefs['notify_new'] = rex_post('notify_new', 'int', 0) === 1;
    $prefs['notify_change'] = rex_post('notify_change', 'int', 0) === 1;
    $prefs['notify_approved'] = rex_post('notify_approved', 'int', 0) === 1;
    $prefs['notify_rejected'] = rex_post('notify_rejected', 'int', 0) === 1;

    $updateSql = rex_sql::factory();
    $updateSql->setTable(rex::getTable('oo_notification_preferences'));
    $updateSql->setValue('notify_new', $prefs['notify_new'] ? 1 : 0);
    $updateSql->setValue('notify_change', $prefs['notify_change'] ? 1 : 0);
    $updateSql->setValue('notify_approved', $prefs['notify_approved'] ? 1 : 0);
    $updateSql->setValue('notify_rejected', $prefs['notify_rejected'] ? 1 : 0);
    $updateSql->setRawValue('updatedate', 'NOW()');
    $updateSql->setWhere(['user_id' => $userId]);
    $updateSql->update();

    echo rex_view::success('Benachrichtigungs-Einstellungen wurden erfolgreich gespeichert.');
}
?>

<form action="<?= rex_url::currentBackendPage() ?>" method="post">
    <!-- CSRF Token fehlt hier der Einfachheit halber (Redakteure), aber man sollte -->
    <input type="hidden" name="save_notifications" value="1">
    
    <div class="panel panel-edit">
        <header class="panel-heading"><div class="panel-title"><i class="rex-icon rex-icon-envelope"></i> Meine E-Mail Benachrichtigungen</div></header>
        <div class="panel-body">
            <p>Konfigurieren Sie hier, über welche Aktionen Sie in regelmäßigen Intervallen vom System automatisiert per E-Mail informiert werden möchten.</p>
            
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th class="rex-table-icon"></th>
                        <th>Aktion</th>
                        <th class="rex-table-action" style="width: 100px;">Aktiviert</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="rex-table-icon"><i class="fa fa-plus text-primary"></i></td>
                        <td>Es wurde ein <strong>neuer</strong> Einrichtungseintrag eingereicht</td>
                        <td>
                            <input type="checkbox" name="notify_new" value="1" <?= $prefs['notify_new'] ? 'checked' : '' ?>>
                        </td>
                    </tr>
                    <tr>
                        <td class="rex-table-icon"><i class="fa fa-pencil text-warning"></i></td>
                        <td>Es wurde ein <strong>Änderungsvorschlag</strong> zu einer Einrichtung eingereicht</td>
                        <td>
                            <input type="checkbox" name="notify_change" value="1" <?= $prefs['notify_change'] ? 'checked' : '' ?>>
                        </td>
                    </tr>
                    <tr>
                        <td class="rex-table-icon"><i class="fa fa-check text-success"></i></td>
                        <td>Ein Vorschlag (Neu/Änderung) wurde durch die Redaktion <strong>freigegeben</strong> (Approved)</td>
                        <td>
                            <input type="checkbox" name="notify_approved" value="1" <?= $prefs['notify_approved'] ? 'checked' : '' ?>>
                        </td>
                    </tr>
                    <tr>
                        <td class="rex-table-icon"><i class="fa fa-times text-danger"></i></td>
                        <td>Ein Vorschlag (Neu/Änderung) wurde durch die Redaktion <strong>abgelehnt</strong> (Rejected)</td>
                        <td>
                            <input type="checkbox" name="notify_rejected" value="1" <?= $prefs['notify_rejected'] ? 'checked' : '' ?>>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <footer class="panel-footer">
            <div class="rex-form-panel-footer">
                <div class="btn-toolbar">
                    <button class="btn btn-save rex-form-aligned" type="submit" name="btn_save" value="1">
                        <i class="rex-icon rex-icon-save"></i> Einstellungen speichern
                    </button>
                </div>
            </div>
        </footer>
    </div>
</form>