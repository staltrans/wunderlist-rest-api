<?php

namespace Wunderlist;

use Httpful\Request;
use Wunderlist\Common;

class Auth extends Common {

    protected $root = 'https://www.wunderlist.com/oauth/';
    protected $client_id;

    function __construct($client_id = null) {
        parent::__construct($client_id);
    }

    function reuqestAccessUrl($rdr, $state) {
        $args = [
            'client_id'    => $this->client_id,
            'redirect_uri' => $rdr,
            'state'        => $state
        ];
        return $url = $this->url('authorize', $args);
    }

    function getAccessToken($secret, $code) {
        $url = $this->url('access_token');
        $data = [
            'client_id'     => $this->client_id,
            'client_secret' => $secret,
            'code'          => $code,
        ];
        $resp = Request::post($url)
            ->body($data)
            ->sendsJson()
            ->expectsJson()
            ->send();
        if (!empty($resp->body->access_token)) {
            return $resp->body->access_token;
        }
        return false;
    }

}
