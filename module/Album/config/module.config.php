<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Album\Controller\Album' => 'Album\Controller\AlbumController',
        ),
    ),
    'router' => array(
        'routes' => array(
        	'halbum' => array(
        			'type' => 'Hostname',
        			'options' => array(
        					'route' => 'album.zf215demo.com',
        					'constraints' => array(
        							//'subdomain' => '\d{3,6}',
        					),
        					'defaults' => array(
        						'__NAMESPACE__' => 'Album\Controller',
        						'controller' => 'Album',
        						'action' => 'index',
        					),
        			),
        			'may_terminate' => true,
        			'child_routes' => array(
				        	/* 'home' => array(
				        			'type' => 'literal',
				        			'options' => array(
				        					'route' => '/',
				        					'defaults' => array(
				        							'controller' => 'Album\Controller\Album',
				        							'action' => 'index',
				        					),
				        			),
				        	), */
        					'default' => array(
				        		'type' => 'Segment',
								'options' => array(
										'route' => '/[:action/[:id]]',
										'constraints' => array(
												'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'id' => '[1-9][0-9]*',
										),
								),
				        	),	
        					'info' => array(
        							'type' => 'Segment',
        							'options' => array(
        									'route' => '/:id',
        									'constraints' => array(
        											'id' => '[0-9]+'
        									),
        									'defaults' => array(
        											'action' => 'info',
        									),
        							),
        					)
        			),
        	),
		),
	),
    'view_manager' => array(
        'template_path_stack' => array(
            'album' => __DIR__ . '/../view',
        ),
    ),
);

