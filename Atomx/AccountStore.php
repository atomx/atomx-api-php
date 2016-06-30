<?php namespace Atomx;


interface AccountStore {
    public function getToken();
    public function storeToken($token);

    public function getApiBase();
}