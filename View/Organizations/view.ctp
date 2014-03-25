<div class="row">
	<div class="col-md-12">
		<?php echo $this->Form->create('Organization', $form_defaults); ?>
		<h1> <?php echo h($organization['Organization']['name']); ?> </h1>
		<h2><small>Here are all the upcoming events for this organization!</small></h2>
		<hr>

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
						<?php echo $this->Html->link(__('View Details'), array('action' => 'view', $event['Event']['event_id'])); ?>
					</td>
    			</tr>
			<?php endforeach; ?>
		</table>
	</div>
</div>