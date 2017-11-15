<?php

namespace Wunderlist;

use Httpful\Request;

class Common {

    protected $root;
    protected $access_token;
    protected $client_id;

    function __construct($client_id) {
        $this->client_id = $client_id;
    }

    function getClientID() {
        return $this->client_id;
    }

    unction setClientID($id) {
        $this->client_id = $id;
    }

    private function url($endpoint) {
        return $this->root . $endpoint;
    }

}


class Auth extends Common {

    private $root = 'https://www.wunderlist.com/oauth/';
    private $access_token;
    private $client_id;

    function __construct($client_id) {
        parent::__construct($client_id);
    }

    function reuqestAccess($rdr, $state) {
        $url = $this->url("authorize?client_id=$this->client_id&redirect_uri=$rdr&state=$state");
        return Request::get($url)->send();
    }

    function getAccessToken($secret, $code) {
        $url = $this->url('access_token');
        $data = [
            'client_id'     => $this->client_id,
            'client_secret' => $secret,
            'code'          => $code,
        ];
        $tmp = Request::init()
            ->sendsJson()
            ->expectsJson();
        Request::ini($tmp);
        return Request::post($uri)->body($data)->send();
    }

}

/*
class Api {

    private $api = 'https://a.wunderlist.com/api/v1/';
    private $access_token;
    private $client_id;

    public function get($endpoint, $args = array()) {
        $url = $this->url($endpoint);
        if (!empty($args)) {
            $url .= '?' . join("&", $args);
        }
        $request = Request::get($url);
        $this->addAuthHeaders($request);
        $response = $request->send();
        return $response;
    }

    public function post($endpoint, $data) {
        $request = Request::post($this->url($endpoint));
        $this->addAuthHeaders($request);
        $response = $request->body($data)->send();
        return $response;
    }

}*/
