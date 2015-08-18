<?php namespace tests;


use Atomx\ReportStreamer;
use GuzzleHttp\Stream\Stream;

class ReportStreamerTest extends \PHPUnit_Framework_TestCase {
    public function testGetLineAndOverflow()
    {
        $stream = Stream::factory("test\t123\ntest2\t123");

        $streamer = new ReportStreamer($stream);

        $line = $streamer->readLine();
        $line2 = $streamer->readLine();

        $this->assertEquals(['test', 123], $line);
        $this->assertEquals(['test2', 123], $line2);
    }

    public function testBreakEnd()
    {
        $stream = Stream::factory("test\t\ntest2\n");

        $streamer = new ReportStreamer($stream);

        $line = $streamer->readLine();
        $line2 = $streamer->readLine();

        $this->assertEquals(["test", ''], $line);
        $this->assertEquals(["test2"], $line2);
    }
}