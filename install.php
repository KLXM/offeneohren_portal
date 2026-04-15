<?php

/** @var rex_addon $this */

if (!$this->hasConfig()) {
    $this->setConfig([
        'template_key' => 'oo_portal_main',
        'base_template_key' => 'uikit_default',
        'framework' => 'uikit',
    ]);
}

require_once __DIR__ . '/lib/rex_offeneohren_portal_setup_service.php';
require_once __DIR__ . '/lib/rex_offeneohren_portal_workflow_installer.php';
rex_offeneohren_portal_setup_service::syncAll();
rex_offeneohren_portal_workflow_installer::install();

// Benachrichtigungs-Einstellungen der Redakteure
rex_sql_table::get(rex::getTable('oo_notification_preferences'))
    ->ensurePrimaryIdColumn()
    ->ensureColumn(new rex_sql_column('user_id', 'int(11)', false, 0))
    ->ensureColumn(new rex_sql_column('notify_new', 'tinyint(1)', false, 1))
    ->ensureColumn(new rex_sql_column('notify_change', 'tinyint(1)', false, 1))
    ->ensureColumn(new rex_sql_column('notify_approved', 'tinyint(1)', false, 1))
    ->ensureColumn(new rex_sql_column('notify_rejected', 'tinyint(1)', false, 1))
    ->ensureColumn(new rex_sql_column('updatedate', 'datetime'))
    ->ensure();
