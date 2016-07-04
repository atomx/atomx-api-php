<?php namespace Atomx\Exceptions;

class TotpRequiredException extends ApiException {
    protected $response = null;

    public function __construct($response)
    {
        $this->response = $response;
    }

    public function getResponse()
    {
        return $this->response;
    }
}
