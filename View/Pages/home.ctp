<div class="row">
<div class="col-md-12">	<div class="jumbotron">
		<h1>Welcome to <?php echo Configure::read('Solution.name'); ?></h1>
		<p><?php echo Configure::read('Solution.name'); ?> <?php echo Configure::read('Solution.description'); ?></p>
		<p><?php
			if( !AuthComponent::user('user_id') )
			{
				echo $this->Html->link(
					__('Create an Account'), 
					array(
						'action' => 'register',
						'controller' => 'users', 
						'admin' => false
					),
					array(
						'class' => 'btn btn-primary btn-lg'
					)
				);
				echo " ";
				echo $this->Html->link(
					__('Login'),
					array(
						'action' => 'login',
						'controller' => 'users',
						'admin' => false
					),
					array(
						'class' => 'btn btn-success btn-lg'
					)
				);
			}
			else 
			{
				echo $this->Html->link(
					__('I have a time token.'),
					array(
						'action' => 'index',
						'controller' => 'times',
						'volunteer' => true
					),
					array('class' => 'btn btn-primary')
				);
			}
			?></p>
	</div></div>
</div>

<div class="row text-center">
	<div class="col-md-3">
		<div class="well text-center">
			<span class="stat">5</span>
			currently volunteering
		</div>
	</div>
	<div class="col-md-3">
		<div class="well text-center">
			<span class="stat">142</span>
			registered volunteers
		</div>
	</div>
	<div class="col-md-3">
		<div class="well text-center">
			<span class="stat">7,258</span>
			hours volunteered this year
		</div>
	</div>
	<div class="col-md-3">
		<div class="well text-center">
			<span class="stat">70</span>
			hours volunteered this month
		</div>
	</div>
</div>