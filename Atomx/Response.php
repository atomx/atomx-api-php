<?php namespace Atomx;

class Response {

    private $namespace;
    private $response;

    public function __construct($response)
    {
        $this->response = $response;
        $this->namespace = $response['resource'];
    }

    public function isValid()
    {
        return $this->response['success'] == true;
    }

    public function isMultiple()
    {
        return isset($this->response['count']);
    }

    public function count()
    {
       return count($this->response['count']);
    }

    public function __get($key)
    {
        return $this->response[$this->namespace][$key];
    }
}