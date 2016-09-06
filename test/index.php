<?php

include '../vendor/autoload.php';

try {
    \LuffyZhao\Pay::instance('WebAlipay');
} catch (\LuffyZhao\Exception\PayException $e) {
    echo $e->getMessage();
}
