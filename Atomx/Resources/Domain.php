<?php namespace Atomx\Resources;

use Atomx\AtomxClient;

class Domain extends AtomxClient {
    protected $endpoint = 'domain';
    protected $requiresToken = false;

    public function setLanguage($lan = null)
    {
        if(empty($lan)) $lan = null;
        $this->language_id = $lan;
    }

    public function setCategory($cat = 0)
    {
        if(empty($cat)) $cat = 0;
        $this->category = $cat;
    }

    public function setAttributes($att = [])
    {
        if(!isset($att)) $att = [];
        $this->attributes = $att;
    }
}
