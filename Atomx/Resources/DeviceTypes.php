<?php namespace Atomx\Resources;


use Atomx\AtomxClient;

class DeviceTypes extends AtomxClient {
    protected $endpoint = 'device-types';
    protected $requiresLogin = false;
}
