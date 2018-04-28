<?php

namespace Bajdzis\System;

use Bajdzis\System\Uri;
use Bajdzis\System\RoutingRule;
use Bajdzis\System\RoutingAction;

class Routing
{
    private $pathsList = [];
    private $currentUri;

    function __construct(Uri $currentUri)
    {
        $this->currentUri = $currentUri;
    }

    public function addPath($path, $function)
    {
        $uri = clone $this->currentUri;
        $uri->setParamsFromString($path);

        return $this->addUri($uri, $function);
    }

    public function addUri(Uri $uri, $function)
    {
        $role = new RoutingRule($uri, $function);
        $this->pathsList[] = $role;
        
        return $role;
    }

    public function execute()
    {
        foreach ($this->pathsList as $role) {
            $result = $role->execute($this->currentUri);
            if($result === RoutingAction::DONE_WORK){
                break;
            }
        }
    }
}


