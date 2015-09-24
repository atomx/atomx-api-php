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

    public static function getReportId($report)
    {
        if (isset($report['report']['id'])) {
            return $report['report']['id'];
        }

        return false;
    }

    public static function isReady($report)
    {
        if (isset($report['report']['is_ready'])) {
            return $report['report']['is_ready'];
        }

        return false;
    }

    public static function numberOfRows($report)
    {
        if (isset($report['report']['lines']))
            return $report['report']['lines'];

        return false;
    }

    public static function getColumns($report)
    {
        if (!is_null($report) && isset($report['query'])) {
            $sumsOrMetrics = (isset($report['query']['sums']) ? $report['query']['sums'] : $report['query']['metrics']);

            return array_merge($report['query']['groups'], $sumsOrMetrics);
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
                return parent::handleResponse($response);
        }

        throw new ApiException('Request failed, received the following status: ' .
            $response->getStatusCode() . ' Body: ' . $response->getBody()->getContents());
    }
}
