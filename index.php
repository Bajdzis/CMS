<?php

include 'vendor/autoload.php';

use \Bajdzis\System\Uri;
use \Bajdzis\System\Routing;

$uri = Uri::getCurrentUri();
$routing = new Routing($uri);

$routing->addPath('/', '\Bajdzis\Action\RewriteUrl');
$routing->addPath('/', '\Bajdzis\Action\DefineConst');
$routing->addPath('/', '\Bajdzis\Action\ShowErrorPage');

$routing->execute();
