<?php namespace Atomx\Resources;

use Atomx\AtomxClient;
use Atomx\Resources\Traits\NameTrait;
use Atomx\Resources\Traits\StateTrait;

class ConversionPixel extends AtomxClient {
    use NameTrait, StateTrait;

    protected $endpoint = 'conversion-pixel';

    public function setDuration($seconds)
    {
        $this->duration = $seconds;
    }

    public function setDurationInDays($days)
    {
        $this->setDurationSeconds($days * 60 * 60 * 24);
    }
}
