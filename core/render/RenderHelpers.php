<?php

/**
 * Description of RenderHelpers
 *
 * @author asphyxia
 */

namespace Core;

class RenderHelpers {

    private function getLayout() {
        return $this->page['layout_dir'] . $this->page['layout'];
    }

    private function getScript() {
        return $this->page['page_dir'] . $this->page['page'];
    }

    private function getTemplate() {
        return $this->page['page_dir'] . $this->page['template'];
    }

    private function hasLayout() {
        return file_exists($this->getLayout());
    }

    private function hasScript() {
        return file_exists($this->getScript());
    }

    private function hasTemplate() {
        return file_exists($this->getTemplate());
    }

}