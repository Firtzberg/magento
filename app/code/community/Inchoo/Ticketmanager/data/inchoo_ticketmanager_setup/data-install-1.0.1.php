<?php
$installer = $this;

$model = Mage::getModel('inchoo_ticketmanager/ticket');

$dataRows = array(
	array(
		'subject' => 'Primjer naslova',
		'message' => 'Sadržaj tiketa koji je na hrvatskom jeziku.',
		),
	array(
		'subject' => 'Subject example',
		'message' => 'Content of the english ticket.',
		),
	);
foreach($dataRows as $data){
    $model->unsetData();
    $model->setData($data);
    $model->save();
}