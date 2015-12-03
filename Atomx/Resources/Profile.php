<?php namespace Atomx\Resources;

use Atomx\AtomxClient;
use Atomx\Resources\Traits\NameTrait;
use InvalidArgumentException;

class Profile extends AtomxClient {
    use NameTrait;

    protected $endpoint = 'profile';

    // Targeting
    public function setCountryTargeting($action, $countries)
    {
        if (!in_array($action, ['include', 'exclude']))
            throw new InvalidArgumentException('API: Invalid bid type provided');

        $this->countries_filter_action = strtoupper($action);
        $this->countries_filter        = $countries;
    }

    public function setFrequencyCap($capping, $per = 86400)
    {
        $this->impression_frequency_cap_amount = $capping;
        $this->impression_frequency_cap_per    = $per;
    }

    // Dayparting times_filter[_action] [start: Nth minute of the week, end: Nth minute of the week]

    // Techno targeting {browser,device,operating_system}_filter[_action]
    public function setDeviceTargeting($action, $devices)
    {
        $this->device_types_filter_action = strtoupper($action);
        $this->device_types_filter        = $devices;
    }

    public function setOSTargeting($oses)
    {
        $this->operating_systems_filter = $oses;
    }

    public function setBrowserTargeting($browsers)
    {
        $this->browsers_filter = $browsers;
    }

    public function setNetworkTargeting($action, $networks)
    {
        $this->networks_filter        = $networks;
        $this->networks_filter_action = strtoupper($action);
    }

    public function setPublisherTargeting($action, $publishers)
    {
        $this->publishers_filter        = $publishers;
        $this->publishers_filter_action = strtoupper($action);
    }

    public function setDomainTargeting($action, $domains)
    {
        $this->domains_filter        = $domains;
        $this->domains_filter_action = strtoupper($action);
    }
}
