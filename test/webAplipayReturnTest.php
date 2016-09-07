<?php
namespace LuffyZhao;

include '../vendor/autoload.php';

use \LuffyZhao\Exception\PayException;

try {
    $verify = \LuffyZhao\Pay::instance('WebAlipay')->returnVerify();

    if ($verify['status'] == 0) {
        throw new PayException("支付失败！");
    } elseif ($verify['status'] == 1) {
        // 支付成功
    }

} catch (PayException $e) {
    echo $e->getMessage();
}
