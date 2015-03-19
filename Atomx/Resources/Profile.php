<?php namespace Atomx\Resources;

use Atomx\AtomxClient;

class Profile extends AtomxClient {
    // Targeting
    public function setCountryTargeting($action, $countries)
    {
        $this->fields['countries_filter_action'] = $action;
        $this->fields['countries_filter']        = $countries;
    }

    public function setFrequencyCap($capping)
    {
        $this->fields['impression_frequency_cap_amount'] = $capping;
        $this->fields['impression_frequency_cap_per']    = 86400;
    }

    // Dayparting times_filter[_action] [start: Nth minute of the week, end: Nth minute of the week]

    // Techno targeting {browser,device,operating_system}_filter[_action]

    // Domain targeting domains_filter[_action]

}