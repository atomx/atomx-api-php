<?php namespace tests;


use Atomx\ReportStreamer;
use GuzzleHttp\Stream\Stream;

class ReportStreamerTest extends \PHPUnit_Framework_TestCase {
    public function testGetLineAndOverflow()
    {
        $stream = Stream::factory("test\t123\ntest2\t456");

        $streamer = new ReportStreamer($stream, false);

        $line = $streamer->readLine();
        $line2 = $streamer->readLine();

        $this->assertEquals(['test', 123], $line);
        $this->assertEquals(['test2', 456], $line2);
    }

    public function testBreakEnd()
    {
        $stream = Stream::factory("col1\tcol2\ntest\t\ntest2\n");

        $streamer = new ReportStreamer($stream);

        $line = $streamer->readLine();
        $line2 = $streamer->readLine();

        $this->assertEquals(["test", ''], $line);
        $this->assertEquals(["test2"], $line2);
    }

    public function testNextAndColumns()
    {
        $stream = Stream::factory(fopen('files/report.tsv', 'rb'));

        $streamer = new ReportStreamer($stream);


        $this->assertTrue($streamer->next());
        $this->assertEquals('Spain', $streamer->country_name);
        $this->assertEquals(2529, $streamer->impressions);
        $totalProfit = $streamer->advertiser_network_profit;

        $this->assertTrue($streamer->next());
        $this->assertEquals('Advertiser2 (19)', $streamer->advertiser_name_id);
        $this->assertEquals(0.00037999998312443495, $streamer->advertiser_network_profit);
        $totalProfit += $streamer->advertiser_network_profit;

        $this->assertEquals(0.06214054941665381495, $totalProfit);
        $this->assertFalse($streamer->next());

    }

    public function testNextWithEmpty()
    {
        $stream = Stream::factory("");

        $streamer = new ReportStreamer($stream);

        $this->assertFalse($streamer->next());
    }
}
