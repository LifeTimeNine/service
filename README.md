# service

本类库封装了一些常见的平台的部分接口  
调用的逻辑为：**平台::业务->功能->操作**  

比如：微信公众平台获取用户信息
```php
$order = \service\WeChat::official()->user()->getUserInfo();
```

目前包含的平台有:
1. 支付宝和阿里云
2. 微信
3. 字节跳动
4. 七牛云

## 配置
类库可以自动获取tp5.0、tp5.1、tp6.0和laravel的配置作为全局配置  
比如 tp5.1，可以在config目录中添加一个 `service.php` 文件，加入如下配置
```php
<?php

return [
    // 阿里云（支付宝）相关参数
    'ali' => [
        'accessKey_id' => '',
        'accessKey_secret' => '',
        // OSS相关配置
        'oss_endpoint' => '',
        'oss_bucketName' => '',
        // 支付相关参数
        'sandbox' => true, // 是否是沙箱环境
        'appid' => '20210001166', // 支付宝APPID (测试数据)
        'public_key' => '', // 应用公钥
        'private_key' => '', // 应用私钥
        'alipay_public_key' => '', // 支付宝公钥
    ],
    // 微信相关参数
    'wechat' => [
        // 公众号
        'official_appid' => '', // 公众号appid(测试数据)
        'official_app_secret' => '', // 公众号sercet（测试数据）
        // 小程序
        'miniapp_appid' => '',
        'miniapp_app_secret' => '',
        'mch_id' => '', // 商户ID （测试数据）
        'mch_key' => '', // 商户支付密钥 （测试数据）
        'mch_key_v3' => '', // 商户支付密钥 v3(测试数据)
        'ssl_cer' => '', // 证书cert.pem路径
        'ssl_key' => '', // 证书key.pem路径
    ],
    // 字节跳动相关参数
    'byteDance' => [
        'miniapp_appid' => '', // 字节小程序APPID
        'miniapp_secret' => '', // 字节小程序APP Secret
        'miniapp_pay_mch_id' => '', // 字节小程序支付商户号
        'miniapp_pay_appid' => '', // 字节小程序支付APPPID
        'miniapp_pay_secret' => '', // 字节小程序支付secret
    ],
    // 类库运行相关参数
    'cache_path' => '', // 缓存目录
    'cache_callable' => [ // 自定义缓存操作方法（如果设置了此参数，缓存目录将不再生效）
        'set' => null, // 写入缓存
        'get' => null, // 获取缓存
        'del' => null, // 删除缓存
        'put' => null, // 写入文件
    ]
];

```

> 注意：默认获取配置的key为 `service`

如果想更换这个key为 `my-service`，可以这样操作
```php
<?php
  \service\Config::setKey('my-service');
>
```

如果类库无法获取到全局配置，可以初始化配置
```php
<?php
  $config = [
    // 阿里云（支付宝）相关参数
    'ali' => [
        // OSS相关配置
        'accessKey_id' => '',
        'accessKey_secret' => '',
        'oss_endpoint' => '',
        'oss_bucketName' => '',
        // 支付相关参数
        'sandbox' => true, // 是否是沙箱环境
        'appid' => '20210001166', // 支付宝APPID (测试数据)
        'public_key' => '', // 应用公钥
        'private_key' => '', // 应用私钥
        'alipay_public_key' => '', // 支付宝公钥
    ]
  ];
  \service\Config::init($config);
>
```

> 注意：使用初始化方法之后，类库不会再从配置文件中获取配置；初始化必须在所有操作之前进行。

## 支持的平台和业务如下

