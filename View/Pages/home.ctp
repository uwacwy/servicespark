<div class="home absorb-margin">

<div class="stripe-lg gradient">
	<h1 class="huge"><?php echo __("Welcome to %s", Configure::read('Solution.name') ); ?></h1>
	<p><?php echo Configure::read('Solution.name'); ?> <?php echo Configure::read('Solution.description'); ?></p>
	<p class="prepend-top"><?php if( !AuthComponent::user('user_id') ): ?>
		
				<?php echo $this->Html->link(
					__('Create an Account'), 
					array(
						'action' => 'register',
						'controller' => 'users', 
						'admin' => false
					),
					array(
						'class' => 'btn-hollow btn-lg'
					)
				); ?> 
				
				<?php 
				echo $this->Html->link(
					__('Login'),
					array(
						'action' => 'login',
						'controller' => 'users',
						'admin' => false
					),
					array(
						'class' => 'btn-hollow btn-lg'
					)
				); ?>
				
			<?php else: ?>
				<?php echo $this->Html->link(
					__('Go to the Time Clock'),
					array(
						'action' => 'index',
						'controller' => 'times',
						'volunteer' => true
					),
					array('class' => 'btn-hollow btn-lg')
				); ?>
				<?php echo $this->Html->link(
					__('View My Activity'),
					array(
						'action' => 'activity',
						'controller' => 'users',
						'volunteer' => true
					),
					array('class' => 'btn-hollow btn-lg')
				); ?>
			<?php endif; ?>
		</p>
</div>

<div class="stripe-sm">
<div class="container">
	<div class="row">
		<div class="col-md-3">
			<div class="stat">
			
				<span class="icon"><i class="glyphicon glyphicon-cog <?php if($currently_volunteering > 0) echo 'spinning'; ?>"></i></span>
				<span class="value animate-count"
					data-count-to="<?php echo h($currently_volunteering); ?>"
					data-count-from="0"
					data-count-speed="750"><?php echo h( number_format($currently_volunteering, 0, ".", ",") ); ?></span>
				<span class="key"><?php echo __("currently volunteering"); ?></span>
			</div>
		</div>
		
		<div class="col-md-3">
			<div class="stat">
				<span class="icon"><i class="glyphicon glyphicon-user"></i></span>
				<span class="value animate-count"
					data-count-to="<?php echo h($registered_volunteers); ?>"
					data-count-from="0"
					data-count-speed="1000"><?php echo h( number_format($registered_volunteers, 0, ".", ",") ); ?></span>
				<span class="key"><?php echo __("registered volunteers"); ?></span>
			</div>
		</div>
		
		<div class="col-md-3">
			<div class="stat">
				<span class="icon"><i class="glyphicon glyphicon-time"></i></span>
				<span class="value animate-count"
					data-count-to="<?php echo h($users_ytd[0][0]['PeriodTotal']); ?>"
					data-count-from="0"
					data-count-speed="1250"><?php echo h( number_format($users_ytd[0][0]['PeriodTotal'], 0, ".", ",") ); ?></span>
				<span class="key"><?php echo __("hours volunteered this year"); ?></span>
			</div>
		</div>
		
		<div class="col-md-3">
			<div class="stat">
				<span class="icon"><i class="glyphicon glyphicon-calendar"></i></span>
				<span class="value animate-count"
					data-count-to="<?php echo number_format($upcoming_events, 0, ".", ","); ?>"
					data-count-from="0"
					data-count-speed="1500"><?php echo h( number_format($upcoming_events, 0, ".", ",") ); ?></span>
				<span class="key"><?php echo __("upcoming events"); ?></span>
			</div>
		</div>
	
	</div>
</div>
</div>



<?php if( !empty($users_top) ): ?>
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<h2 class="text-center"><?php echo __("%d Volunteer Leaderboard", date('Y')); ?></h2>
			<div class="_table">
				<div class="_thead">
					<div class="_tr">
						<div class="_th"><?php echo __("Rank"); ?></div>
						<div class="_th"><?php echo __("Name"); ?></div>
						<div class="_th"><?php echo __("Hours"); ?></div>
					</div>
				</div>
				<div class="_tbody">
					<?php
					$last = PHP_INT_MAX;
					$rank = 0;
					foreach($users_top as $user):
						$omit = true;
						if( $last > $user[0]['UserTotal'] )
						{
							$rank ++;
							$omit = false;
						}
					?>
					<div class="_tr _primary">
						<div class="_td"><?php echo ($omit) ? '<span class="sr-only">'.$rank.'</span>' : number_format($rank); ?></div>
						<div class="_td"><?php echo h(__("%s %s.", $user['User']['first_name'], substr($user['User']['last_name'], 0, 1) ) ); ?></div>
						<div class="_td"><?php echo __("%s hours", number_format($user[0]['UserTotal']) ); ?></div>
					</div>
					<?php 
					$last = $user[0]['UserTotal'];
					endforeach; ?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>