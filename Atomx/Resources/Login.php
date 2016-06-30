<?php namespace Atomx\Resources;

use Atomx\ApiException;
use Atomx\AtomxClient;

class Login extends AtomxClient {
    protected $endpoint = 'login';
    protected $requiresToken = false;

    /**
     * @param array $fields contains the username/password for the user
     * @return string
     * @throws ApiException
     */
    public function login(array $fields)
    {
        try{
            $response = $this->post($fields);
        } catch (ApiException $e) {
            $message = $e->getMessage();

            if (isset($fields['password']))
                $message = str_replace($this->accountStore->getPassword(), '[redacted]', $message);

            throw new ApiException('Unable to login to API! Message: ' . $message);
        }

        if ($response['success'] !== true)
            throw new ApiException('Unable to login to API. Message: ' . $response['message']);

        return $response;
    }
}
