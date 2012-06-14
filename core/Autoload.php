<?php

/**
 * Description of CoreLoader
 *
 * @author asphyxia
 * @copyright copyleft 2012
 */

final class CoreLoader {

    public function loadClass($name) {
        # TODO Get classes to load from configuration
        $classes = array(
            'Controller' => '../core/Controller',
            'Config' => '../core/Config',
            'Render' => 'Render',
            'Render\Render' => 'Render',
            'Render\RenderHelpers' => 'RenderHelpers',
            'CoreException' => 'CoreException',
            'Error' => '../validation/Error',
            'Utils' => '../util/Utils',
            'FrontController' => '../core/FrontController',
            'FrontControllerHelpers' => '../core/FrontControllerHelpers'
        );
        if (!array_key_exists($name, $classes)) {
            throw new \Exception("Class {$name} not found!");
        }
        #echo "Loading: '" . dirname(__FILE__) . '/' . $classes[$name] . '.php' . "\n";
        require dirname(__FILE__) . '/' . $classes[$name] . '.php';
    }

}