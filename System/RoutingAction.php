<?php

namespace Bajdzis\System;

use Bajdzis\System\Uri;

class RoutingAction
{
    const CONTINUE_WORK = false;
    const DONE_WORK = true;

    protected $currentUri;
    protected $relativeParams;

    public function setCurrentUrl(Uri $currentUri)
    {
        $this->currentUri = $currentUri;
    }

    public function setRelativeParams(array $relativeParams)
    {
        $this->relativeParams = $relativeParams;
    }

    public function execute()
    {
        return self::CONTINUE_WORK;
    }
}
