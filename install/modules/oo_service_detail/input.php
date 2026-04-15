<?php
use FriendsOfRedaxo\MForm;

$mform = new MForm();
$mform->addFieldsetArea('Detailansicht Einstellungen');
$mform->addLinkField(1, ['label' => 'Übersichtsseite (Zurück-Link)']);
echo $mform->show();
