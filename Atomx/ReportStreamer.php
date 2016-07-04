<?php namespace Atomx;

use Atomx\TokenStore;
use GuzzleHttp\Stream\Stream;


class ReportStreamer {
    private $stream;
    private $overflow = '';

    public function __construct(Stream $stream, $report = null)
    {
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
