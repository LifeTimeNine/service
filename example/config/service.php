<?php

return [
    'ali' => [

        'accessKey_id' => '',
        'accessKey_secret' => '',
        'oss_endpoint' => 'oss-cn-beijing.aliyuncs.com',
        'oss_bucketName' => 'lifetime-tpadmin',

        'sandbox' => true, // 是否是沙箱环境
        'appid' => '20210001166', // 支付宝APPID (测试数据)
        'public_key' => 'MIGfMA0GCSqGHdfVBoCNZ5mubSSYX69tkGd97og+DrR2E0v0WsJ2NVewz8hFrLXSFg6frHWj0vrF+JOXTWrVJRjvyCtAeHUZSV668fqdyBqIGmb9umNDdfPZBlkMi2Oa7MAC6UOjXDruWBEKR+MJUqSijhqBDhICGQOrtyIawIDAQAB', // 应用公钥
        'private_key' => 'MIIEpQIuLrex4T88QMpme+MuEcGgZcBdmXCQq/Ab7O7KGoVm8EKQYG0unFThS5xYSF66uGVkN8GvPcrlR+/GHP8VzhPFDnaM8mrNmzj3WdYfBYi1TP37wXwP38NFbb3/G5yfNwSmTIGfGFYnOpO3p2vl36M14SBIBnYl6W4YNWeNsNhvtN4jTJsG71zrRM1LKOKbMpG3ttGiEVNA+rtaoipwARL67KB5Kv8RE6iDqakeTVhLhv8vCNeIQkwlkQE+0g/6QBCh6jr/+Fc0By1HM5lRIDnF1rReO2hbDKNK4n3QIDAQABAoIBAEae1mNnV5YQtYG0uXPf1OQ4fJHdh9HrQW32KwerQ0W/RtTtc1Mv5Sd/F3eHclfssP1q8xSGR6ioAy+WLj5qmUPrT1b792gf7Zzhnj4prW1+HXhGmBMnCIMJ9W9kIPj7u7i9fxFtxAMbFwKvZLPz6tRFZjFuhy300TwVh+T8odR4wxOX+btk1WUPZIxSS/td7KN7//5aaK7c+I5nsoo+Tp5fR0SFBidfYBbot5l01oPpOea9678RyqG3K1ZlHv/3THpm+lKPHYVpdi2IM3jESb7bWPQINNE/9XUpnVqNfG+ycXJnFcEy+i6lR437uHRWFV/BeK7bNtKPPJ6HyEJ5fCECgYEA0Kp76VRro6CSR4GLN7hrKh3XVwLMbG8qZCVBwYAp6iUWF+yx41Kj0qT+4wQjEMudmb1uuVc49epqIrAE+0r5faLK8tB3uBrdQoAl5RF03wXmCv1ZWlGQ/XQKyVXBNb70p/8jb90z4xTm9JT8Xo/tHIH4IXhGvmA0m3e1Gs2ew1kCgYEAqehEK4JaDgHZjJ0J7pWfFVbl1BkeHRrpWQ/ebtrngfhWL7UsGFbx4TfgckC1f6es4CN+522vsj5W9pQ/WcsT07FpTYRdQY1sFUlIK01FSchN4K30h9uCVcCQmDUeUH2nywuLWAziNUxcFwtUVrKNcGWmWtWYGUzOgvfy8kR5zCUCgYEAukESraj/E2UGbPFC1Q+2CDfuuOn0km1/xzhCQ3gk8az81YofFqvzMti38ucEbb9yA4LFTIaAf3EoH6JCMBQyMmSXBrnxwtGn4e4E5Tz5twDK39BYa3gXFUT5Q9FZzqGOPE5O0VD/OZi0tmguBDIwEZwpYaa8br3s90CY1T3okrECgYEAj4M8cXO9FS/Czd6nUPrYUHIB1tEQeo0MpMmenAwSl9lnEwz4neZykEVeM6MsqxK6FuhkfJ5NVUUKt2QoznOV361uwKcZDhGiRaiMaObvq46hGTJV5Zsnz77DY40aeeppHDw4Crt3JoXFE0HijwhqJ5H0nazkuBoEJOPCNZhZOHECgYEAhLHPRFr42UWdDBS6K4OFd83zqsZ5sh4PlOaILZgTY5cHKnoS7A/Q2Usryb3ONdLKOUxwhSIrxUi+HBFF2nS5L2RWaMrR23wkDPx4jqA9yGTl+n1EuHA1RW34qaonkkxG0oHMXaUC5NZTCE+cSE9mC6emrwfYgKfgzAxMoUAsGe4=', // 应用私钥
        'alipay_public_key' => 'MIIBIgKCAQEA6+UTiewfpDdU5p1f7Je6LLeWK2toKJzk6WV4ZVXBmxhzfwKzPgyqXOfugAdwSkU0VnVfRMEVUtoxuUlsMuBM0FriuOgyjkoXBS0sxk1AGRzfVGt8sOBNAnINpAek7hyctWcw+4t5FmWBOF/lZCpMdZMFQ4b375SL9d2HYCN6RK+/xLXYz3F1lK1bNINwrnZqToKlJ1YboawJznJo5ZzkdfTXzolXGyBazYjcAs10AUyKXnfQYGAKgEqhtatgtKxMz8q44n4m6vffkgmyHbnFbEPDHgEo9UBSZX/z9wjSBxduFn7seY0yJ5Ur4bhQQIDAQAB', // 支付宝公钥

        // 短信
        'sms_signName' => '',
        'sms_templateCode' => ''
    ],
    'wechat' => [
        // 公众号
        'official_appid' => 'wx372', // 公众号appid(测试数据)
        'official_app_secret' => '36831d9d4', // 公众号sercet（测试数据）

        // 小程序
        'miniapp_appid' => '',
        'miniapp_app_secret' => '',

        // 开放平台 web应用
        'open_webs' => [
            'default' => 'test',
            'apps' => [
                // 
                'test' => [
                    'appid' => '',
                    'app_secret' => ''
                ],
            ]
        ],

        'mch_id' => '16032', // 商户ID （测试数据）
        'mch_key' => '3A099B87C5', // 商户支付密钥 （测试数据）
        'mch_key_v3' => '4655e71652', // 商户支付密钥 v3(测试数据)
        'ssl_cer' => env('root_path') . '/config/cret/apiclient_cert.pem', // 证书cert.pem路径
        'ssl_key' => env('root_path') . '/config/cret/apiclient_key.pem', // 证书key.pem路径
    ],
    'byteDance' => [
        'miniapp_appid' => '', // 字节小程序APPID
        'miniapp_secret' => '', // 字节小程序APP Secret
        'miniapp_pay_mch_id' => '', // 字节小程序支付商户号
        'miniapp_pay_appid' => '', // 字节小程序支付APPPID
        'miniapp_pay_secret' => '', // 字节小程序支付secret

        // 抖店
        'shakeshop' => [
            'default' => 'dev',
            'shops' => [
                'dev' => [
                    'app_key' => '',
                    'app_secret' => '',
                    'shop_id' => '',
                ]
            ]
        ]
    ],
    'cache_path' => env('runtime_path') . 'service' . DIRECTORY_SEPARATOR, // 缓存目录
    'cache_callable' => [ // 自定义缓存操作方法（如果设置了此参数，缓存目录将不再生效）
        'set' => null, // 写入缓存
        'get' => null, // 获取缓存
        'del' => null, // 删除缓存
        'put' => null, // 写入文件
    ]
];