---
<table border=0 cellpadding=0 cellspacing=0>
  <tr>
    <td>平台</td>
    <td>业务</td>
    <td>功能</td>
    <td>操作</td>
  </tr>
  <tr>
    <td rowspan=81>WeChat - 微信</td>
    <td rowspan=27>official - 公众平台</td>
    <td rowspan=6>oauth - 网页授权</td>
    <td>getCode - 请求code(授权第一步)</td>
  </tr>
  <tr>
    <td>getUserAccessToken - 获取access_token</td>
  </tr>
  <tr>
    <td>refreshAccessToken - 刷新access_token</td>
  </tr>
  <tr>
    <td>getUserinfo - 获取用户个人信息</td>
  </tr>
  <tr>
    <td>checkAccessToken - 校验授权凭证是否有效</td>
  </tr>
  <tr>
    <td>getJsSdkSign - 获取JS-SDK使用权限</td>
  </tr>
  <tr>
    <td rowspan=6>template - 模板消息</td>
    <td>setIndustry - 设置所属行业</td>
  </tr>
  <tr>
    <td>getIndustry - 获取设置的行业信息</td>
  </tr>
  <tr>
    <td>addTemplate - 添加模板</td>
  </tr>
  <tr>
    <td>getAllPrivateTemplate - 获取模板列表</td>
  </tr>
  <tr>
    <td>delPrivateTemplate - 删除模板</td>
  </tr>
  <tr>
    <td>send - 发送模板消息</td>
  </tr>
  <tr>
    <td rowspan=15>user - 用户管理</td>
    <td>createTag - 创建标签</td>
  </tr>
  <tr>
    <td>getTag - 获取已经创建的用户标签</td>
  </tr>
  <tr>
    <td>updateTag - 更新标签信息</td>
  </tr>
  <tr>
    <td>delTag - 删除标签</td>
  </tr>
  <tr>
    <td>getTagUser - 获取某个标签下的粉丝列表</td>
  </tr>
  <tr>
    <td>batchBindTag - 批量为用户打标签</td>
  </tr>
  <tr>
    <td>batchUnBindTag - 批量为用户取消标签</td>
  </tr>
  <tr>
    <td>getUserTag - 获取用户身上的标签</td>
  </tr>
  <tr>
    <td>updateRemark - 设置用户备注名</td>
  </tr>
  <tr>
    <td>getUserinfo - 获取用户基本信息(UnionID机制)</td>
  </tr>
  <tr>
    <td>batchGetUserInfo - 批量获取用户基本信息</td>
  </tr>
  <tr>
    <td>getUserList - 获取用户列表</td>
  </tr>
  <tr>
    <td>getBlackList - 获取黑名单列表</td>
  </tr>
  <tr>
    <td>bacthBlack - 批量拉黑用户</td>
  </tr>
  <tr>
    <td>bacthUnBlack - 批量取消拉黑用户</td>
  </tr>
  <tr>
    <td rowspan=30>pay - 支付v2</td>
    <td rowspan=6>app - app支付</td>
    <td>pay - 下单支付</td>
  </tr>
  <tr>
    <td>query - 查询订单</td>
  </tr>
  <tr>
    <td>reverse - 撤销订单</td>
  </tr>
  <tr>
    <td>refund - 申请退款</td>
  </tr>
  <tr>
    <td>refundQuery - 退款查询</td>
  </tr>
  <tr>
    <td>notify - 异步通知验签</td>
  </tr>
  <tr>
    <td rowspan=6>jsapi - JSAPI支付</td>
    <td>pay - 下单支付</td>
  </tr>
  <tr>
    <td>query - 查询订单</td>
  </tr>
  <tr>
    <td>reverse - 撤销订单</td>
  </tr>
  <tr>
    <td>refund - 申请退款</td>
  </tr>
  <tr>
    <td>refundQuery - 退款查询</td>
  </tr>
  <tr>
    <td>notify - 异步通知验签</td>
  </tr>
  <tr>
    <td rowspan=6>native - Native支付</td>
    <td>pay - 下单支付</td>
  </tr>
  <tr>
    <td>query - 查询订单</td>
  </tr>
  <tr>
    <td>reverse - 撤销订单</td>
  </tr>
  <tr>
    <td>refund - 申请退款</td>
  </tr>
  <tr>
    <td>refundQuery - 退款查询</td>
  </tr>
  <tr>
    <td>notify - 异步通知验签</td>
  </tr>
  <tr>
    <td rowspan=6>h5 - H5支付</td>
    <td>pay - 下单支付</td>
  </tr>
  <tr>
    <td>query - 查询订单</td>
  </tr>
  <tr>
    <td>reverse - 撤销订单</td>
  </tr>
  <tr>
    <td>refund - 申请退款</td>
  </tr>
  <tr>
    <td>refundQuery - 退款查询</td>
  </tr>
  <tr>
    <td>notify - 异步通知验签</td>
  </tr>
  <tr>
    <td rowspan=6>miniApp - 小程序支付</td>
    <td>pay - 下单支付</td>
  </tr>
  <tr>
    <td>query - 查询订单</td>
  </tr>
  <tr>
    <td>reverse - 撤销订单</td>
  </tr>
  <tr>
    <td>refund - 申请退款</td>
  </tr>
  <tr>
    <td>refundQuery - 退款查询</td>
  </tr>
  <tr>
    <td>notify - 异步通知验签</td>
  </tr>
  <tr>
    <td rowspan=20>payV3 - 支付v3</td>
    <td rowspan=4>jsapi - JSAPI支付</td>
    <td>pay - 下单支付</td>
  </tr>
  <tr>
    <td>query - 查询订单</td>
  </tr>
  <tr>
    <td>close - 关闭订单</td>
  </tr>
  <tr>
    <td>notify - 异步通知验签</td>
  </tr>
  <tr>
    <td rowspan=4>native - Native支付</td>
    <td>pay - 下单支付</td>
  </tr>
  <tr>
    <td>query - 查询订单</td>
  </tr>
  <tr>
    <td>close - 关闭订单</td>
  </tr>
  <tr>
    <td>notify - 异步通知验签</td>
  </tr>
  <tr>
    <td rowspan=4>app - app支付</td>
    <td>pay - 下单支付</td>
  </tr>
  <tr>
    <td>query - 查询订单</td>
  </tr>
  <tr>
    <td>close - 关闭订单</td>
  </tr>
  <tr>
    <td>notify - 异步通知验签</td>
  </tr>
  <tr>
    <td rowspan=4>h5 - H5支付</td>
    <td>pay - 下单支付</td>
  </tr>
  <tr>
    <td>query - 查询订单</td>
  </tr>
  <tr>
    <td>close - 关闭订单</td>
  </tr>
  <tr>
    <td>notify - 异步通知验签</td>
  </tr>
  <tr>
    <td rowspan=4>miniApp - 小程序支付</td>
    <td>pay - 下单支付</td>
  </tr>
  <tr>
    <td>query - 查询订单</td>
  </tr>
  <tr>
    <td>close - 关闭订单</td>
  </tr>
  <tr>
    <td>notify - 异步通知验签</td>
  </tr>
  <tr>
    <td rowspan=4>miniapp - 小程序</td>
    <td>login - 登录</td>
    <td>code2Session - 登录凭证校验</td>
  </tr>
  <tr>
    <td rowspan=3>userInfo - 用户信息</td>
    <td>check - <font47">验证用户信息</font>
    </td>
  </tr>
  <tr>
    <td>decodeUserInfo - 用户信息解密</td>
  </tr>
  <tr>
    <td>getPaidUnionId - 用户支付完成后，获取该用户的 UnionId</td>
  </tr>
  <tr>
    <td rowspan=7>Ali - 支付宝或者阿里云</td>
    <td colspan=2 rowspan=7>pay - 支付</td>
    <td>page - Web 页面支付</td>
  </tr>
  <tr>
    <td>wap - 手机网站支付</td>
  </tr>
  <tr>
    <td>app - APP 支付</td>
  </tr>
  <tr>
    <td>notify - 异步通知验签</td>
  </tr>
  <tr>
    <td>query - 查询订单</td>
  </tr>
  <tr>
    <td>refund - 申请退款</td>
  </tr>
  <tr>
    <td>refundQuery - 退款查询</td>
  </tr>
  <tr>
    <td rowspan=4>ByteDance - 字节跳动</td>
    <td colspan=2 rowspan=4>miniapp - 字节小程序</td>
    <td>code2Session - 获取 session_key 和 openId</td>
  </tr>
  <tr>
    <td>checkUserInfo - 验证用户信息</td>
  </tr>
  <tr>
    <td>getPayOrderInfo - 获取支付订单信息</td>
  </tr>
  <tr>
    <td>createQRCode - 获取小程序或小游戏的二维码</td>
  </tr>
</table>