<?php
$mform = new \FriendsOfRedaxo\MForm();
$mform->addFieldsetArea('Alternative Portale');
$mform->addHtml('<p>Erzeugt eine Linkliste der alternativen Portale aus der Tabelle ' . rex::getTable('yf_alternate') . '.</p>');
echo $mform->show();
?>