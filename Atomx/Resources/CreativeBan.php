<?php namespace Atomx\Resources;

use Atomx\AtomxClient;

class CreativeBan extends AtomxClient {

    protected $endpoint = 'creative_ban';

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function setCreativeId($id)
    {
        $this->creative_id = $id;
    }

    public function setReasonId($id)
    {
        $this->reason_id = $id;
    }
}
