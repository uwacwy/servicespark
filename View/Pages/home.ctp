<div class="row">
<div class="col-md-12">	<div class="jumbotron">
		<h1>Welcome to <?php echo Configure::read('Solution.name'); ?></h1>
		<p><?php echo Configure::read('Solution.name'); ?> <?php echo Configure::read('Solution.description'); ?></p>
		<p><?php echo $this->Html->link(
				__('Create an Account'), 
				array(
					'action' => 'register',
					'controller' => 'users', 
					'admin' => false
				),
				array(
					'class' => 'btn btn-primary btn-lg'
				)
			); ?></p>
	</div></div>
</div>

<div class="row text-center">
	<div class="col-md-3">
		<div class="well text-center">
			<span class="stat">#</span>
			currently volunteering
		</div>
	</div>
	<div class="col-md-3">
		<div class="well text-center">
			<span class="stat">#</span>
			registered volunteers
		</div>
	</div>
	<div class="col-md-3">
		<div class="well text-center">
			<span class="stat">#</span>
			hours volunteered this year
		</div>
	</div>
	<div class="col-md-3">
		<div class="well text-center">
			<span class="stat">#</span>
			hours volunteered this month
		</div>
	</div>
</div>