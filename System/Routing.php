<?php

namespace Bajdzis\System;

use Bajdzis\System\Uri;
use Bajdzis\System\RoutingRule;

class Routing
{
    private $pathsList = [];

    function __construct()
    {
        
    }

    public function addPath($path, $function)
    {
        $uri = Uri::getCurrentUri();
        $uri->setParamsFromString($path);

        return $this->addUri($uri, $function);
    }

    public function addUri(Uri $uri, $function)
    {
        $role = new RoutingRule($uri, $function);
        $this->pathsList[] = $role;
        
        return $role;
    }

    public function execute(Uri $executeUri)
    {
        foreach ($this->pathsList as $role) {
            $result = $role->execute($executeUri);
            if($result === true){
                break;
            }
        }
    }
}


