<?php namespace Atomx\Resources;

use Atomx\ApiException;
use Atomx\AtomxClient;
use Atomx\ReportStreamer;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;

class Report extends AtomxClient {
    private $returnStream = false;

    // Run report
    public function run($json)
    {
        return $this->postUrl('report', compact('json'));
    }

    public function status($reportId)
    {
        return $this->getUrl('report/' . $reportId, ['status' => true]);
    }

    public function isReady($report)
    {
        if (isset($report['report']['is_ready'])) {
            return $report['report']['is_ready'];
        }

        return false;
    }

    public function download($report)
    {
        $reportId = $report['report']['id'];

        $this->returnStream = true;

        $stream = $this->getUrl('report/' . $reportId, [], [
//            'timeout'         => 0,
//            'connect_timeout' => 0
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
                return $stream->getContents();
        }

        throw new ApiException('Request failed, received the following status: ' .
            $response->getStatusCode() . ' Body: ' . $response->getBody()->getContents());
    }
}
