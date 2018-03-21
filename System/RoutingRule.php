<?php

namespace Bajdzis\System;

use Bajdzis\System\Uri;

class RoutingRule
{
    private $path;
    private $function;

    function __construct(Uri $uri, $function)
    {
        $this->uri = $uri;
        $this->function = $function;
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

        $functionName = $this->function;
        return $functionName($relativeParams, $executeUri);
    }
}
