<?php
namespace LuffyZhao;

include '../vendor/autoload.php';
use \Exception;
use \LuffyZhao\Exception\PayException;

try {

    $pay = \LuffyZhao\Pay::instance('WebAlipay', ['App' => 'http://'])
        ->setOrder(['title' => '订单标题'])->create();

    echo $pay;

} catch (PayException $payException) {

    echo $payException->getMessage();

} catch (Exception $e) {

    echo $e->getMessage();

}
