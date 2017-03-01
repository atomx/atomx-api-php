<?php namespace Atomx;



use GuzzleHttp\Psr7\Stream;

class ReportStreamer
{
    private $stream;
    private $overflow = '';
    protected $columns = [];
    protected $line;

    public function __construct(Stream $stream, $hasColumns = true)
    {
        $this->stream = $stream;

        if ($hasColumns)
            $this->readColumns();
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
            if (strlen($buffer) > $break + 1)
                $this->overflow = substr($buffer, $break + 1);

            $buffer = substr($buffer, 0, $break);
        }

        if (empty($buffer))
            return false;

        $line = str_getcsv($buffer, "\t");

        return $line;
    }

    protected function readColumns()
    {
        // Read the first line of columns
        $this->line = $this->readLine();

        if ($this->line)
            $this->columns = array_flip($this->line);
    }

    public function __get($column)
    {
        return $this->line[$this->columns[$column]];
    }

    public function next()
    {
        $this->line = $this->readLine();

        return !!$this->line;
    }
}
