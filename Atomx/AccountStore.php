<?php namespace Atomx;


interface AccountStore {
    public function getUsername();
    public function getPassword();
    public function getToken();
    public function storeToken($token);

    public function getApiBase();
}