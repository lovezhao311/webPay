<?php
namespace LuffyZhao;

include '../vendor/autoload.php';
use \Exception;
use \LuffyZhao\Exception\PayException;

$order = [
    "out_trade_no" => '20160907000023423',
    'subject'      => 'dfasdfasdf',
    "total_fee"    => 600,
    "body"         => 'dfasdfasadsfasdf',
    "show_url"     => 'http://www.kuaibo.com/',
    "goods_type"   => '1',
];

$notifyUrl = 'http://www.kuaibo.com/notify_url.php';
$returnUrl = 'http://www.kuaibo.com/return_url.php';

$config = include '../src/Config/mobileWebAlipay.php';

try {

    $pay = \LuffyZhao\Pay::instance('MobileWebAlipay', $config)
        ->setOrder($order)
        ->setNotify($notifyUrl)
        ->setReturn($returnUrl)
        ->handle();

    print_r($pay);

} catch (PayException $payException) {

    echo $payException->getMessage();

} catch (Exception $e) {

    echo $e->getMessage();

}
