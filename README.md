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
本类库封装了一些常见的平台的部分接口  
>调用的逻辑为：平台::业务->功能->操作  
比如：微信公众平台获取用户信息
```php
$order = WeCat::official()->user()->getUserInfo();
```

目前包含的平台有:
1. 支付宝和阿里云
2. 微信
3. 字节跳动

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
    <td rowspan=15>uer - 用户管理</td>
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