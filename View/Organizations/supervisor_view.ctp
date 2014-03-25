<div class="row">
	<div class="col-md-12">
		<?php echo $this->Form->create('Organization', $form_defaults); ?>
		<h1> <?php echo h($organization['Organization']['name']); ?> </h1>
		<hr>
		<h2><small>Volunteers have donated <?php echo h($organizationTime['hours'])?> hours and <?php echo h($organizationTime['minutes'])?> minutes total for this organization.</small></h2>
		<hr>

		<h2>Volunteers for <?php echo h($organization['Organization']['name']); ?>
		<table class="table table-striped"></h2>
			<thead>  
          		<tr>  
            		<th>Name</th>
            		<th>Events Attended</th>
            		<th>Total Hours</th>
            		<th>Total Minutes</th> 
          		</tr>  
        	</thead>
		</table>
	</div>
</div>