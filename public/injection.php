<?php
namespace My {

/* 	//src1
	class A
	{
		// Some useful functionality
	}

	class B
	{
		protected $a = null;
		public function __construct(A $a)
		{
			$this->a = $a;
		}
	} */

	
	//src2
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
	
	class B
	{
		protected $a = null;
		public function __construct(A $a)
		{
			$this->a = $a;
		}
	}
	
	class C
	{
		protected $b = null;
		public function __construct(B $b)
		{
			$this->b = $b;
		}
	}
}
namespace {
	chdir(dirname(__DIR__));
	require 'init_autoloader.php';
	error_reporting(E_ALL);
	ini_set('display_errors', 'on');
	
/* 	//src1-use
	$di = new Zend\Di\Di;
	$b1_1 = $di->get('My\B');
	$b1_2 = $di->get('My\B');
	var_dump($b1_1===$b1_2);//true
	$b2_1 = $di->newInstance('My\B');
	$b2_2 = $di->newInstance('My\B');
	var_dump($b2_1===$b1_2);//false */
	
	
	//src2-use
	$di = new Zend\Di\Di;
	$di->instanceManager()->setParameters('My\A', array('username'=>'MyUsernameValue', 'password'=>'MyHardToGuessPassword%$#'));
	$c = $di->get('My\C');
	//or
	$c = $di->newInstance('My\C');
	var_dump($c);
	
}