<?php
namespace Core;
final class Exception extends \Exception{
    public function handleException(\Exception $ex) {
        echo "Core exception: `" . $ex-getMessage() . '`';
        die;
    }

}
