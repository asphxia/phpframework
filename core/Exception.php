<?php
namespace Core;
final class Exception extends \Exception{
    public function handleException(\Exception $ex) {
        header("Core-Exception: `" . $ex->getMessage() . '`');
        header("Location: /");
    }
}
