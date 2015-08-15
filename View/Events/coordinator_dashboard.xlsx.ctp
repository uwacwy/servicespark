<?php



$this->PhpExcel->createWorksheet();
$this->PhpExcel->setDefaultFont('Calibri', 12);

// define table cells
$table = array(
	array('label' => __('Event Title'), 'width' => 'auto', 'filter' => true),
	array('label' => __('Organization Name'), 'width' => 'auto', 'filter' => true),
	array('label' => __('Event Start Time'), 'width' => 'auto', 'filter' => true),
	array('label' => __('Event Stop Time'), 'width' => 'auto', 'filter' => true),
	array('label' => __('Comment Count'), 'width' => 'auto', 'filter' => true),
	array('label' => __('Attendee Count'), 'width' => 'auto', 'filter' => true),
	array('label' => __('RSVP Count'), 'width' => 'auto', 'filter' => true),
	array('label' => __('RSVP Goal'), 'width' => 'auto', 'filter' => true),
	array('label' => __('RSVP Met'), 'width' => 'auto', 'filter' => true),
	array('label' => __('Skills'), 'width' => 'auto', 'filter' => true),
);

// heading
$this->PhpExcel->addTableHeader($table, array('bold' => true));


// data
foreach ($events as $event)
{
	
	$i = $this->PhpExcel->_row;
	
	$this->PhpExcel->addTableRow(array(
		$event['Event']['title'],
		$event['Organization']['name'],
		$event['Event']['start_time'], // c
		$event['Event']['stop_time'], // d
		$event['Event']['comment_count'],
		count($event['EventTime']),
		$event['Event']['rsvp_count'],
		$event['Event']['rsvp_desired'],
		"=G$i/H$i",
		implode(', ', Hash::extract($event['Skill'], '{n}.skill'))
	));
	
	$this->PhpExcel->_xls->getActiveSheet()->getStyle("I$i")->getNumberFormat()->setFormatCode( '0.0%' );
}


$this->PhpExcel->addTableFooter();
date_default_timezone_set('America/Denver');
$this->PhpExcel->output( __('%s-coordinator-events-%s-%s.xlsx', Inflector::slug(Configure::read('Solution.name')), $period, date('d-M-Y') ) ); 