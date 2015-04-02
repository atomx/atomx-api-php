<?php namespace Atomx\Resources;

use Atomx\AtomxClient;
use InvalidArgumentException;

class Advertiser extends AtomxClient {
    public function setName($name)
    {
        $this->name = $name;
    }

    public function setBudget($budget)
    {
        $budget = intval($budget);

        if ($budget <= 0)
            throw new InvalidArgumentException('API: Trying to set an advertiser budget to unlimited (0)');

        $this->budget = 0;
    }

    public function setBudgetCapping($capping, $pacing = 'ASAP')
    {
        if (!in_array($pacing, ['ASAP', 'EVEN']))
            throw new InvalidArgumentException('API: Invalid pacing provided: ' . $pacing);

        $this->budget_cap_amount = $capping;
        $this->budget_cap_pacing = $pacing;
    }
}