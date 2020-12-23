# 服务类库

**适用于thinkphp5+**

添加配置文件 `lifetime-service.php`  
添加配置
```php
<?php
  'ali' => [ // 阿里（支付宝）相关配置
    // ···
  ],
  'wechat' => [ // 微信相关配置
    // ···
  ],
  'byteDance' => [ // 字节跳动相关配置
    // ···
  ],
  'cache_path' => '', // 缓存目录
  'cache_callable' => [ // 自定义缓存操作方法（如果设置了此参数，缓存目录将不再生效）
        'set' => null, // 写入缓存
        'get' => null, // 获取缓存
        'del' => null, // 删除缓存
        'put' => null, // 写入文件
    ]
>
```

所有的类都可以使用 `instance` 方法初始化  
比如
```php
<?php
  
  Pay::instance();

>
```

参数  
* 配置 `(array)`：此配置会覆盖掉配置文件中的配置  

## 支付宝支付

### 配置
添加一个名称为`ali`的配置项  
添加必须配置
```php
<?php

return [
  'ali' => [
    'sandbox' => true, // 是否是沙箱环境,
    'appid' => '', // 支付宝APPID
    'private_key' => '', // 应用私钥,
    'alipay_public_key' => '', // 支付宝公钥 
  ]
];

>
```
参考 [支付宝支付文档](https://opendocs.alipay.com/apis/api_1/alipay.trade.pay#%E5%85%AC%E5%85%B1%E8%AF%B7%E6%B1%82%E5%8F%82%E6%95%B0)

#### 类 `Pay`

命名空间 `service\ali`

### 方法
#### 1. `page`
Web页面支付  
参数  
* 订单信息 `(array)`: 支付宝网站支付订单参数, 参考 [支付宝支付API文档](https://opendocs.alipay.com/apis/api_1/alipay.trade.page.pay#%E8%AF%B7%E6%B1%82%E5%8F%82%E6%95%B0)
* 异步通知地址 `(string)`: 	支付宝服务器主动通知商户服务器里指定的页面http/https路径
* 同步跳转地址 `(string)`: 支付完成之后页面跳转的地址  

返回数据  构建好的、签名后的最终跳转URL（GET）或String形式的form （POST）

#### 2. `wap`
手机网站支付  
参数  
* 订单信息 `(array)`: 支付宝网站支付订单参数, 参考 [支付宝支付API文档](https://opendocs.alipay.com/apis/api_1/alipay.trade.wap.pay#%E8%AF%B7%E6%B1%82%E5%8F%82%E6%95%B0)
* 异步通知地址 `(string)`: 	支付宝服务器主动通知商户服务器里指定的页面http/https路径
* 同步跳转地址 `(string)`: 支付完成之后页面跳转的地址  

返回数据  构建好的、签名后的最终跳转URL（GET）或String形式的form （POST）

#### 3. `app`
Web页面支付  
参数  
* 订单信息 `(array)`: 支付宝网站支付订单参数, 参考 [支付宝支付API文档](https://opendocs.alipay.com/apis/api_1/alipay.trade.app.pay#%E8%AF%B7%E6%B1%82%E5%8F%82%E6%95%B0)
* 异步通知地址 `(string)`: 	支付宝服务器主动通知商户服务器里指定的页面http/https路径

返回数据  调起支付宝支付的 String 参数

#### 4. `notify`
异步通知处理  
参数  
* 验证完成执行的函数 `(callable)`：可以接收两个参数，1.支付宝通知的信息，2.签名验证结果; 如果函数返回false，会给支付宝返回失败的消息

返回数据  返回给支付宝的消息

#### 5. `query`
订单查询  
参数  
* 参数 `(array)`：查询参数， 参考 [支付宝支付API文档](https://opendocs.alipay.com/apis/api_1/alipay.trade.query#%E8%AF%B7%E6%B1%82%E5%8F%82%E6%95%B0)
* 查询完成执行的函数 `(callable)`：可以接收两个参数，1.支付宝返回的信息，2.签名验证结果;

#### 6. `refund`
退款  
参数  
* 参数 `(array)`：退款参数， 参考 [支付宝支付API文档](https://opendocs.alipay.com/apis/api_1/alipay.trade.refund#%E8%AF%B7%E6%B1%82%E5%8F%82%E6%95%B0)
* 退款完成执行的函数 `(callable)`：可以接收两个参数，1.支付宝返回的信息，2.签名验证结果;

#### 7. `refundQuery`
退款查询  
参数  
* 参数 `(array)`：查询参数， 参考 [支付宝支付API文档](https://opendocs.alipay.com/apis/api_1/alipay.trade.fastpay.refund.query#%E8%AF%B7%E6%B1%82%E5%8F%82%E6%95%B0)
* 查询完成执行的函数 `(callable)`：可以接收两个参数，1.支付宝返回的信息，2.签名验证结果;

---

## 微信

### 配置
添加一个名称为`wechat`的配置项  
添加必须配置
```php
<?php

return [
  'wechat' => [
    'cache_path' => '', // 缓存目录
    'official_appid' => '', // 公众号APPID
    'official_app_secret' => '', // 公众号secert
    'mch_id' => '', // 商户ID
    'mch_key' => '', // 商户支付密钥
    'ssl_cer' => '', // 证书cert.pem路径
    'ssl_key' => '', // 证书key.pem路径
  ]
];

>
```

## `Pay`

命名空间 `service\wechat`

微信支付类

参考 [微信支付开发文档](https://pay.weixin.qq.com/wiki/doc/apiv3/wxpay/pages/api.shtml)

微信支付不同的场景所需要的参数不同，因此使用方法如下
```php
<?php
use service\wechat\Pay;
Pay::instance()->jsApi()->pay();
>
```
`Pay::instance()` 初始化支付类 `jsApi()` 切换至微信JSAPI场景 `pay()` 传入订单参数返回支付信息，呵！，一气呵成！！！

微信支付的场景有  
* micropay 付款码支付 参考 [微信付款码支付](https://pay.weixin.qq.com/wiki/doc/api/micropay.php?chapter=5_1)
* jsapi   JSAPI支付 参考 [微信JSAPI支付](https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=7_1)
* native  Native支付 参考 [微信Native支付](https://pay.weixin.qq.com/wiki/doc/api/native.php?chapter=6_1)
* app APP支付 参考 [微信APP支付](https://pay.weixin.qq.com/wiki/doc/api/app/app.php?chapter=8_1)
* h5  H5支付  参考 [微信H5支付](https://pay.weixin.qq.com/wiki/doc/api/H5.php?chapter=15_1)
* miniApp 小程序支付  参考 [微信小程序支付](https://pay.weixin.qq.com/wiki/doc/api/wxa/wxa_api.php?chapter=7_3&index=1)

每个场景都包含以下方法:  
* `pay`：下单支付
* `query`：查询订单
* `refund`：退款申请
* `refundQuery`：退款查询
* `notify`：异步通知验签  

具体参数请参考各场景的API文档

## `Official`

开放平台相关接口

命名空间 `service\wechat`

使用示例：
```php
<?php
use service\wechat\Official;
Official::instance()->oauth()->getCode();
>
```
`instance()`初始化类,`oauth()`切换网页授权场景,`getCode`：获取code

目前支持的场景有:
* `oauth`：网页授权 
* `template`：模板消息 

### `oauth`

微信网页授权

参考 [微信公众号授权](https://developers.weixin.qq.com/doc/offiaccount/OA_Web_Apps/Wechat_webpage_authorization.html)

### 方法
* `getCode` ：请求Code（授权第一步）
* `getUserAccessToken` ：通过Code获取Access_token （授权第二步）, 注意：此access_token与基础支持的access_token不同
* `refreshAccessToken` ：刷新Access_token
* `getUserinfo` ：获取用户个人信息
* `checkAccessToken` ：校验凭证是否有效
* `getJsSdkSign` ：获取JS-SDK使用权限
### `template`
微信模板消息

参考 [微信公众号模板消息](https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Template_Message_Interface.html)

### 方法
* `setIndustry` ：设置所属行业
* `getIndustry` ：获取所属行业
* `addTemplate` ：获得模板ID
* `getAllPrivateTemplate` : 获取模板列表
* `delPrivateTemplate` ：删除模板
* `send` ：发送模板消息

---

## 字节跳动

### 配置
添加一个名字为`byteDance`的配置项
添加配置, 如:
```php
<?php
  return [
    'byteDance' => [
      'cache_path' => '', // 缓存目录
      'miniapp_appid' => '', // 字节小程序APPID
      'miniapp_secret' => '', // 字节小程序APP Secret
    ]
  ];
>
```

## `MiniApp`

命名空间 `service\byteDance`

使用示例:
```php
<?php
use service\byteDance\MiniApp;
MiniApp::instance()->getAccessToken();
>
```

字节小城相关接口类

参考 [字节小程序文档](https://microapp.bytedance.com/docs/zh-CN/mini-app/develop/server/server-api-introduction)

### 方法
* `getAccessToken`：获取头条平台Access_token
* `code2Session`：获取session_key和appId
* `checkUserInfo`：验证用户信息

## 常用工具

### `Tools`

包含的方法:  
* `request`：CURL模拟网络请求
* `getAge`: 根据出生年月日获取年龄
* `createOrderSn`：生成订单编号
* `isJson`：判断是否是JSON字符串
* `getMillisecond`：获取毫秒级时间戳
* `getMonthDay`：获取某个月的天数
* `deldir`：删除文件夹及其所有子文件
