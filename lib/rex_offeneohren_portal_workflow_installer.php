<?php

class rex_offeneohren_portal_workflow_installer
{
    public static function install(): void
    {
        $sql = rex_sql::factory();

        $sql->setQuery(
            'CREATE TABLE IF NOT EXISTS ' . rex::getTable('oo_submission') . ' (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                type VARCHAR(20) NOT NULL,
                status VARCHAR(20) NOT NULL,
                service_id INT UNSIGNED NULL,
                payload_json MEDIUMTEXT NOT NULL,
                message TEXT NULL,
                reporter_name VARCHAR(191) NULL,
                reporter_email VARCHAR(191) NULL,
                ip_hash VARCHAR(64) NULL,
                user_agent VARCHAR(255) NULL,
                editor_login VARCHAR(191) NULL,
                editor_note TEXT NULL,
                applied_service_id INT UNSIGNED NULL,
                reviewedate DATETIME NULL,
                createdate DATETIME NOT NULL,
                updatedate DATETIME NOT NULL,
                createuser VARCHAR(191) NOT NULL,
                updateuser VARCHAR(191) NOT NULL,
                KEY status_createdate (status, createdate),
                KEY service_id (service_id),
                KEY ip_hash_createdate (ip_hash, createdate)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
        );

        $sql->setQuery(
            'CREATE TABLE IF NOT EXISTS ' . rex::getTable('oo_submission_event') . ' (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                submission_id INT UNSIGNED NOT NULL,
                event_type VARCHAR(40) NOT NULL,
                event_note TEXT NULL,
                actor_login VARCHAR(191) NULL,
                createdate DATETIME NOT NULL,
                KEY submission_event (submission_id, createdate)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
        );
    }
}
