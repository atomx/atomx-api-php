<?php namespace Atomx;


interface TokenStore {
    public function getUsername();
    public function getPassword();
    public function getToken();
    public function storeToken($token);
}