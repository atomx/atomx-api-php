<?php namespace tests;

use Atomx\Resources\Report;
require_once('AtomxAccountStore.php');

class ReportTest extends \PHPUnit_Framework_TestCase {
    public function testCreateAndDownloadReport()
    {
        $from = date('Y-m-d 00:00:00', time() - 24 * 60 * 60);
        $to = date('Y-m-d 00:00:00', time());

        $report = new Report(new AtomxAccountStore());
        $options = [
            'scope'    => 'network_buy',
            'groups'   => ['day', 'campaign_id', 'creative_id', 'country_id'],
            'metrics'  => ['impressions', 'clicks', 'conversions', 'campaign_cost', 'advertiser_network_profit'],
            'where'    => [['advertiser_network_id', '==', '1']],
            'from'     => $from,
            'to'       => $to,
            'timezone' => 'UTC'
        ];

        $report = $report->run($options);

        $this->assertEquals([
            'day',
            'campaign_id',
            'creative_id',
            'country_id',
            'impressions',
            'clicks',
            'conversions',
            'campaign_cost',
            'advertiser_network_profit'
        ], $report->readLine());
    }
}
