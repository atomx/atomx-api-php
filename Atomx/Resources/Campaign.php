<?php namespace Atomx\Resources;

use Atomx\AtomxClient;
use Atomx\Resources\Traits\NameTrait;
use Atomx\Resources\Traits\StateTrait;
use InvalidArgumentException;

class Campaign extends AtomxClient {
    use NameTrait, StateTrait;

    protected $endpoint = 'campaign';

    public function setAdvertiserId($id)
    {
        $this->advertiser_id = $id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setStartTime($time)
    {
        $this->start_time = $time;
    }

    public function setState($state)
    {
        if (!in_array($state, ['active', 'inactive']))
            throw new InvalidArgumentException('API: Invalid bid type provided');

        $this->state = strtoupper($state);
    }

    public function setBudget($amount)
    {
        if ($amount == 0)
            throw new InvalidArgumentException('API: Trying to set a campaign budget to unlimited (0)');

        $this->budget = $amount;
    }

    public function setDailyBudget($dailyBudget, $per = 86400)
    {
        $this->daily_budget_amount = $dailyBudget;
        $this->daily_budget_per    = $per;
    }

    public function setBidType($type)
    {
        if (!in_array($type, ['CPM', 'dCPM', 'CPC', 'CPA']))
            throw new InvalidArgumentException('API: Invalid bid type provided');

        $this->pricemodel = strtoupper($type);
    }

    public function setBidPrice($price)
    {
        $this->bid = $price;
    }

    public function setCreatives($activeCreatives, $inactiveCreatives)
    {
        $this->creatives_active   = $activeCreatives;
        $this->creatives_inactive = $inactiveCreatives;
    }

    public function setConversionPixels($pixels)
    {
        $this->conversion_pixels_active = $pixels;
    }

    public function setBrokerFee($percentage)
    {
        $this->brokerfee = $percentage;
    }
}
