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

$solution_name = "United Way of Albany County";
?>
<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<meta name="viewport" content="width=device-width, user-scalable=no">
	<title>
		<?php echo $cakeDescription ?>:
		<?php echo $title_for_layout; ?>
	</title>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>
	<?php

		echo $this->Html->meta('icon');

		echo $this->Html->css('https://netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css');
		echo $this->Html->css('autocomplete');
		echo $this->Html->script('https://netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js');
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
	  <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
		<span class="sr-only">Toggle navigation</span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
	  </button>
	  <a class="navbar-brand" href="<? echo $this->Html->url('/'); ?>"><?php echo h($solution_name); ?></a>
	</div>

	<!-- Collect the nav links, forms, and other content for toggling -->
	<div class="collapse navbar-collapse">

	  <ul class="nav navbar-nav">
		<?php if( $super_admin ) : ?>
			<?php

			$admin_menu = array(
				'users' => array(
					'label' => "Manage Users",
					'actions' => array(
						'register' => "Register New User"
					)
				),
				'events' => array(
					'label' => "Manage Events",
					'actions' => array(
						'add' => "New Event",
						'index' => "List Events"
					)
				),
				'addresses' => array(
					'label' => "Manage Addresses",
					'actions' => array(
						'index' => "List Addresses",
						'add' => "Add Address"
					)
				),
				'skills' => array(
					'label' => "Manage Skills",
					'actions' => array(
						'index' => "List Skills",
						'add' => "Add a Skill"
					)
				),
				'times' => array(
					'label' => "Manage Time",
					'actions' => array(
						'index' => "List Time Punches"
					)
				),
				'permissions' => array(
					'label' => "Manage Permissions",
					'actions' => array(
						'add' => "Grant New Permission"
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
					'label' => "Organizations",
					'actions' => array(
						'add' => "Create an Organization",
						'index' => "Manage my Organizations"
					)
				),
				'events' => array(
					'label' => "Events",
					'actions' => array(
						'add' => "New Event",
						'index' => "List Events",
						'search' => "Search Events"
					)
				),
				'users' => array(
					'label' => "Me",
					'actions' => array(
						'profile' => "Edit My Profile",
						'activity' => "View Volunteer Activity"
					)
				)
			);

			foreach($user_menu as $controller => $properties) : ?>
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
		<?php endif; ?>
		</ul>

		<?php if(AuthComponent::user('user_id') ) : ?>
			<ul class="nav navbar-nav navbar-right">
				<?php
					$right_menu = array(
						'users' => array(
								'label' => sprintf('Hello, %s', AuthComponent::user('full_name') ),
								'actions' => array(
									'profile' => "Edit My Profile",
									'logout' => "Logout"
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
				<?
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
			<?php echo $this->Session->flash(); ?>
			<?php echo $this->fetch('content'); ?>
		</div>
	</div>
	<hr>
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				&copy; <?php echo date('Y'); ?> United Way of Albany County
			</div>
		</div>
		<?php // echo $this->element('sql_dump'); ?>
	</div>
</body>
</html>
