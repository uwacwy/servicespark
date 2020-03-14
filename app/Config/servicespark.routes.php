<?php


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