<?php

$installer = $this;

$model = Mage::getModel('inchoo_ticketmanager/reply');

$dataRows = array(
    array(
        'ticket_id' => 1,
        'message' => 'Odgovor na prvu poruku',
    ),
    array(
        'ticket_id' => 2,
        'message' => 'Odgovor na drugu poruku.',
    ),
    array(
        'ticket_id' => 1,
        'message' => 'Dodatni odgovor na prvu.',
    )
);
foreach($dataRows as $data){
    $model->unsetData();
    $model->setData($data);
    $model->save();
}