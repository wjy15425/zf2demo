<?php
use Zend\Filter\StringToUpper;
use Zend\Config\Processor\Filter as FilterProcessor;
use Zend\Config\Config;

include '../vendor/autoload.php';
$config = array(
    'version'  => 'v1.0.0.1',
    'webhost'  => 'www.example.com',
    'database' => array(
        'adapter' => 'pdo_mysql',
        'params'  => array(
            //'host'     => 'db.example.com',
            'username' => 'dbuser',
            'password' => 'secret',
            'dbname'   => 'mydatabase'
        )
    )
);
$config = new Config($config, true);//arrowModifications
echo $config->database->get('params')->get('host','127.0.0.1'),"<br/>\n";

// Zend\Config\Writer\Ini
// Zend\Config\Writer\Xml
// Zend\Config\Writer\PhpArray
// Zend\Config\Writer\Json
// Zend\Config\Writer\Yaml
$writer = new Zend\Config\Writer\PhpArray();
// AbstractWriter->toString[PhpArray->processConfig]
$writer->toFile('/temp.config', $config, true);

//Processor: Contant(常量解析) Filter Queue(多个Processor) Token 
$upper = new StringToUpper();
$upperProcessor = new FilterProcessor($upper);
echo $config->version,',';
$upperProcessor->process($config);
echo $config->version,"<br/>\n";