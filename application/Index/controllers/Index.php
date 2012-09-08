<?php
namespace Index;
use Core\Application as Application;
class Index extends Application {
    public function index($name = 'Anonymous') {
        return array('name' => $name);
    }
}