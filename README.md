# 服务类库

**适用于thinkphp5+**

## 支付宝支付

## 配置
添加一个名称为`ali.php`的配置文件  
添加必须配置
```php
<?php

return [
  'sandbox' => true, // 是否是沙箱环境,
  'appid' => '', // 支付宝APPID
  'private_key' => '', // 应用私钥,
  'alipay_public_key' => '', // 支付宝公钥 
];

>
```

#### 类 **`service\ali\Pay`**

### 初始化
```php
  $alipay = service\ali\Pay::instance();
```

**`instance`**  
参数  
* 配置 `(array)`: 支付宝支付API公共参数, 参考 [支付宝支付文档](https://opendocs.alipay.com/apis/api_1/alipay.trade.pay#%E5%85%AC%E5%85%B1%E8%AF%B7%E6%B1%82%E5%8F%82%E6%95%B0), 传入的配置会覆盖掉配置文件中的配置  

返回 **`Pay`** 对象  

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

## 微信公众号

## 配置
添加一个名称为`ali.php`的配置文件  
添加必须配置
```php
<?php

return [
  'official_appid' => '', // 公众号APPID
  'official_app_secret' => '', // 公众号secert
];

>
```

#### 类 **`service\wechat\Official`**

### 初始化
```php
  $official = service\wechat\Official::instance();
```

**`instance`**  
参数  
* 配置 `(array)`: , 参考 [微信公众号文档](https://developers.weixin.qq.com/doc/offiaccount/Getting_Started/Overview.html), 传入的配置会覆盖掉配置文件中的配置  

返回 `Official` 对象

公众号授权步骤参考 [公众号授权](https://developers.weixin.qq.com/doc/offiaccount/OA_Web_Apps/Wechat_webpage_authorization.html)

### 方法

#### 1. `getCode`
获取code （授权第一步） 
参数
* 跳转地址 `(array)` ：确认授权之后跳转的地址
* 是否获取用户详情信息 `(bool)`：scope参数(默认为`true`)
* state参数 `(string)`：state参数

#### 2. `getAccessToken`
获取access_token （授权第二步） 

返回: 微信返回的信息 `(array)`

#### 3. `refreshAccessToken`
刷新 access_token 
参数：
* refresh_token `(string)`  

返回: 微信返回的信息 `(array)`

#### 4. `getUserinfo`
获取用户个人信息  
参数：
* accessToken `(string)`：调用凭证
* openid  `(string)`：用户标识  

返回: 微信返回的信息 `(array)`

#### 5. `checkAccessToken`
校验授权凭证是否有效  
参数：
* accessToken `(string)`：调用凭证
* openid  `(string)`：用户标识  

返回: 微信返回的信息 `(array)`