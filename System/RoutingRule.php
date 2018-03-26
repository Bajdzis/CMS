<?php

namespace Bajdzis\System;

use \Bajdzis\System\Uri;
use \Bajdzis\System\RoutingAction;

class RoutingRule
{
    private $path;
    private $className;

    function __construct(Uri $uri, $className)
    {
        $this->uri = $uri;
        $this->className = $className;
    }

    public function execute(Uri $executeUri)
    {
        $compareResult = $this->uri->compare($executeUri);

        if (($compareResult['domain'] && $compareResult['subDomain'] && $compareResult['scheme']) === false) {
            return false;
        }

        $roleParams = $this->uri->getParams();
        $executeParams = $executeUri->getParams();

        if(count($executeParams) < count($roleParams)){
            return false;
        }

        foreach ($roleParams as $key => $value) {
            if($roleParams[$key] !== $executeParams[$key]){
                return false;
            }
        }

        $relativeParams = array_slice($executeParams, count($roleParams) );

        $className = $this->className;
        $classInstance = new $className();
        if(($classInstance instanceof RoutingAction) === false){
            throw new \Exception("Error '$className' is not valid RoutingAction instance", 1);
        }
        $classInstance->setCurrentUrl($executeUri);
        $classInstance->setRelativeParams($relativeParams);
        return $classInstance->execute();
    }
}
