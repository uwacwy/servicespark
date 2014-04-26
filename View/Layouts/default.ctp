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
			margin-right: 5px;
			margin-left: 5px;
		}

		.append-bottom
		{
			margin-bottom: 1.5em !important;
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
	  	<?php
	  		$dropdown_sprint = '<a href="%s" class="dropdown-toggle" data-toggle="dropdown">%s <b class="caret"></b></a>';
	  		$item_sprint = '<li><a href="%s">%s%s</a></li>';
	  	?>
	  	<?php if( !AuthComponent::user('user_id') ): ?>
	  		<li class="dropdown">
	  			<?php echo sprintf($dropdown_sprint,
	  				$this->Html->url( array('controller' => 'events', 'action' => 'index', 'go' => false) ),
	  				__('Explore %s', Configure::read('Solution.name') )
	  				);
	  			?>
	  			<ul class="dropdown-menu">
	  				<li class="dropdown-header"><?php echo h( __('Events') ); ?></li>
	  				<?php
	  					echo sprintf($item_sprint,
		  					$this->Html->url( array('controller' => 'events', 'action' => 'index', 'go' => false) ),
		  					'<span class="glyphicon glyphicon-calendar"></span> ',
		  					__('Ongoing and Upcoming Events')
		  				);
	  				?>
	  				<?php
	  					echo sprintf($item_sprint,
	  						$this->Html->url( array('controller' => 'events', 'action' => 'search', 'go' => false) ),
	  						'<span class="glyphicon glyphicon-search"></span> ',
	  						__('Search Events')
	  					);
	  				?>
	  				<li class="divider"></li>
	  				<li class="dropdown-header"><?php echo h( __('Your Account') ); ?></li>
	  				<?php
	  					echo sprintf($item_sprint,
	  						$this->Html->url( array('controller' => 'users', 'action' => 'login', 'go' => false) ),
	  						'<span class="glyphicon glyphicon-user"></span> ',
	  						__('Login to %s', Configure::read('Solution.name') )
	  					);
	  				?>
	  				<?php
	  					echo sprintf($item_sprint,
	  						$this->Html->url( array('controller' => 'users', 'action' => 'register', 'go' => false) ),
	  						'<span class="glyphicon glyphicon-plus"></span> ',
	  						__('Create %s Account', Configure::read('Solution.name') )
	  					);
	  				?>
	  				<?php
	  					echo sprintf($item_sprint,
	  						$this->Html->url( array('controller' => 'recoveries', 'action' => 'user', 'go' => false) ),
	  						'<span class="glyphicon glyphicon-question-sign"></span> ',
	  						__('Lost Password Recovery')
	  					);
	  				?>
	  			</ul>
	  		</li>
	 	 <?php endif; ?>


	  	<?php if( AuthComponent::user('user_id') ): ?>
			<li class="dropdown">
				<?php echo sprintf($dropdown_sprint,
					$this->Html->url( array('controller' => 'events', 'action' => 'index', 'volunteer' => true) ),
					__('Volunteer')
					);
				?>
				<ul class="dropdown-menu">
					<li class="dropdown-header"><?php echo h( __('Events') ); ?></li>
					<?php
						echo sprintf($item_sprint,
	  					$this->Html->url( array('controller' => 'events', 'action' => 'index', 'volunteer' => true) ),
	  					'<span class="glyphicon glyphicon-calendar"></span> ',
	  					__('Ongoing and Upcoming Events')
	  				);
					?>
					<?php
						echo sprintf($item_sprint,
							$this->Html->url( array('controller' => 'events', 'action' => 'search', 'volunteer' => true) ),
							'<span class="glyphicon glyphicon-search"></span> ',
							__('Search Events')
						);
					?>
					<li class="divider"></li>
					<li class="dropdown-header"><?php echo h( __('Organizations') ); ?></li>
					<?php
						echo sprintf($item_sprint,
							$this->Html->url( array('controller' => 'organizations', 'action' => 'join', 'volunteer' => true) ),
							'<span class="glyphicon glyphicon-remove-circle"></span> ',
							__('Join Organizations')
						);
					?>
					<?php
						echo sprintf($item_sprint,
							$this->Html->url( array('controller' => 'organizations', 'action' => 'leave', 'volunteer' => true) ),
							'<span class="glyphicon glyphicon-remove-circle"></span> ',
							__('Leave An Organizations')
						);
					?>
					<li class="divider"></li>
					<li class="dropdown-header"><?php echo h( __('Accounts') ); ?></li>
					<?php
						echo sprintf($item_sprint,
							$this->Html->url( array('controller' => 'users', 'action' => 'register', 'volunteer' => true) ),
							'<span class="glyphicon glyphicon-user"></span> ',
							__('Create %s Account', Configure::read('Solution.name') )
						);
					?>
					<?php
						echo sprintf($item_sprint,
							$this->Html->url( array('controller' => 'recoveries', 'action' => 'user', 'volunteer' => true) ),
							'<span class="glyphicon glyphicon-question-sign"></span> ',
							__('Lost Password Recovery')
						);
					?>
				</ul>
			</li>
	  	<?php endif ?>

	  	<?php if( AuthComponent::user('user_id') && $this->Session->read('can_supervise') ): ?>
	  		<li class="dropdown">
				<?php 
					echo sprintf($dropdown_sprint,
						$this->Html->url( array('controller' => 'events', 'action' => 'index', 'supervisor' => true) ),
						__('Supervise')
					);
				?>
				<ul class="dropdown-menu">
					<li class="dropdown-header"><?php echo h( __('Organizations') ); ?></li>
					<?php
						echo sprintf($item_sprint,
							$this->Html->url( array('controller' => 'organizations', 'action' => 'index', 'supervisor' => true) ),
							'<span class="glyphicon glyphicon-bullhorn"></span> ',
							__('My Supervising Organizations')
						);
					?>
					<?php
						echo sprintf($item_sprint,
							$this->Html->url( array('controller' => 'organizations', 'action' => 'leave', 'supervisor' => true) ),
							'<span class="glyphicon glyphicon-remove-circle"></span> ',
							__('Leave Supervising Organizations')
						);
					?>
					<li class="divider"></li>
					<li class="dropdown-header"><?php echo h( __('Events') ); ?></li>
					<?php
						echo sprintf($item_sprint,
							$this->Html->url( array('controller' => 'events', 'action' => 'index', 'supervisor' => true) ),
							'<span class="glyphicon glyphicon-info-sign"></span> ',
							__('Supervisor Dashboard')
						);
					?>
				</ul>
			</li>
	  	<?php endif; ?>

	  	<?php if( AuthComponent::user('user_id') && $this->Session->read('can_coordinate') ): ?>
	  		<li class="dropdown">
				<?php 
					echo sprintf($dropdown_sprint,
						$this->Html->url( array('controller' => 'events', 'action' => 'index', 'coordinator' => true) ),
						__('Coordinate')
					);
				?>
				<ul class="dropdown-menu">
					<li class="dropdown-header"><?php echo h( __('Organizations') ); ?></li>
					<?php
						echo sprintf($item_sprint,
							$this->Html->url( array('controller' => 'organizations', 'action' => 'index', 'coordinator' => true) ),
							'<span class="glyphicon glyphicon-bullhorn"></span> ',
							__('My Coordinating Organizations')
						);
					?>
					<?php
						echo sprintf($item_sprint,
							$this->Html->url( array('controller' => 'organizations', 'action' => 'leave', 'coordinator' => true) ),
							'<span class="glyphicon glyphicon-remove-circle"></span> ',
							__('Leave Coordinating Organizations')
						);
					?>
					<li class="divider"></li>
					<li class="dropdown-header"><?php echo h( __('Events') ); ?></li>
					<?php
						echo sprintf($item_sprint,
							$this->Html->url( array('controller' => 'events', 'action' => 'index', 'coordinator' => true) ),
							'<span class="glyphicon glyphicon-info-sign"></span> ',
							__('Coordinator Dashboard')
						);
					?>
					<?php
						echo sprintf($item_sprint,
							$this->Html->url( array('controller' => 'events', 'action' => 'add', 'coordinator' => true) ),
							'<span class="glyphicon glyphicon-plus"></span> ',
							__('Create an Event')
						);
					?>
				</ul>
			</li>
	  	<?php endif; ?>

	  	<?php
	  		$inline_form = $form_defaults;
	  		$inline_form['class'] = 'navbar-form navbar-left';
	  		$inline_form['type'] = 'get';
	  		$inline_form['action'] = 'index';
	  	echo $this->Form->create('Search', $inline_form); ?>
	  		<?php echo $this->Form->input('query', array('placeholder' => __('search %s', Configure::read('Solution.name') ), 'label' => false ) ); ?>
	  	<?php echo $this->form->end( array('label' => 'search', 'class' => 'btn btn-default', 'div' => false) ); ?>

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
