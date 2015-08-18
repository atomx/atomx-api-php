<?php namespace tests;


use Atomx\AccountStore;
use Atomx\Resources\Report;

class AtomxAccountStore implements AccountStore {
    private $token = null;

    public function getToken()
    {
        return $this->token;
    }

    public function storeToken($token)
    {
        $this->token = $token;
    }

    public function getUsername()
    {
        return '';
    }

    public function getPassword()
    {
        return '';
    }

    public function getApiBase()
    {
        return '';
    }
}

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
            'where'    => [['network_id', '==', '1']],
            'from'     => $from,
            'to'       => $to,
            'timezone' => 'UTC'
        );

       // $report = $report->run($options);

        //var_dump($report);
    }

    public function testReportIsDone()
    {
        $report = new Report(new AtomxAccountStore());
        $rData = $this->getReportData();

        $this->assertEquals(true, $report->isReady($rData));
    }

    public function testDownloadReport()
    {
        $report = new Report(new AtomxAccountStore());
        $rData = $this->getReportData();

        $streamer = $report->download($rData);
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
            'timestamp' => "2015-08-18T09:13:15.330011",
            'report' => [
                'error' => NULL,
                'is_ready' => true,
                'link' => "/v1/report/7aae3048e3f54c327163c5ef25826abc",
                'lines' => 30003,
                'id' => "7aae3048e3f54c327163c5ef25826abc",
                'started' => 1439889079
            ]
        ];
        return $rData;
    }
}