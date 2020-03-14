<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
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
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/View/Pages/home.ctp)...
 */
	Router::connect(
		'/', 
		array('controller' => 'pages', 'action' => 'display', 'home')
	);


/**
 * ...and connect the rest of 'Pages' controller's URLs.
 */
Router::connect('/beta', array('controller' => 'events', 'action' => 'angular') );
Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));



Router::connect(
	'/:prefix/events/:event_id', 
	array(
		'action' => 'get_event',
		'controller' => 'events',
		'[method]' => 'GET',
	),
	array(
		'event_id' => "[0-9]+",
		'api' => true,
		'prefix' => 'api',
		'pass' => array('event_id')
	)
);

Router::connect(
	'/:prefix/events/:event_id/addresses',
	array(
		'action' => 'get_event_addresses',
		'controller' => 'events',
		'[method]' => 'GET',
	),
	array(
		'event_id' => "[0-9]+",
		'api' => true,
		'prefix' => 'api',
		'pass' => array('event_id')
	)
);

Router::connect(
	'/:prefix/events/:event_id/skills',
	array(
		'action' => 'get_event_skills',
		'controller' => 'events',
		'[method]' => 'GET',
	),
	array(
		'event_id' => "[0-9]+",
		'api' => true,
		'prefix' => 'api',
		'pass' => array('event_id')
	)
);


Router::connect(
	'/:prefix/events/:event_id/comments',
	array(
		'action' => 'get_event_comments',
		'controller' => 'events',
		'[method]' => 'GET',
	),
	array(
		'event_id' => "[0-9]+",
		'api' => true,
		'prefix' => 'api',
		'pass' => array('event_id')
	)
);

Router::connect(
	'/:prefix/events/:event_id/comments',
	array(
		'action' => 'post_event_comment',
		'controller' => 'events',
		'[method]' => 'POST',
	),
	array(
		'event_id' => "[0-9]+",
		'api' => true,
		'prefix' => 'api',
		'pass' => array('event_id')
	)
);

Router::connect(
	'/:prefix/events/:event_id/rsvps',
	array(
		'action' => 'get_event_rsvps',
		'controller' => 'events',
		'[method]' => 'GET',
	),
	array(
		'event_id' => "[0-9]+",
		'api' => true,

		'prefix' => 'api',
		'pass' => array('event_id')
	)
);

Router::connect(
	'/:prefix/events/:event_id/rsvps/me', 
	array(
		'action' => 'get_my_rsvp',
		'controller' => 'events',
		'[method]' => 'GET',
	),
	array(
		'event_id' => "[0-9]+",
		'api' => true,

		'prefix' => 'api',
		'pass' => array('event_id')
	)
);

Router::connect(
	'/:prefix/events/:event_id/rsvps/me', 
	array(
		'action' => 'update_my_rsvp',
		'controller' => 'events',
		'[method]' => 'PATCH',
	),
	array(
		'event_id' => "[0-9]+",
		'api' => true,
		'prefix' => 'api',
		'pass' => array('event_id')
	)
);

Router::connect(
	'/:prefix/events/:event_id/times',
	array(
		'action' => 'get_event_times',
		'controller' => 'events',
		'[method]' => 'GET',
	),
	array(
		'event_id' => "[0-9]+",
		'api' => true,

		'prefix' => 'api',
		'pass' => array('event_id')
	)
);

/**
 * GET /api/organizations/:organization_id
 */
Router::connect(
	'/:prefix/organizations/:organization_id',
	array(
		'action' => 'get_organization',
		'controller' => 'organizations',
		'[method]' => 'GET',
	),
	array(
		'organization_id' => "[0-9]+",
		'api' => true,
		'prefix' => 'api',
		'pass' => array('organization_id')
	)
);

/**
 * GET /api/organizations/:organization_id
 */
Router::connect(
	'/:prefix/organizations/:organization_id/events',
	array(
		'action' => 'get_organization_events',
		'controller' => 'organizations',
		'[method]' => 'GET',
	),
	array(
		'organization_id' => "[0-9]+",
		'api' => true,
		'prefix' => 'api',
		'pass' => array('organization_id')
	)
);

/**
 * GET /api/organizations/:organization_id/role
 */
Router::connect(
	'/:prefix/organizations/:organization_id/role',
	array(
		'action' => 'get_organization_role',
		'controller' => 'organizations',
		'[method]' => 'GET',
	),
	array(
		'organization_id' => "[0-9]+",
		'api' => true,
		'prefix' => 'api',
		'pass' => array('organization_id')
	)
);


/**
 * GET /api/webhooks/:token
 */
Router::connect(
    '/:prefix/webhooks/:token',
    array(
        'action' => 'get',
        'controller' => 'webhooks',
        '[method]' => 'GET',
    ),
    array(
        'token' => "[a-zA-Z0-9]+",
        'api' => true,
        'prefix' => 'api',
        'pass' => array('token')
    )
);

/**
 * POST /api/webhooks/:token
 */
Router::connect(
    '/:prefix/webhooks/:token',
    array(
        'action' => 'post',
        'controller' => 'webhooks',
        '[method]' => 'POST',
    ),
    array(
        'token' => "[_a-zA-Z0-9]+",
        'api' => true,
        'prefix' => 'api',
        'pass' => array('token')
    )
);

Router::connect(
	'/:prefix/rsvps/:rsvp_id',
	array(
		'action' => 'patch_rsvp',
		'controller' => 'rsvps',
		'[method]' => 'PATCH'
	),
	array(
		'rsvp_id' => "[0-9]+",
		'api' => true,
		'prefix' => 'api',
		'pass' => array('rsvp_id')
	)
);