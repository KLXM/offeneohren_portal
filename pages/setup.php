<?php

$addon = rex_addon::get('offeneohren_portal');
$csrf = rex_csrf_token::factory('offeneohren_portal_setup');

$report = [];

if (rex_post('sync', 'bool')) {
    if (!$csrf->isValid()) {
        echo rex_view::error(rex_i18n::msg('csrf_token_invalid'));
    } else {
        $action = rex_post('action', 'string', 'all');

        switch ($action) {
            case 'templates':
                $report = rex_offeneohren_portal_setup_service::syncTemplates();
                break;
            case 'modules':
                $report = rex_offeneohren_portal_setup_service::syncModules();
                break;
            case 'all':
            default:
                $report = rex_offeneohren_portal_setup_service::syncAll();
                break;
        }
    }
}

// 1. Konfiguration Formular
// ----------------------------------------------------------------------------
$form = rex_config_form::factory('offeneohren_portal');

$field = $form->addLinkmapField('change_article_id');
$field->setLabel('Artikel-ID für Formular "Änderung vorschlagen"');

$field = $form->addLinkmapField('new_article_id');
$field->setLabel('Artikel-ID für Formular "Neuer Eintrag"');

$fragment = new rex_fragment();
$fragment->setVar('class', 'edit', false);
$fragment->setVar('title', 'Konfiguration', false);
$fragment->setVar('body', $form->get(), false);
echo $fragment->parse('core/page/section.php');


// 2. Setup und Synchronisation
// ----------------------------------------------------------------------------
$html = '';
$html .= '<p>' . rex_escape($addon->i18n('setup_intro')) . '</p>';
$html .= '<p>' . rex_escape($addon->i18n('setup_warning')) . '</p>';
$html .= '<p>' . rex_escape($addon->i18n('setup_yfcb_note')) . '</p>';

$html .= '<form method="post" action="'.rex_url::currentBackendPage().'" style="margin-bottom: 24px;">';
$html .= $csrf->getHiddenField();
$html .= '<input type="hidden" name="sync" value="1">';
$html .= '<div class="btn-toolbar">';
$html .= '<button class="btn btn-primary" type="submit" name="action" value="all">' . rex_escape($addon->i18n('setup_action_sync_all')) . '</button> ';
$html .= '<button class="btn btn-default" type="submit" name="action" value="templates">' . rex_escape($addon->i18n('setup_action_sync_templates')) . '</button> ';
$html .= '<button class="btn btn-default" type="submit" name="action" value="modules">' . rex_escape($addon->i18n('setup_action_sync_modules')) . '</button> ';
$html .= '</div>';
$html .= '</form>';

if ($report) {
    $html .= '<h4>' . rex_escape($addon->i18n('setup_report')) . '</h4>';
    $html .= '<table class="table table-striped">';
    $html .= '<thead><tr><th>Type</th><th>Key</th><th>Status</th><th>Message</th></tr></thead><tbody>';

    foreach ($report as $line) {
        $label = 'label-default';
        if ('success' === $line['status']) {
            $label = 'label-success';
        } elseif ('warning' === $line['status']) {
            $label = 'label-warning';
        } elseif ('error' === $line['status']) {
            $label = 'label-danger';
        }

        $html .= '<tr>';
        $html .= '<td>' . rex_escape($line['type']) . '</td>';
        $html .= '<td>' . rex_escape($line['key']) . '</td>';
        $html .= '<td><span class="label ' . $label . '">' . rex_escape($line['status']) . '</span></td>';
        $html .= '<td>' . rex_escape($line['message']) . '</td>';
        $html .= '</tr>';
    }

    $html .= '</tbody></table>';
}

$fragment = new rex_fragment();
$fragment->setVar('title', $addon->i18n('setup_title'), false);
$fragment->setVar('body', $html, false);
echo $fragment->parse('core/page/section.php');
