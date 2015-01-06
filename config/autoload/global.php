<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return array(
    'db' => array(
        //供factories:Zend\Db\Adapter\Adapter 使用
        'driver'         => 'Pdo',
        'dsn'            => 'mysql:dbname=zf2skeleton;host=localhost',
        'driver_options' => array(
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        ),
        //多数据库配置 供abstract_factories使用
        'adapters' => array(
            'Db\ReadOnly' => array(
                'driver'         => 'Pdo',
                'dsn'            => 'mysql:dbname=zf2skeleton;host=localhost',
                'driver_options' => array(
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
                ),
            ),
            'Db\Writeable' => array(
                'driver'         => 'Pdo',
                'dsn'            => 'mysql:dbname=zf2skeleton;host=localhost',
                'driver_options' => array(
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter\Adapter'
                    => 'Zend\Db\Adapter\AdapterServiceFactory',
        ),
        'abstract_factories' => array(
            'Zend\Db\Adapter\AdapterAbstract'
                    => 'Zend\Db\Adapter\AdapterAbstractServiceFactory',
        ),
    ),
	'site_evn'=>array(
			'topdomain' => 'wwww.zf2demo.com',
	),	
);