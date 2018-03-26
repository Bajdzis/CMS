<?php

namespace Bajdzis\Action;

use \Bajdzis\System\RoutingAction;

class DefineConst extends RoutingAction
{

    public function execute()
    {
        define ('DEBUG_MODE', false);
        return RoutingAction::CONTINUE_WORK;
    }

}
