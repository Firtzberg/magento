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
		)
	);

foreach($dataRow as $data){
	$model->setData($data)->setOrigData()->save();
}