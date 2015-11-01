<?php
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Http\Headers;

ini_set('display_errors', 1);
include '../vendor/autoload.php';

$request = Request::fromString(<<<EOS
POST http://localhost.com/test.php HTTP/1.1
\r\n
header1: header1_val
header2: header1_val
\r\n
\r\n
page=1&order=time
EOS
);

$request = new Request();
$request->setMethod(Request::METHOD_POST);
$request->setUri('http://localhost.com/test.php');
$request->getHeaders()->addHeaders(array(
    'header1' => 'header1_val',
    'header2' => 'header2_val',
));
$request->getPost()->set('page', 1);
$request->getPost()->order = 'time';
$request->getQuery()->query1 = 'quer1_val';
$request->setContent($request->getQuery()->toString());
var_dump($request->getQuery('query1'));
echo $request->toString();

echo "\r\n---------Response---------\r\n";
$response = Response::fromString(<<<EOS
HTTP/1.1 200 ok
header2: header2_val
header1: header1_val

<html>
<body>
    Hello World
</body>
</html>
EOS
);

$response = new Response();
$response->setStatusCode(200);
$request->getHeaders()->addHeaders(array(
    'header1' => 'header1_val',
    'header2' => 'header2_val',
));
$response->setContent(<<<EOS
<html>
<body>
    Hello World
</body>
</html>
EOS
);

// getBody 为 由 getContent 经过相关的处理得到
var_dump($response->getBody(), $response->getContent());

$headers = Headers::fromString(<<<EOS
Host: baidu
Content-Type: text/html
Content-Length: 213
EOS
);
echo "\nContent-Type:" . $headers->get('Content-Type')->getFieldValue();
