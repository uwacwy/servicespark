<div class="row">
	<div class="col-md-12">
		<?php echo $this->Form->create('Organization', $form_defaults); ?>
		<h1><?php echo h($organization['Organization']['name']); ?><small> Organization Information</small></h1>
		<blockquote>
			View information about this organization, and see the upcoming and ongoing events that this organization is sponsoring.
		</blockquote>
		<hr>
		<?php echo $this->Element('print_addresses', array('addresses' => $organization['Address']) ); ?>

		<h2><small>Upcoming Events</small></h2>
		<hr>
		<div class="table-responsive">
			<table class="table table-striped"> 
				<thead>  
	          		<tr>  
	            		<th> Events </th>  
	            		<th> Date	</th>
	          		</tr>  
	        	</thead>
	        	<?php foreach ($events as $event):?>
	    			<tr>
						<td><?php echo h($event['Event']['title']); ?></td>
						<td><?php echo h($event['Event']['start_time']); ?></td>
						<td>
							<?php echo $this->Html->link(__('View Details'), array('controller' => 'events', 'action' => 'view', $event['Event']['event_id'])); ?>
						</td>
	    			</tr>
				<?php endforeach; ?>
			</table>
		</div>
	</div>
</div>