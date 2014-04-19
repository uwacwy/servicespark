<div class="row">
	<div class="col-md-12">
		<h1>Activity<small> View user activity for your organization.</small></h1>
		<?php debug($events); ?>
		<h2>Members</h2>
		<table class="table table-striped"> 
			<thead>  
		  		<tr>
		  			<th>Name</th>
		  			<th>Number of Events</th>
		  			<th>Total Volunteer Hours</th>
		  			<th>Action</th>
		  		</tr>
		  	</thead>
		  	<tbody>
		  		<?php foreach ($userHours as $hours): ?>
			  		<tr>
			  			<td>
				  			<?php echo $hours['User']['first_name']; ?>
				  			<?php echo $hours['User']['last_name']; ?>
			  			</td>
			  			<td>
			  				<?php echo $hours['0']['UserNumberEvents']; ?>
			  			</td>
			  			<td>
			  				<?php echo $hours['0']['UserSumTime']; ?>
			  			</td>
			  			<td>
			  				<?php echo $this->Html->link('View Details',
			  					array(
			  						'volunteer' => false,
			  						'controller' => 'users',
			  						'action' => 'activity',
			  						$hours['User']['user_id']
			  					),
			  					array(
			  						'class' => 'btn btn-sm btn-default'
			  					)
			  				);
			  				?>
			  			</td>
			  		</tr>
		  		<?php endforeach; ?>
		  	</tbody>
		</table>
	</div>
</div>