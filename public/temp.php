<?php
namespace My {

use Zend\Validator\Iban;

	class A
	{
		protected $username = null;
		protected $password = null;
		public function __construct($username, $password)
		{
			$this->username = $username;
			$this->password = $password;
		}
	}
	
	interface Ib
	{
		
	}

	class B implements Ib
	{
		public $a;
		public function setA($a)
		{
			$this->a = $a;
		}
	}
	class C
	{
		public $b = null;
		public function __construct(Ib $b)
		{
			$this->b = $b;
		}
	}
}
namespace {
use Zend\Di\Di;
use Zend\Di\DefinitionList;
use Zend\Di\Definition;
use Zend\Di\Definition\Builder;

	chdir(dirname(__DIR__));
	require 'init_autoloader.php';
	error_reporting(E_ALL);
	ini_set('display_errors', 'on');
	
	$di = new Di();
	
	$di->instanceManager()->addTypePreference('My\Ib', 'My\B');

	/* $di->instanceManager()->setParameters('My\B', array('a' => function () {
        return new My\A('MyUsernameValue', 'MyHardToGuessPassword');
    })); */
    $di->instanceManager()->setParameters(
		'My\B',  array('a' => array('My\A' => array('password' => 'ddd', 'username' => 'nnnn')))
	);
	$c = $di->get('My\C');
	var_dump($c->b);die;
	
	
	
}