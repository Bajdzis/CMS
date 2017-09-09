<?php

namespace Bajdzis\System;

/**
 * Klasa odwzorująca linki w systemie CMS.
 */
class Url
{
    private $url;
    private $protocol;
    private $domain;
    private $subDomain;
    private $params;

    function __construct(string $url = null)
    {
        if ($url === null) {

            return $this;
        }
        $matches = $this->parseUrl($url);
        $this->setProtocol($matches['protocol']);
        $this->setDomain($matches['domain']);
        $this->setSubDomain($matches['subDomain']);
        $this->setParamsFromString($matches['params']);
    }

    public function setDomain(string $domain)
    {
        $this->domain = $domain;
    }

    public function getDomain()
    {
        return $this->domain;
    }

    public function setSubDomain(string $subDomain)
    {
        $this->subDomain = $subDomain;
    }

    public function getSubDomain()
    {
        return strlen($this->subDomain) === 0 ? null : $this->subDomain;
    }

    public function setProtocol(string $protocol)
    {
        $this->protocol = $protocol;
    }

    public function getProtocol()
    {
        return $this->protocol;
    }

    public function setParams(array $params)
    {
        foreach ($params as $key => $param) {
            $params[$key] = urldecode($param);
        }
        $this->params = $params;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function getUrl() : string
    {
        return $this->getProtocol().':'.$this->getLink();
    }

    public function getLink() : string
    {
        $subDomain = $this->getSubDomain();
        if($subDomain === 'www'){
            $subDomain = null;
        }
        if($subDomain){
            $subDomain .= '.';
        }
        $domain = $this->getDomain();
        $params = $this->getJoinParams();

        return "//$subDomain$domain/$params/";
    }

    protected function setParamsFromString(string $paramsString)
    {
        $params = explode('/', $paramsString);
        if(end($params) === '' ){
            array_pop($params);
        }
        if(isset($params[0]) && $params[0] === '' ){
            array_shift($params);
        }
        $this->setParams($params);
    }

    protected function parseUrl(string $url)
    {
        $result = preg_match("/(?<protocol>[a-z]*):\/\/(((?<subDomain>[^\/]*)\.)?)(?<domain>[^\:\.\/]*\.[^\:\.\/]*)((\:(?<port>[0-9]*))?)(\/{0,1}(?<params>.*))/", $url, $matches);
        if($result === 0){
            throw new \InvalidArgumentException("parseUrl only accepts correct url. Input was : ".$url);
        }

        return $matches;
    }

    protected function getJoinParams() : string
    {
        $params = $this->getParams();
        foreach ($params as $key => $param) {
            $params[$key] = urlencode($param);
        }

        return implode('/', $params);
    }

    public static function getCurrentUrl()
    {
        $url = new Url();
        $result = preg_match("/(((?<subDomain>[^\/]*)\.)?)(?<domain>[^\.\/]*\.[^\.\/]*)/", $_SERVER['HTTP_HOST'], $matches);
        $url->setProtocol($_SERVER['REQUEST_SCHEME']);
        $url->setParamsFromString($_SERVER['REQUEST_URI']);
        $url->setDomain($matches['domain']);
        $url->setSubDomain($matches['subDomain']);

        return $url;
    }
}
