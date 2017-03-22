<?php namespace Atomx\Resources;

use Atomx\Exceptions\ApiException;
use Atomx\AtomxClient;
use Atomx\ReportStreamer;
use GuzzleHttp\Psr7\Response;

class Report extends AtomxClient {
    private $returnStream = false;

    public function run($json, $timeout = 600)
    {
        $this->returnStream = true;

        $stream = $this->postUrl('report?download', [
            'json'            => $json,
            'timeout'         => $timeout,
            'connect_timeout' => 20
        ]);

        $this->returnStream = false;

        return new ReportStreamer($stream);
    }

    /**
     * @param Response $response
     * @return \Psr\Http\Message\StreamInterface|mixed|null|string
     * @throws ApiException
     */
    protected function handleResponse(Response $response)
    {
        if ($response->getStatusCode() == 200) {
            $stream = $response->getBody();

            if ($this->returnStream == true)
                return $stream;
            else
                return parent::handleResponse($response);
        }

        throw new ApiException('Request failed, received the following status: ' .
            $response->getStatusCode() . ' Body: ' . $response->getBody()->getContents());
    }
}
