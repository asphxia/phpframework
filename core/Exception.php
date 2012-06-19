<?php
namespace Core;
final class Exception extends \Exception{
    public function handleException(\Exception $ex) {
        header("Core-Exception: `" . $ex->getMessage() . '`');
        if ($_SERVER["REQUEST_URI"] !== '/') {
            header("Location: /");
        }
    }
}
