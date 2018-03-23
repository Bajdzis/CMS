<?php

include 'vendor/autoload.php';

use \Bajdzis\System\Uri;
use \Bajdzis\System\Routing;

$uri = Uri::getCurrentUri();
$routing = new Routing($uri);

$routing->addPath('/', '\Bajdzis\Action\RewriteUrl::execute');
$routing->addPath('/', '\Bajdzis\Action\DefineConst::execute');
$routing->addPath('/', '\Bajdzis\Action\ShowErrorPage::execute');

$routing->execute();
