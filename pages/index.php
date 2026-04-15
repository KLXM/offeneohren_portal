<?php

$addon = rex_addon::get('offeneohren_portal');
echo rex_view::title($addon->i18n('title'));
rex_be_controller::includeCurrentPageSubPath();
