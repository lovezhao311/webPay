<?php

include '../vendor/autoload.php';

try {
    \LuffyZhao\Pay::instance('wxweb');
} catch (\LuffyZhao\Exception\PayException $e) {
    echo $e->getMessage();
}
