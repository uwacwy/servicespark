		<h3>Filter</h3>
		<div class="list-group"><?php echo $this->Html->link(
	__("Month To Date (%s)", date("F Y")),
	array(
		$organization['organization_id'],
		'mtd'
	),
	array('class' => 'list-group-item')
); ?>
<?php echo $this->Html->link(
	__("Last Month (%s)", date("F Y", strtotime('-1 month'))),
	array(
		$organization['organization_id'],
		'last_month'
	),
	array('class' => 'list-group-item')
); ?>
<?php echo $this->Html->link(
	__("This Year (%s)", date("Y")),
	array(
		$organization['organization_id'],
		'ytd'
	),
	array('class' => 'list-group-item')
); ?>

</div>

<?php
$form_defaults['type'] = 'get';
$form_defaults['url'] = array('supervisor' => true, 'controller' => 'organizations', 'action' => 'dashboard', $organization['organization_id'], 'custom');
echo $this->Form->create('Organization', $form_defaults); ?>


<p>
	<i class="glyphicon glyphicon-hand-right"></i>
	<?php echo __("You can bookmark or share any custom view. You can only share this page with other <strong>%s</strong> %s.",
	$organization['name'],
	Inflector::pluralize($permission)
	); ?>
</p>

<?php echo $this->Form->input('Filter.start_date', array(
	'type' => 'date', 
	'separator' => '', 
	'default' => array(
		'month' => date('m'), 
		'day' => 1, 
		'year' => date('Y')
	)
)); ?>
<?php echo $this->Form->input('Filter.stop_date', array(
	'type' => 'date', 
	'separator' => '', 
	'default' => array(
		'month' => date('m'), 
		'day' => date('t'), 
		'year' => date('Y')
	)
)); ?>

<?php echo $this->Form->end( array('label' => __("Apply Custom"), 'class' => 'btn btn-default') ); ?>