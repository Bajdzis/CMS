<?php

namespace Bajdzis\Action;

use \Bajdzis\System\RoutingAction;

class RewriteUrl extends RoutingAction
{

    public function execute()
    {
        
        $linkOriginal = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $linkValidate = $this->currentUri->getUri();

        if ($linkOriginal !== $linkValidate) {
            header('HTTP/1.1 301 Moved Permanently');
            header('Location: '.$linkValidate);
            return RoutingAction::DONE_WORK;
        }

        return RoutingAction::CONTINUE_WORK;
    }

}
