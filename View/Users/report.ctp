<?php

$this->PhpExcel->createWorksheet();
$this->PhpExcel->setDefaultFont('Calibri', 12);

// define table cells
$table = array(
    array('label' => __('Event Name'), 'width' => 'auto', 'filter' => true),
    array('label' => __('Event Description'), 'width' => 'auto', 'filter' => true),
    array('label' => __('Organization'), 'width' => 'auto', 'filter' => true),
    array('label' => __('Event Start'), 'width' => 'auto', 'filter' => true),
    array('label' => __('Event Stop'), 'width' => 'auto', 'filter' => true),
    array('label' => __('Time In'), 'width' => 'auto', 'filter' => true),
    array('label' => __('Time Out'), 'width' => 'auto', 'filter' => true),
    array('label' => __('Total Duration'), 'width' => 'auto', 'filter' => true)
);

// heading
$this->PhpExcel->addTableHeader($table, array('name' => 'Cambria', 'bold' => true));


// data
foreach ($time_data as $time) {
    $this->PhpExcel->addTableRow(array(
    	$time['Event']['title'],
    	$time['Event']['description'],
    	$time['Event']['Organization']['name'],
    	$time['Event']['start_time'],
    	$time['Event']['stop_time'],
    	$time['Time']['start_time'],
    	$time['Time']['stop_time'],
    	$time['Time']['duration']
    ));
}

$this->PhpExcel->addTableFooter();
$this->PhpExcel->output(); 