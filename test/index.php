<?php
namespace LuffyZhao;

include '../vendor/autoload.php';
use \Exception;
use \LuffyZhao\Exception\PayException;

$order = [
    "out_trade_no" => 'dfasdfasdfasd',
    'subject'      => 'adfadfsdf',
    "total_fee"    => 59.65,
    "body"         => '好好先生没工夺',
    "show_url"     => 'https://doc.open.alipay.com/docs/doc.htm?spm=a219a.7629140.0.0.2yIZwZ&treeId=62&articleId=104743&docType=1#s6',
    "goods_type"   => '1',
];

$notifyUrl = 'https://doc.open.alipay.com/docs/doc.htm?spm=a219a.7629140.0.0.llIehP&treeId=60&articleId=104790&docType=1';
$returnUrl = 'https://doc.open.alipay.com/docs/doc.htm?spm=a219a.7629140.0.0.llIehP&treeId=60&articleId=104790&docType=1';

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
