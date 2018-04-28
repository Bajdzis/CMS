<?php

namespace Bajdzis\System;

/**
 * Klasa odwzorująca linki w systemie CMS.
 */
class Uri
{
    private $scheme;
    private $domain;
    private $subDomain;
    private $params;

    function __construct(string $uri = null)
    {
        if ($uri === null) {

            return;
        }
        $matches = $this->parseUri($uri);
        $this->setScheme($matches['scheme']);
        $this->setHost($matches['host']);
        if(isset($matches['path'])){
            $this->setParamsFromString($matches['path']);
        }
    }

    public function compare(Uri $uri): array
    {
        return [
            'domain' => $this->compareDomain($uri),
            'subDomain' => $this->compareSubDomain($uri),
            'scheme' => $this->compareScheme($uri),
            'params' => $this->compareParams($uri)
        ];
    }

    public function setDomain(string $domain)
    {
        $this->domain = $domain;
    }

    public function getDomain()
    {
        return $this->domain;
    }

    public function compareDomain(Uri $uri): bool
    {
        return $this->getDomain() === $uri->getDomain();
    }

    public function setSubDomain(string $subDomain)
    {
        $this->subDomain = $subDomain;
    }

    public function getSubDomain()
    {
        return strlen($this->subDomain) === 0 ? null : $this->subDomain;
    }

    public function compareSubDomain(Uri $uri): bool
    {
        return $this->getSubDomain() === $uri->getSubDomain();
    }

    public function setHost($host)
    {
        if($host === 'localhost'){
            $this->setDomain('localhost');
            return;
        }
        $result = preg_match("/(((?<subDomain>[^\/]*)\.)?)(?<domain>[^\.\/]*\.[^\.\/]*)/", $host, $matches);
        $this->setDomain($matches['domain']);
        $this->setSubDomain($matches['subDomain']);
    }

    public function setScheme(string $scheme)
    {
        $this->scheme = $scheme;
    }

    public function getScheme()
    {
        return $this->scheme;
    }

    public function compareScheme(Uri $uri): bool
    {
        return $this->getScheme() === $uri->getScheme();
    }

    public function setParams(array $params)
    {
        foreach ($params as $key => $param) {
            $params[$key] = urldecode($param);
        }
        $this->params = $params;
    }

    public function getParams(): array
    {
        return $this->params ?? [];
    }

    public function compareParams(Uri $uri): bool
    {
        return $this->getJoinParams() === $uri->getJoinParams();
    }

    public function getUri() : string
    {
        return $this->getScheme().':'.$this->getLink();
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

    public function setParamsFromString(string $paramsString)
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

    protected function parseUri(string $uri)
    {
        $result = parse_url($uri);
        if ($result === false) {
            throw new \InvalidArgumentException("parseUri only accepts correct url. Input was : ".$uri);
        }

        return $result;
    }

    protected function getJoinParams() : string
    {
        $params = $this->getParams();
        foreach ($params as $key => $param) {
            $params[$key] = urlencode($param);
        }

        return implode('/', $params);
    }

    public static function getCurrentUri()
    {
        $uri = new Uri();
        $uri->setScheme($_SERVER['REQUEST_SCHEME']);
        $uri->setParamsFromString($_SERVER['REQUEST_URI']);
        $uri->setHost($_SERVER['HTTP_HOST']);

        return $uri;
    }
}
