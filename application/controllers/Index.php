<?php
namespace Application;
class Index {
    public function index($name = 'Anonymous') {
        return array('name' => $name);
    }
}