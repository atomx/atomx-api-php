<?php namespace tests;

use Atomx\Resources\Report;
require_once('AtomxAccountStore.php');

class ReportTest extends \PHPUnit_Framework_TestCase {
    public function testCreateAndDownloadReport()
    {
        $from = date('Y-m-d 00:00:00', time() - 24 * 60 * 60);
        $to = date('Y-m-d 00:00:00', time());

        $report = new Report(new AtomxAccountStore());
        $options = array(
            'scope'    => 'advertiser',
            'groups'   => ['campaign_id', 'domain_id', 'day_timestamp'],
            'sums'     => ['impressions', 'clicks', 'conversions', 'campaign_cost'],
            'where'    => [['advertiser_network_id', '==', '1']],
            'from'     => $from,
            'to'       => $to,
            'timezone' => 'UTC'
        );

        //$report = $report->run($options);

        //var_export($report);
    }

    public function testReportIsDone()
    {
        $report = new Report(new AtomxAccountStore());
        $rData = $report->status(Report::getReportId($this->getReportData()));

        var_dump($rData);

        $this->assertEquals(true, Report::isReady($rData));
    }

    public function testDownloadReport()
    {
        $report = new Report(new AtomxAccountStore());
        $rData = $this->getReportData();

        $streamer = $report->download($rData);

        var_dump(Report::numberOfRows($rData));
        var_dump(Report::getColumns($rData));

        var_dump($streamer->readLine());

        $imps = 0;
        $revenue = 0;

        while (($row = $streamer->readLine()) !== false) {
            $imps += $row[3];
            $revenue += $row[6];
        }

        var_dump($imps, $revenue);
    }

    public function testRunAndDownloadReport()
    {
        $from = date('Y-m-d 00:00:00', time() - 24 * 60 * 60);
        $to = date('Y-m-d 00:00:00', time());

        $report = new Report(new AtomxAccountStore());
        $options = array(
            'scope'    => 'advertiser',
            'groups'   => ['campaign_id', 'domain_id', 'day_timestamp'],
            'sums'     => ['impressions', 'clicks', 'conversions', 'campaign_cost'],
            'where'    => [['advertiser_network_id', '==', '1']],
            'from'     => $from,
            'to'       => $to,
            'timezone' => 'UTC'
        );

        $streamer = $report->runAndDownload($options);

        $this->assertNotEquals(false, $streamer);

        var_dump($streamer->readLine());
    }

    /**
     * @return array
     */
    private function getReportData()
    {
        // TODO: Mock the response
        $rData = [
                'success' => true,
                'timestamp' => '2015-09-23T15:30:38.563796',
                'report' =>
                    [
                        'link' => '/v1/report/89130c7cd76c7637195224529bbae9c7',
                        'duration' => NULL,
                        'started' => 1443022238,
                        'lines' => 0,
                        'error' => NULL,
                        'finished' => NULL,
                        'fast' => true,
                        'is_ready' => true,
                        'id' => '89130c7cd76c7637195224529bbae9c7',
                    ],
                'query' =>
                    [
                        'from' => '2015-09-22 00:00:00',
                        'timezone' => 'UTC',
                        'sums' =>
                            array (
                                0 => 'impressions',
                                1 => 'clicks',
                                2 => 'conversions',
                                3 => 'campaign_cost',
                            ),
                        'groups' =>
                            array (
                                0 => 'campaign_id',
                                1 => 'domain_id',
                                2 => 'day_timestamp',
                            ),
                        'to' => '2015-09-23 00:00:00',
                        'where' =>
                            array (
                                0 =>
                                    array (
                                        0 => 'advertiser_network_id',
                                        1 => '==',
                                        2 => '1',
                                    ),
                            ),
                        'scope' => 'advertiser'
                    ]
            ];
        return $rData;
    }
}