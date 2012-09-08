<?php
namespace Index;
class Index {
    public function index($name = 'Anonymous') {
        return array('name' => $name);
    }
}