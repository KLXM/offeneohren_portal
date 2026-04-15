<?php

$addon = rex_addon::get('offeneohren_portal');

if (rex_addon::get('cronjob')->isAvailable()) {
    rex_cronjob_manager::registerType(rex_cronjob_offeneohren_notifications::class);
}

if (rex_addon::get('yform')->isAvailable()) {
    rex_yform_manager_dataset::setModelClass('rex_yf_service', rex_offeneohren_portal_service::class);
    rex_yform_manager_dataset::setModelClass('rex_yf_group', rex_offeneohren_portal_group::class);
    rex_yform_manager_dataset::setModelClass('rex_yf_language', rex_offeneohren_portal_language::class);
    rex_yform_manager_dataset::setModelClass('rex_yf_district', rex_offeneohren_portal_district::class);
}

if (rex::isBackend()) {
    // Addon permission registration
    rex_perm::register('offeneohren_portal[]');

    rex_extension::register('PACKAGES_INCLUDED', static function (): void {
        // Register element path so setup-installed YFCB elements are discoverable in merge mode.
        rex_extension::register('YFORM_CONTENT_BUILDER_ELEMENT_PATHS', static function (rex_extension_point $ep): array {
            $paths = $ep->getSubject();
            $paths[] = rex_path::addon('offeneohren_portal', 'install/yfcb_elements/');
            return $paths;
        });

        rex_extension::register('YFORM_CONTENT_BUILDER_ELEMENT_MODE', static function (): string {
            return 'merge';
        });
    });
}
