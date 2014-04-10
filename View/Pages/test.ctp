<?php
	// ideally $links[$prefix][$controller][$action]
	$crud = array('index', 'create', 'read', 'update', 'delete');
	$links = array(
		'go' => array(
			'events' => $crud,
			'organizations' => $crud,
			'users' => $crud,
			'times' => $crud
		),
		'admin' => array(
			'events' => $crud,
			'organizations' => $crud,
			'users' => $crud,
			'times' => $crud
		),
		'manager' => array(
			'events' => array('index', 'view'),
			'organizations' => array('view')
		),
		'coordinator' => array(
			'events' => array('index', 'add', 'edit', 'delete'),
			'organizations' => array('index', 'edit'),
			'times' => array('edit', 'delete', 'adjust')
		),
		'volunteer' => array(
			'events' => $crud,
			'organizations' => array('leave', 'join', 'create'),
			'users' => array('profile', 'activity'),
			'times' => array('index', 'in', 'out')
		),
		'none' => array(
			'users' => array('profile', 'activity'),
			'recoveries' => array('user')
		)
	);

	foreach($links as $prefix => $controllers)
	{
		echo sprintf("<hr><h1><small>prefix</small> %s</h1>", $prefix);

		foreach($controllers as $controller => $actions)
		{
			echo sprintf("<h2>%s</h2>", $controller);
			echo '<ul>';
			foreach($actions as $key => $action)
			{
				$url = array(
					$prefix => true,
					'controller' => $controller,
					'action' => $action
				);

				if($prefix == "none")
					unset($url[$prefix]);

				echo sprintf('<li>%s</li>', $this->Html->link($action, $url) );
			}
			echo '</ul>';
		}
	}