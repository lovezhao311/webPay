<?php

namespace LuffyZhao;

include '../vendor/autoload.php';

use \LuffyZhao\Exception\PayException;

$config = include '../src/Config/webAplipay.php';

try {
    $pay = \LuffyZhao\Pay::instance('WebAlipay', $config);

    $verify = $pay->notifyVerify();

    if ($verify['status'] == 0) {
        throw new PayException("支付失败！");
    } elseif ($verify['status'] == 1) {
        echo $pay->notifyView();
    }

} catch (PayException $e) {
    echo $e->getMessage();
}
