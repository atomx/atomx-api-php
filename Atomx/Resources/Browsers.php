<?php namespace Atomx\Resources;


use Atomx\AtomxClient;

class Browsers extends AtomxClient {
    protected $endpoint = 'browsers';
    protected $requiresLogin = false;
}
