<?php 
$this->PhpExcel->createWorksheet();
$this->PhpExcel->setDefaultFont('Calibri', 12);

// define table cells
$table = array(
    array('label' => __('Last Name'), 'width' => 'auto', 'filter' => true),
    array('label' => __('First Name'), 'width' => 'auto', 'filter' => true),
    array('label' => __('Number Events'), 'width' => 'auto', 'filter' => true),
    array('label' => __('Total Hours'), 'width' => 'auto')
);

// heading
$this->PhpExcel->addTableHeader($table, array('name' => 'Cambria', 'bold' => true));


// data
foreach ($userHours as $user) {
    $this->PhpExcel->addTableRow(array(
        $user['User']['last_name'],
        $user['User']['first_name'],
        $user[0]['UserNumberEvents'],
        $user[0]['UserSumTime']
    ));
}

$this->PhpExcel->addTableFooter();
$this->PhpExcel->save('php://output');
//$this->PhpExcel->output(); 

?>