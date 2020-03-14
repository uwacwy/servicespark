<?php

$this->PhpExcel->createWorksheet();
$this->PhpExcel->setDefaultFont('Calibri', 12);

// define table cells
$table = array(
    array('label' => __('Time In'), 'width' => 'auto', 'filter' => true),
    array('label' => __('Time Out'), 'width' => 'auto', 'filter' => true),
    array('label' => __('Total Duration'), 'width' => 'auto', 'filter' => true),
    array('label' => __('Organization Name'), 'width' => 'auto', 'filter' => true),
    array('label' => __('Event Name'), 'width' => 'auto', 'filter' => true),
    array('label' => __('Description/Memo'), 'width' => 'auto', 'filter' => true),
    array('label' => __('Event Start Time'), 'width' => 'auto', 'filter' => true),
    array('label' => __('Event Stop Time'), 'width' => 'auto', 'filter' => true),
);

// heading
$this->PhpExcel->addTableHeader($table, array('bold' => true));


// data
foreach ($time_data as $time) {

    $i = $this->PhpExcel->_row;
    
    if( !empty($time['EventTime']) )
    {
    	$this->PhpExcel->addTableRow(array(
			PHPExcel_Shared_Date::PHPToExcel(strtotime($time['Time']['start_time']), null, date_default_timezone_get() ),
			PHPExcel_Shared_Date::PHPToExcel(strtotime($time['Time']['stop_time']), null, date_default_timezone_get() ),
			$time['Time']['duration'],
			$time['EventTime'][0]['Event']['Organization']['name'],
			$time['EventTime'][0]['Event']['title'],
			$time['EventTime'][0]['Event']['description'],
			$time['EventTime'][0]['Event']['start_time'],
			$time['EventTime'][0]['Event']['stop_time']
    	));
    }
    elseif( !empty($time['OrganizationTime']) )
    {
		$this->PhpExcel->addTableRow(array(
			PHPExcel_Shared_Date::PHPToExcel(strtotime($time['Time']['start_time']), null, date_default_timezone_get() ),
			PHPExcel_Shared_Date::PHPToExcel(strtotime($time['Time']['stop_time']), null, date_default_timezone_get() ),
			$time['Time']['duration'],
			$time['OrganizationTime'][0]['Organization']['name'],
			"",
			$time['OrganizationTime'][0]['memo']
    	));
    }

    $this->PhpExcel->_xls->getActiveSheet()->getStyle("A$i:B$i")->getNumberFormat()->setFormatCode( 'm/d/yyyy h:mm AM/PM' );
    $this->PhpExcel->_xls->getActiveSheet()->getStyle("G$i:H$i")->getNumberFormat()->setFormatCode( 'm/d/yyyy h:mm AM/PM' );

}

$this->PhpExcel->addTableFooter();
date_default_timezone_set('America/Denver');
$this->PhpExcel->output( __('%s-%s-activity-%s-%s.xlsx', Configure::read('Solution.name'), AuthComponent::user('username'), $period, date('d-M-Y') ) ); 