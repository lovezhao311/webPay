<?php
namespace LuffyZhao\Driver;

use LuffyZhao\Library\Payment;

class WebAlipay extends Payment
{
    public function create()
    {
        return 22;
    }
}

/*
service
partner
_input_charset
sign_type
sign
notify_url
return_url

out_trade_no
subject
payment_type
total_fee
seller_id
seller_email
seller_account_name
buyer_id
buyer_email
buyer_account_name
price
quantity
body
show_url
paymethod
enable_paymethod
anti_phishing_key
exter_invoke_ip
extra_common_param
it_b_pay
token
qr_pay_mode
qrcode_width
need_buyer_realnamed
hb_fq_param
goods_type
 */
