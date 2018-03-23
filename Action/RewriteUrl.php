<?php

namespace Bajdzis\Action;

class RewriteUrl
{

    public static function execute($relativeParam, $url)
    {
        
        $linkOriginal = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $linkValidate = $url->getUri();

        if ($linkOriginal !== $linkValidate) {
            header('HTTP/1.1 301 Moved Permanently');
            header('Location: '.$linkValidate);
            return true;
        }

        return false;
    }

}
