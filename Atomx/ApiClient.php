<?php namespace Atomx;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Message\Response;
use InvalidArgumentException;

class ApiClient {
    protected $endpoint;
    protected $apiBase;

    protected $client;

    protected $writable = [];
    protected $fields = [];

    /**
     * @param array $credentials Contains the API endpoint and the username/password
     */
    function __construct()
    {
        $this->client = new Client(['base_url' => $this->apiBase]);
    }

    /**
     * Query the API for a certain field
     */
    public function get($url, $options = [])
    {
        return $this->request('get', $url, $options);
    }

    public function put($url, $options = [])
    {
        return $this->request('put', $url, $options);
    }

    public function request($method, $url, $options = [])
    {
        $options = $this->getOptions($options);

        $request = $this->client->createRequest($method, $url, $options);

        try {
            $response = $this->client->send($request);
         } catch (RequestException $e) {
            $response = $e->hasResponse() ? $e->getResponse() : '';

            // TODO: Log this info, do not put it in the exception (as it might leak in a dev version etc)
            throw new Exception('Request failed: ' . $e->getRequest() . "\nResponse: " . $response);
        }

        return $this->handleResponse($response);
    }

    /**
     * @param $options
     * @return array
     */
    private function getOptions($options)
    {
        $defaultOptions = $this->getDefaultOptions();
        // TODO: Use a merge that overrides already set values
        $options = array_merge_recursive($defaultOptions, $options);

        return $options;
    }

    protected function getDefaultOptions()
    {
        return [];
    }

    protected function handleResponse(Response $response)
    {
        if ($response->getStatusCode() == 200)
            return $response->getBody()->getContents();

        throw new Exception('Request failed, received the following status: ' .
            $response->getStatusCode() . ' Body: ' . $response->getBody()->getContents());
    }

    /*j
    function __set($name, $value)
    {
        if ($this->isWritable($name)) {
            $field = $this->writable[$name];

            $this->fields[$name] = $value;
        }
        else
            throw new InvalidArgumentException("API: Field $name does not exist in class.");
    }

    function __get($name)
    {
        if ($this->isWritable($name)) {
            $field = $this->writable[$name];

            if (isset($this->fields[$field]))
                return $this->fields[$name];
            else
                return null;
        }

        throw new InvalidArgumentException("API: Field $name does not exist in class.");
    }
    */

    public function update($fields)
    {
        // Fetch the fields and see what individual updates we can do with a single request
        $this->fields = $fields;
    }

    public function commit()
    {
        return $this->put($this->endpoint, $this->fields);
    }

    /**
     * @param $name
     * @return bool
     */
    private function isWritable($name)
    {
        return array_key_exists($name, $this->writable);
    }
}
