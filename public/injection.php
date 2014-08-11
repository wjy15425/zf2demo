<?php
namespace My {

use Zend\Validator\Iban;
    /*     //src1
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
    
    /*
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
    */
    
    
    //src3
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
/*     class B implements Ib
    {
        protected $a = null;
        public function __construct(A $a)
        {
            $this->a = $a;
        }
    } */
    class B implements Ib
    {
        protected $a;
        public function setA($a)
        {
            $this->a = $a;
        }
    }
    class C
    {
        protected $b = null;
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
    
/*     //src1-use
    $di = new Zend\Di\Di;
    $b1_1 = $di->get('My\B');
    $b1_2 = $di->get('My\B');
    var_dump($b1_1===$b1_2);//true
    $b2_1 = $di->newInstance('My\B');
    $b2_2 = $di->newInstance('My\B');
    var_dump($b2_1===$b1_2);//false */
    
    /*
    //src2-use
    $di = new Zend\Di\Di;
    $di->instanceManager()->setParameters('My\A', array('username'=>'MyUsernameValue', 'password'=>'MyHardToGuessPassword%$#'));
    $c = $di->get('My\C');
    //or
    $c = $di->newInstance('My\C');
    var_dump($c);
    */
    
    //src3-use
//     $im = new Builder\InjectionMethod;
//     $im->setName('setA');
//     $im->addParameter('a', 'My\A', true);
//     $class = new Builder\PhpClass;
//     $class->setName('My\B');
//     $class->addInjectionMethod($im);
    
//     $builder = new Definition\BuilderDefinition;
//     $builder->addClass($class);
    
    
//     $aDefList = new DefinitionList($builder);
    //$aDefList->addDefinition(new Definition\RuntimeDefinition);

    // Now make sure the Di understands it
    $di = new Di();
    
/*     $di->instanceManager()->setParameters('My\A', array(
        'username' => 'MyUsernameValue',
        'password' => 'MyHardToGuessPassword%$#'
    ));
    $c = $di->get('My\C');
    var_dump($c);
    
    $di->instanceManager()->setParameters('My\B', array(
        'a' => array(
            'username' => 'MyUsernameValue',
            'password' => 'MyHardToGuessPassword%$#'
        ))
    );
    $c = $di->get('My\C');
    var_dump($c);
    */

    $di->instanceManager()->addTypePreference('My\Ib', 'My\B');
    
    //instantiator __construct 可识别
    $c = $di->get('My\C', array(
        'username' => 'MyUsernameValue',
        'password' => 'MyHardToGuessPassword%$#',
    ));
    
/*     $c = $di->get('My\C',  array(
        'a' => array('MyUsernameValue', 'MyHardToGuessPassword%$#')
    ));
    var_dump($c);
    
    $c = $di->get('My\C', array(
        'a' => new My\A('MyUsernameValue', 'MyHardToGuessPassword%$#')
    ));
    var_dump($c); 
    

    $di->instanceManager()->setParameters(
            'My\B',  array('a' => array(
                    'username' => 'MyUsernameValue',
                    'password' => 'MyHardToGuessPassword%$#',
            )
            )
    );

	var_dump($c);die;}