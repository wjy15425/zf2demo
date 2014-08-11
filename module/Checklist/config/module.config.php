<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Checklist\Controller\Task' => 'Checklist\Controller\TaskController',
            //'Checklist\Controller\Foo' => 'Checklist\Controller\FooController',
        ),
    ),
    'controller_plugins' => array(
        'factories' => array(
            'Translate' => 'Checklist\Controller\Plugin\Translate',
        )
    ),
    'router' => array(
        'routes' =>    array(
/*             'checklist' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/task',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Checklist\Controller',
                        'controller'    => 'Task',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes'  => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route' => '/[:controller[/:action]]',
                            'constraints' => array(
                                    'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                        ),
                    ),
                ),
            ), */
            'task' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/task[/:action[/:id]]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Checklist\Controller',
                        'controller'    => 'Task',
                        'action'        => 'index',
                    ),
                    'constraint' => array(
                        'action' => '^add|edit|delete$',
                        'id'     => '[1-9][0-9]*',
                    ),
                ),
            ),
                
        ),
    ),
    'view_manager' => array(
            'template_path_stack' => array(
                    'album' => __DIR__ . '/../view',
            ),
    ),
    'translator' => array(
        'locale' => 'zh_CN',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
);