<?php namespace Atomx\Resources;

use Atomx\AtomxClient;

class Totp extends AtomxClient {
    protected $endpoint      = 'totp';
    protected $requiresToken = true;

    public function send($totp)
    {
        return $this->post(compact('totp'));
    }
}
