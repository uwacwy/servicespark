<div class="row">
	<div class="col-md-12">
		<h1>Activity<small> View user activity for your organization.</small></h1>
		<div class="row">
			<div class="col-md-3">
				<div class="well text-center">
					<span class="stat"><?php echo h( number_format($summary_all_time[0][0]['OrganizationAllTime']/60, 2) ); ?></span>
					hours volunteered all-time
				</div>
			</div>
			<div class="col-md-3">
				<div class="well text-center">
					<span class="stat"><?php echo h( number_format($summary_past_month[0][0]['OrganizationPastMonth']/60, 2) ); ?></span>
					hours volunteered in past month
				</div>
			</div>
			<div class="col-md-3">
				<div class="well text-center">
					<span class="stat"><?php echo h( number_format($summary_past_year[0][0]['OrganizationPastYear']/60, 2) ); ?></span>
					hours volunteered in past year
				</div>
			</div>
			<div class="col-md-3">
				<div class="well text-center">
					<span class="stat"><?php echo h( number_format($summary_ytd[0][0]['OrganizationYTD']/60, 2) ); ?></span>
					hours volunteered year-to-date
				</div>
			</div>
		</div>

		<?php echo $this->Form->create('Organization', $form_defaults); ?>
		<?php echo $this->Form->end(array('label' => "Generate Excel for this Organization", 'class' => 'btn btn-success')); ?>
		
		<h2>Members</h2>
		<div class="table-responsive">
			<table class="table table-striped"> 
				<thead>  
			  		<tr>
			  			<th>Name</th>
			  			<th>Email</th>
			  			<th>Number of Events</th>
			  			<th>Total Volunteer Hours</th>
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
					  			<?php echo $hours['User']['email']; ?>
				  			</td>
				  			<td>
				  				<?php echo $hours['0']['UserNumberEvents']; ?>
				  			</td>
				  			<td>
				  				<?php echo $hours['0']['UserSumTime']; ?>
				  			</td>
				  		</tr>
			  		<?php endforeach; ?>
			  	</tbody>
			</table>
		</div>
	</div>
</div>
