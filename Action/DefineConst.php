<?php

namespace Bajdzis\Action;

class DefineConst
{

    public static function execute($relativeParam, $url)
    {
        define ('IS_LOCALHOST', $_SERVER['HTTP_HOST'] == 'localhost');
        define ('DEBUG_MODE', false);
        return false;
    }

}
