<?php

namespace Wunderlist\Api;

use Httpful\Request;
use Wunderlist\Common;

class Api extends Common {

    protected $root = 'https://a.wunderlist.com/api/v1/';
    protected $access_token;
    protected $client_id;

    function __construct($client_id = null, $access_token = null) {
        $this->access_token = $access_token;
        parent::__construct($client_id);
    }

    function getAccessToken() {
        return $this->access_token;
    }

    function setAccessToken($id) {
        $this->access_token = $id;
    }

    private function addAuthHeaders(Request &$request) {
        if (!empty($this->client_id) && !empty($this->access_token)) {
            $request->addHeader('X-Client-ID', $this->client_id);
            $request->addHeader('X-Access-Token', $this->access_token);
        }
    }

    function get($endpoint, $args = []) {
        $url = $this->url($endpoint, $args);
        $request = Request::get($url);
        $this->addAuthHeaders($request);
        $response = $request->send();
        return $response;
    }

    function post($endpoint, $data) {
        $url = $this->url($endpoint);
        $request = Request::post($this->url($endpoint));
        $this->addAuthHeaders($request);
        $response = $request
            ->body($data)
            ->sendsJson()
            ->expectsJson()
            ->send();
        return $response;
    }

    function patch($endpoint, $data) {
        $request = Request::patch($this->url($endpoint));
        $this->addAuthHeaders($request);
        $response = $request
            ->body($data)
            ->sendsJson()
            ->expectsJson()
            ->send();
        return $response;
    }

    function put($endpoint, $data) {
        $request = Request::put($this->url($endpoint));
        $this->addAuthHeaders($request);
        $response = $request
            ->body($data)
            ->sendsJson()
            ->expectsJson()
            ->send();
        return $response;
    }

    function del($endpoint, $args) {
        $request = Request::delete($this->url($endpoint, $args));
        $this->addAuthHeaders($request);
        $response = $request
            ->sendsJson()
            ->expectsJson()
            ->send();
        return $response;
    }

    function endpoint($name, $id = null) {
        if(isset($id)) {
            $name .= "/$id";
        }
        return $name;
    }

    function toInt($var) {
        $int = intval($var);
        if (is_int($int) && $int == $var) {
            return $int;
        }
        return false;
    }

    function toBool($var) {
        return (bool) $this->toInt($var);
    }

    function boolToString($var) {
        return ($this->toBool($var)) ? 'true' : 'false';
    }

    /**
     * @see https://developer.wunderlist.com/documentation/endpoints/root
     */
    function getRoot() {
        return $this->get('root');
    }

    /**
     * @see https://developer.wunderlist.com/documentation/endpoints/list
     */
    function lists($list_id = null) {
        return $this->endpoint('lists', $list_id);
    }

    function getList($list_id = null) {
        return $this->get($this->lists($list_id));
    }

    function newList($title) {
        return $this->post($this->lists(), ['title' => $title]);
    }

    function updList($list_id, $revision, $title = null) {
        $revision = $this->toInt($revision);
        if ($revision) {
            $data = ['revision' => $revision];
            if (isset($title)) {
                $data['title'] = $title;
            }
            return $this->patch($this->lists($list_id), $data);
        }
        return false;
    }

    function delList($list_id, $revision) {
        $revision = $this->toInt($revision);
        if ($revision) {
            $data = ['revision' => $revision];
            return $this->del($this->lists($list_id), $data);
        }
        return false;
    }

    /**
     * @see https://developer.wunderlist.com/documentation/endpoints/task
     */
    function tasks($task_id = null) {
        return $this->endpoint('tasks', $task_id);
    }

    function getTasks($list_id, $completed = null) {
        $data = ['list_id' => $list_id];
        if (isset($completed)) {
            $data['completed'] = $this->boolToString($completed);
        }
        return $this->get($this->tasks(), $data);
    }

    function getTask($task_id) {
        return $this->get($this->tasks($task_id));
    }

    function newTask($list_id, $title, $other_vars = []) {
        $data = [
            'list_id' => $this->toInt($list_id),
            'title'   => $title,
        ] + $other_vars;
        return $this->post($this->tasks(), $data);
    }

    function updTask($task_id, $revision, $other_vars = []) {
        $revision = $this->toInt($revision);
        if ($revision) {
            $data = ['revision' => $revision] + $other_vars;
            if (isset($data['list_id'])) {
                $data['list_id'] = $this->toInt($data['list_id']);
            }
            if (isset($data['completed'])) {
                $data['completed'] = $this->toBool($data['completed']);
            }
            return $this->patch($this->tasks($task_id), $data);
        }
        return false;
    }

    function delTask($task_id, $revision) {
        $revision = $this->toInt($revision);
        if ($revision) {
            return $this->del($this->tasks($task_id), ['revision' => $revision]);
        }
        return false;
    }

    /**
     * @see https://developer.wunderlist.com/documentation/endpoints/user
     */
    function getSelf() {
        return $this->get('user');
    }

    function getAvatarURL($user_id, $size = 'original', $fallback = true) {
        return $this->url($this->endpoint('avatar'), ['user_id' => $user_id, 'size' => $size, 'fallback' => $fallback]);
    }

    function getUser($list_id  = null) {
        $args = [];
        if (isset($list_id)) {
            $args['list_id'] = $list_id;
        }
        return $this->get('users', $args);
    }

    /**
     * @see https://developer.wunderlist.com/documentation/endpoints/task_comment
     */
    function taskComments($task_id = null) {
        return $this->endpoint('task_comments', $task_id);
    }

    function getTaskComments($task_id = null) {
        return $this->get($this->taskComments($task_id));
    }

    function newTaskComments($task_id, $text) {
        return $this->post($this->taskComments(), ['task_id' => $this->toInt($task_id), 'text' => $text]);
    }

}
