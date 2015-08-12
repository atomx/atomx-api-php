<?php namespace Atomx\Resources\Traits;

/**
 * Set the state of a resource
 */
trait StateTrait
{
    public function setState($state)
    {
        if (!in_array($state, ['active', 'inactive']))
            throw new InvalidArgumentException('API: Invalid state provided');

        $this->state = strtoupper($state);
    }
}
