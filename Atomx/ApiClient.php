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

    public function clearFields()
    {
        $this->fields = [];
    }

    // GET

    public function get($options = [])
    {
        return $this->getUrl($this->endpoint, $options);
    }

    public function getUrl($url, $query = [], $options = [])
    {
        $options['query'] = $query;

        return $this->request('get', $url, $options);
    }

    // PUT

    public function put($fields = [])
    {
        return $this->putUrl($this->endpoint, ['json' => $fields]);
    }

    public function putUrl($url, $options = [])
    {
        return $this->request('put', $url, $options);
    }

    // POST

    public function post($fields = [])
    {
        return $this->postUrl($this->endpoint, ['json' => $fields]);
    }

    public function postUrl($url, $options = [])
    {
        return $this->request('post', $url, $options);
    }

    public function request($method, $url, $options = [])
    {
        $request = $this->client->createRequest($method, $url, $this->getOptions($options));

        try {
            $response = $this->client->send($request);
        }
        catch (RequestException $e) {
            $response = $e->hasResponse() ? "\nResponse: " . $e->getResponse() : '';

            // TODO: Log this info, do not putUrl it in the exception (as it might leak in a dev version etc)

            throw new ApiException('Request failed: ' . $e->getRequest() . $response);
        }

        // TODO: Be able to reuse the field? or create a new Client every time?
        $this->clearFields();

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
        return [
            'exceptions' => false,
            'timeout'    => 30,
            'connect_timeout' => 10
        ];
    }

    protected function handleResponse(Response $response)
    {
        if ($response->getStatusCode() == 200)
            return $response->getBody()->getContents();

        throw new ApiException('Request failed, received the following status: ' .
            $response->getStatusCode() . ' Body: ' . $response->getBody()->getContents());
    }

    public function __set($name, $value)
    {
        $this->fields[$name] = $value;
    }

    public function __get($name)
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

}
