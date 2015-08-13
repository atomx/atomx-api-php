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

    public function setDailyBudget($dailyBudget, $pacing = 'ASAP')
    {
        if (!in_array($pacing, ['ASAP', 'EVEN']))
            throw new InvalidArgumentException('API: Invalid pacing provided: ' . $pacing);

        $this->daily_budget_amount = $dailyBudget;
        $this->daily_budget_pacing = $pacing;
    }

    public function setBidType($type)
    {
        if (!in_array($type, ['CPM', 'dCPM', 'CPC', 'CPA']))
            throw new InvalidArgumentException('API: Invalid bid type provided');

        $this->pricemodel = $type;
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
}
