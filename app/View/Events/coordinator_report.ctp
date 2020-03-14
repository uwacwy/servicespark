<?php 
$this->PhpExcel->createWorksheet();
$this->PhpExcel->setDefaultFont('Calibri', 12);

// define table cells
$table = array(
    array('label' => __('First Name'), 'width' => 'auto', 'filter' => true),
    array('label' => __('Last Name'), 'width' => 'auto', 'filter' => true),
    array('label' => __('Email'), 'width' => 'auto', 'filter' => true),
    array('label' => __('Clock In'), 'width' => 'auto', 'filter' => true),
    array('label' => __('Clock Out'), 'width' => 'auto', 'filter' => true),
    array('label' => __('Duration'), 'width' => 'auto', 'filter' => true)
);

// heading
$this->PhpExcel->addTableHeader($table, array('name' => 'Cambria', 'bold' => true));


// data
foreach ($times as $time) {

//    debug($time);

    $i = $this->PhpExcel->_row;

    $this->PhpExcel->addTableRow(array(
        $time['Time']['User']['first_name'],
        $time['Time']['User']['last_name'],
        $time['Time']['User']['email'],
        PHPExcel_Shared_Date::PHPToExcel(strtotime($time['Time']['start_time']), null, date_default_timezone_get() ),
        PHPExcel_Shared_Date::PHPToExcel(strtotime($time['Time']['stop_time']), null, date_default_timezone_get() ),
        $time['Time']['duration']
    ));

    $this->PhpExcel->_xls->getActiveSheet()->getStyle("D$i:E$i")->getNumberFormat()->setFormatCode( 'm/d/yyyy h:mm AM/PM' );
}

$this->PhpExcel->addTableFooter();
date_default_timezone_set('America/Denver');
// ServiceSpark--2020-02-02--event-as-slug--time-entries.xlsx
$filename = sprintf('%s--%s--%s--time-entries.xlsx',
    Configure::read('Solution.name'),
    Inflector::slug($event['Event']['title']),
    date( 'Y-m-d', strtotime( $event['Event']['start_time'] ) )
);
$this->PhpExcel->output( $filename );