<?php namespace Atomx;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Message\Response;

class ApiClient {
    protected $endpoint;
    protected $apiBase;

    protected $client;

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
        $request = $this->client->createRequest($method, $url, $this->getOptions($options));

        try {
            $response = $this->client->send($request);
        }
        catch (RequestException $e) {
            $response = $e->hasResponse() ? $e->getResponse() : '';

            // TODO: Log this info, do not put it in the exception (as it might leak in a dev version etc)

            throw new ApiException('Request failed: ' . $e->getRequest() . "\nResponse: " . $response);
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
        return ['exceptions' => false];
    }

    protected function handleResponse(Response $response)
    {
        if ($response->getStatusCode() == 200)
            return $response->getBody()->getContents();

        throw new ApiException('Request failed, received the following status: ' .
            $response->getStatusCode() . ' Body: ' . $response->getBody()->getContents());
    }

    function __set($name, $value)
    {
        $this->fields[$name] = $value;
    }

    function __get($name)
    {
        if (isset($this->fields[$name]))
            return $this->fields[$name];
        else
            return null;
    }

    public function fill($fields)
    {
        // TODO: Merge with the other fields
        $this->fields = $fields;
    }

    public function update()
    {
        return $this->put($this->endpoint, $this->fields);
    }

    public function create()
    {
        return $this->post($this->endpoint, $this->fields);
    }
}
