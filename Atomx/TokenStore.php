<?php namespace Atomx;


interface TokenStore {
    public function getToken();
    public function storeToken($token);

    public function getApiBase();
}
