<?php namespace Atomx\Resources;

use Atomx\ApiException;
use Atomx\AtomxClient;
use Atomx\ReportStreamer;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;

class Report extends AtomxClient {
    private $returnStream = false;

    public function run($json, $timeout = 600)
    {
        $this->returnStream = true;

        $stream = $this->postUrl('report?download', compact('json'), [
            'timeout'         => $timeout,
            'connect_timeout' => 20
        ]);

        $this->returnStream = false;

        return new ReportStreamer($stream);
    }

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
