<?php
$mform = new \FriendsOfRedaxo\MForm();
$mform->addFieldsetArea('A-Z Angebotsliste');
$mform->addHtml('<p>Dieses Modul listet alle aktiven Angebote alphabetisch gruppiert auf.</p>');
echo $mform->show();
?>