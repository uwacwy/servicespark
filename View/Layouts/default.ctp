<?php
/**
 *
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

$cakeDescription = __d('cake_dev', 'CakePHP: the rapid development php framework');

global $solution_name;
?>
<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<meta name="viewport" content="width=device-width, user-scalable=no">
	<title>
		<?php echo $title_for_layout; ?> &ndash; <?php echo Configure::read('Solution.name'); ?>
	</title>
	<style>
		.stat{font-weight:bold; font-size: 48px;display: block;}
		a.asc:after { content: "\e155"; }
		a.desc:after { content: "\e156"; }
		a.asc:after,
		a.desc:after {
			position: relative;
			top: 1px;
			display: inline-block;
			font-family: 'Glyphicons Halflings';
			font-style: normal;
			font-weight: normal;
			color: #000;
			margin-left: 7px;
			line-height: 1;
			-webkit-font-smoothing: antialiased;
			-moz-osx-font-smoothing: grayscale;
		}

		.form-inline .form-group label,
		.form-inline .form-group input,
		.form-inline .form-group select
		{
			margin-right: 10px;
		}

		.append-bottom
		{
			margin-bottom: 1.5em;
		}
		.append-top
		{
			margin-top: 1.5em;
		}
		.prepend-left
		{
			margin-left: 20px;
		}
		.collapse-top
		{
			margin-top: 0 !important;
			padding-top: 0 !important;
		}
	</style>
	<?php		
		echo $this->Html->css('autocomplete');
		

		if( Configure::read('debug') > 0 )
		{
			echo $this->Html->script('//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.js');
			echo $this->Html->script('//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.js');
			echo $this->Html->script('//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.js');
			echo $this->Html->css('//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.css');
		}
		else
		{
			echo $this->Html->script('//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js');
			echo $this->Html->script('//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js');
			echo $this->Html->script('//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js');
			echo $this->Html->css('//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css');
		}
		echo $this->Html->script('mustaches');
		echo $this->Html->script('mustache');
		echo $this->Html->script('uwac');

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');

	?>
</head>
<body>

<nav class="navbar navbar-default" role="navigation">
  <div class="container-fluid">
	<!-- Brand and toggle get grouped for better mobile display -->
	<div class="navbar-header">
	  <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#ss-navbar">
		<span class="sr-only">Toggle navigation</span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
	  </button>
	  <a class="navbar-brand" href="<? echo $this->Html->url('/'); ?>"><?php echo Configure::read('Solution.name'); ?></a>
	</div>

	<!-- Collect the nav links, forms, and other content for toggling -->
	<div class="collapse navbar-collapse" id="ss-navbar">

	  <ul class="nav navbar-nav">
		<?php if( $super_admin ) : ?>
			<?php

			$admin_menu = array(
				'users' => array(
					'label' => __('Users'),
					'actions' => array(
						'register' => __('Register New User'),
						'find' => __('Search Users')
					)
				),
				'organizations' => array(
					'label' => __('Organizations'),
					'actions' => array(
						'add' => __('Create Organization'),
						'search' => __('Search Organizations')
					)
				),
				'events' => array(
					'label' => __('Events'),
					'actions' => array(
						'add' => __('New Event'),
						'index' => __('List Events')
					)
				),
				'addresses' => array(
					'label' => __('Addresses'),
					'actions' => array(
						'index' => __('List Addresses'),
						'add' => __('Add Address')
					)
				),
				'skills' => array(
					'label' => __('Skills'),
					'actions' => array(
						'index' => __('List Skills'),
						'add' => __('Add a Skill')
					)
				),
				'times' => array(
					'label' => __('Time'),
					'actions' => array(
						'index' => __('List Time Punches')
					)
				),
				'permissions' => array(
					'label' => __('Permissions'),
					'actions' => array(
						'add' => __('Grant New Permission')
					)
				)
			);
			foreach($admin_menu as $controller => $properties) : ?>
				<li class="dropdown">
					<?php
						echo sprintf('<a href="%s" class="dropdown-toggle" data-toggle="dropdown">%s <b class="caret"></b></a>',
							$this->Html->url( array('controller' => $controller, 'action' => 'index', 'admin' => true) ),
							h($properties['label'])
						);
					?>
					<ul class="dropdown-menu">
						<?php
							foreach($properties['actions'] as $action => $label)
							{
								echo sprintf('<li><a href="%s">%s</a></li>',
									$this->Html->url( array('controller' => $controller, 'action' => $action, 'admin' => true) ),
									h($label)
								);
							}
						?>
					</ul>
				</li>
			<?php endforeach; ?>

		<?php else : ?>
			<?php
			
			$user_menu = array(
				'organizations' => array(
					'label' => __('Organizations'),
					'actions' => array(
						'join' => __('Join an Organization'),
						'add' => __('New Organization'),
						'index' => __('Manage my Organizations')
					)
				),
				'events' => array(
					'label' => __('Events'),
					'actions' => array(
						'add' => __('New Event'),
						'index' => __('List Events'),
						'search' => __('Search Events'),
						'matches' => __('Events For Me')
					)
				)
			);

			foreach($user_menu as $controller => $properties) : ?>
				<li class="dropdown">
					<?php
						echo sprintf('<a href="%s" class="dropdown-toggle" data-toggle="dropdown">%s <b class="caret"></b></a>',
							$this->Html->url( array('controller' => $controller, 'action' => 'index', 'admin' => false, 'coordinator' => true) ),
							h($properties['label'])
						);
					?>
					<ul class="dropdown-menu">
						<?php
							foreach($properties['actions'] as $action => $label)
							{
								echo sprintf('<li><a href="%s">%s</a></li>',
									$this->Html->url( array('controller' => $controller, 'action' => $action, 'go' => true) ),
									h($label)
								);
							}
						?>
					</ul>
				</li>
			<?php endforeach; ?>
		<?php endif; ?>
			<li class="dropdown">
				<?php
					echo sprintf('<a href="%S" class="dropdown-toggle" data-toggle="dropdown">%s <b class="caret"></b></a>',
						$this->Html->url( array('controller' => 'organizations', 'action' => 'index', 'coordinator' => true) ),
						'Coordinate'
					);
				?>
				<ul class="dropdown-menu">
					<li><?php echo $this->Html->link(__('My Organizations'), array('controller'=>'organizations', 'action' => 'index', 'coordinator' => true)); ?></li>
					<li class="divider"></li>
	<li><?php echo $this->Html->link(__('My Events'), array('controller' => 'events', 'action' => 'index', 'coordinator' => true) ); ?></li>
	<li><?php echo $this->Html->link(__('New Event'), array('controller' => 'events', 'action' => 'add', 'coordinator' => true) ); ?> </li>
				</ul>
			</li>
		</ul>

		<?php if(AuthComponent::user('user_id') ) : ?>
			<ul class="nav navbar-nav navbar-right">
				<?php
					$right_menu = array(
						'users' => array(
								'label' => sprintf('Hello, %s', AuthComponent::user('full_name') ),
								'actions' => array(
									'profile' => __('Edit My Profile'),
									'activity' => 'View Activity',
									'logout' => __('Logout')

								)
						)
					);
				?>

				<?php foreach($right_menu as $controller => $properties) : ?>
					<li class="dropdown">
						<?php
							echo sprintf('<a href="%s" class="dropdown-toggle" data-toggle="dropdown">%s <b class="caret"></b></a>',
								$this->Html->url( array('controller' => $controller, 'action' => 'index', 'admin' => false) ),
								h($properties['label'])
							);
						?>
						<ul class="dropdown-menu">
							<?php
								foreach($properties['actions'] as $action => $label)
								{
									echo sprintf('<li><a href="%s">%s</a></li>',
										$this->Html->url( array('controller' => $controller, 'action' => $action, 'admin' => false) ),
										h($label)
									);
								}
							?>
						</ul>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php else: ?>
			<?php echo $this->Form->create('User', array('action'=> 'login', 'class' => 'navbar-form navbar-right', 'inputDefaults' => array('div' => 'form-group', 'wrapInput' => false, 'class' => 'form-control', 'label' => false))); ?>
				<label>Login</label>
				<?php
					echo $this->Form->input('username', array('placeholder' => 'username'));
					echo $this->Form->input('password', array('placeholder' => 'password'));
				?>
			<?php echo $this->Form->end(array('label'=> 'Login', 'class' => 'btn btn-primary', 'div' => array('class' =>'form-group'))); ?>
		<?php endif; ?>

	</div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>

	<div class="container">
		<div id="content">
			<?php
				echo $this->Session->flash( );
			?>
			<?php echo $this->fetch('content'); ?>
		</div>
	</div>
	<hr>
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<p><?php echo h(Configure::read('Solution.name') ); ?> &copy; <?php echo date('Y'); ?> United Way of Albany County</p>
				<p>Proudly powered by CakePHP, Bootstrap, jQuery, MediaTemple, PHP, Apache and MySQL.</p>
				<!--
					<?php echo h( Configure::read('Solution.name') ); ?> was built by Brad Kovach, Jamie Wiggins, and Thomas Wolf for a University of Wyoming Computer Science Senior Design project
					for the 2013-2014 academic year.  <?php echo h( Configure::read('Solution.name') ); ?> is deliberately built on open-source technologies to pave the way for
					wide-spread use in non-profit environments.  To inquire about licensing <?php echo h( Configure::read('Solution.name') ); ?> for your community, 
					please contact volunteer@unitedwayalbanycounty.org
				-->
			</div>
		</div>
		<?php  echo $this->element('sql_dump'); ?>
	</div>
</body>
</html>
