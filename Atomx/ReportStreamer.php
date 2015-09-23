<?php namespace Atomx;

use Atomx\AccountStore;
use GuzzleHttp\Stream\Stream;


class ReportStreamer {
    private $stream;

    private $overflow = '';
    private $columns = [];
    private $count = 0;

    public function __construct(Stream $stream, $report = null)
    {
        if (!is_null($report)) {
            $sumsOrMetrics = (isset($report['report']['sums']) ? $report['report']['sums'] : $report['report']['metrics']);
            $this->columns = array_merge($report['report']['groups'], $sumsOrMetrics);
            $this->count = $report['report']['count'];
        }

        $this->stream = $stream;
    }

    public function getOverflow()
    {
        return $this->overflow;
    }

    public function readLine()
    {
        $buffer = $this->overflow;

        $this->overflow = '';

        if (empty($buffer)) {
            if ($this->stream->eof())
                return false;
        }

        while (($break = strpos($buffer, "\n")) === false && !$this->stream->eof()) {
            $buffer .= $this->stream->read(1024);
        }

        if ($break !== false) {
            if (strlen($buffer) > $break+1)
                $this->overflow = substr($buffer, $break+1);

            $buffer = substr($buffer, 0, $break);
        }
        $line = str_getcsv($buffer, "\t");

        if (count($line) == 0)
            return false;

        return $line;
    }
}
