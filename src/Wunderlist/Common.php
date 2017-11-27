<?php

namespace Wunderlist\Common;

use Httpful\Request;

class Common {

    protected $root;
    protected $client_id;

    function __construct($client_id = null) {
        $this->client_id = $client_id;
    }

    function getClientID() {
        return $this->client_id;
    }

    function setClientID($id) {
        $this->client_id = $id;
    }

    function url($endpoint, $args = []) {
        $url = $this->root . $endpoint;
        if (!empty($args)) {
            $tmp = [];
            foreach ($args as $key => $val) {
                $tmp []= "$key=$val";
            }
            $url .= '?' . join("&", $tmp);
        }
        return $url;
    }

}
