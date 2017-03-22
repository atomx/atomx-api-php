<?php namespace Atomx;

use Atomx\Exceptions\ApiException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\StreamInterface;

class ApiClient {
    protected $endpoint;
    protected $apiBase;

    protected $client;

    protected $fields = [];

    /**
     * @param HandlerStack $handler
     */
    function __construct($handler = null)
    {
        $this->client = new Client([
            'base_uri' => $this->apiBase,
            'handler' => $handler]
        );
    }

    public function getClient()
    {
        return $this->client;
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

    /**
     * @param $url
     * @param array $options
     * @return string|StreamInterface
     * @throws ApiException
     */
    public function postUrl($url, $options = [])
    {
        return $this->request('post', $url, $options);
    }

    public function request($method, $url, $options = [])
    {
        try {
            $request = new Request($method, $url);
            $response = $this->client->send($request, $this->getOptions($options));
        }
        catch (RequestException $e) {
            $response = $e->hasResponse() ? "\nResponse: " . $e->getResponse() : '';

            throw new ApiException("Request failed: " . $e->getMessage() . "\nRequest:" . $e->getRequest() . $response);
        }

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

        $options = array_replace_recursive($defaultOptions, $options);

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

    /**
     * @param Response $response
     * @return mixed
     * @throws ApiException
     */
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
        $this->fields = $fields;
    }

}
