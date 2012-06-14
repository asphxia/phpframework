<?php

/**
 * Description of CoreLoader
 *
 * @author asphyxia
 * @copyright copyleft 2012
 */
namespace Core;

final class Autoload {

    public function loadClass($name) {

        # TODO Get classes to load from configuration
        $classes = array(
            'Core\Exception' => '../core/CoreException',
            'Core\Render\Render' => '../core/render/Render',
            'Core\Router\Utils\Utils' => '../core/router/utils/Utils',
            'Core\Router\Router' => '../core/router/Router',
            'Core\Configuration\EncoderInterface' => '../core/config/EncoderInterface',
            'Core\Configuration\IniEncoder' => '../core/config/encoder/IniEncoder',
            'Core\Configuration\Configuration' => '../core/config/Configuration',
            'Core\Utils\Singleton' => '../core/utils/Singleton',
            'Core\FrontController' => '../core/FrontController',
        );
        if (!array_key_exists($name, $classes)) {
            throw new \Exception("Class {$name} not found!");
        }
        //echo "Loading: '" . dirname(__FILE__) . '/' . $classes[$name] . '.php' . "\n";
        require dirname(__FILE__) . '/' . $classes[$name] . '.php';
    }

}