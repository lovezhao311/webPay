<?php
namespace LuffyZhao;

include '../vendor/autoload.php';
use \Exception;
use \LuffyZhao\Exception\PayException;

$order = [
    "out_trade_no" => '20160907000023423',
    'subject'      => '快播2年会员充值 600 元',
    "total_fee"    => 600,
    "body"         => '快播2年会员充值 600 元',
    "show_url"     => 'http://www.kuaibo.com/',
    "goods_type"   => '1',
];

$notifyUrl = 'http://www.kuaibo.com/notify_url.php';
$returnUrl = 'http://www.kuaibo.com/return_url.php';

$config = include '../src/Config/webAplipay.php';

try {

    $pay = \LuffyZhao\Pay::instance('WebAlipay', $config)
        ->setOrder($order)
        ->setNotify($notifyUrl)
        ->setReturn($returnUrl)
        ->handle();

    echo $pay . "\n";

} catch (PayException $payException) {

    echo $payException->getMessage();

} catch (Exception $e) {

    echo $e->getMessage();

}
