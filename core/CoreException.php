<?php

namespace Core;

final class CoreException {

    private $_render = null;

    public function __construct() {
        #$this->_render = new Render();
    }

    public function handleException(\Exception $ex) {
        $extra = array('message' => $ex->getMessage());
        if ($ex instanceof NotFoundException) {
            header('HTTP/1.0 404 Not Found');
            $this->_render = new Render(\Config::getConfig('404'));
            $this->_render->runPage($extra);
        } else {
            header('HTTP/1.0 500 Internal Server Error');
            $this->_render = new Render(\Config::getConfig('500'));
            $this->_render->runPage($extra);
        }
        die;
    }

}
