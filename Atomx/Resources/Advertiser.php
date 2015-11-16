<?php namespace Atomx\Resources;

use Atomx\AtomxClient;
use Atomx\Resources\Traits\NameTrait;
use Atomx\Resources\Traits\StateTrait;
use InvalidArgumentException;

class Advertiser extends AtomxClient {
    use NameTrait, StateTrait;

    protected $endpoint = 'advertiser';

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setState($state)
    {
        if (!in_array($state, ['active', 'inactive']))
            throw new InvalidArgumentException('API: Trying to set an invalid state for the advertiser');

        $this->state = strtoupper($state);
    }

    public function setBudget($budget)
    {
        $budget = intval($budget);

        if ($budget <= 0)
            throw new InvalidArgumentException('API: Trying to set an advertiser budget to unlimited (0)');

        $this->budget = $budget;
    }

    public function setBudgetCapping($capping, $pacing = 'ASAP')
    {
        if (!in_array($pacing, ['ASAP', 'EVEN']))
            throw new InvalidArgumentException('API: Invalid pacing provided: ' . $pacing);

        $this->budget_cap_amount = $capping;
        $this->budget_cap_pacing = $pacing;
    }
}
