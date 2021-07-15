<?php
/*
 * @Description   用户管理
 * @Author        lifetime
 * @Date          2020-12-24 09:13:03
 * @LastEditTime  2021-01-16 23:05:19
 * @LastEditors   lifetime
 */
namespace service\wechat\official;

use service\wechat\kernel\BasicWeChat;

/**
 * 用户管理
 * @class User
 */
class User extends BasicWeChat
{
    /**
     * 创建标签
     * @param   string  $name   标签名称
     * @return  array
     */
    public function createTag(string $name)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/tags/create?access_token=ACCESS_TOKEN";
        $this->registerHttp($url, __FUNCTION__, func_get_args());
        return $this->httpPostForJson(['tag' => ['name' => $name]]);
    }

    /**
     * 获取已经创建的用户标签
     * @return  array
     */
    public function getTag()
    {
        $url = "https://api.weixin.qq.com/cgi-bin/tags/get?access_token=ACCESS_TOKEN";
        $this->registerHttp($url, __FUNCTION__, func_get_args());
        return $this->httpGetForJson();
    }

    /***
     * 更新标签信息
     * @param   int     $id     微信标签id
     * @param   string  $name   名称
     * @return  array
     */
    public function updateTag(int $id, string $name)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/tags/update?access_token=ACCESS_TOKEN";
        $this->registerHttp($url, __FUNCTION__, func_get_args());
        return $this->httpPostForJson(['tag' => ['id' => $id, 'name' => $name]]);
    }

    /**
     * 删除标签
     * @param   int     $id     微信标签id
     * @return array
     */
    public function delTag(int $id)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/tags/delete?access_token=ACCESS_TOKEN";
        $this->registerHttp($url, __FUNCTION__, func_get_args());
        return $this->httpPostForJson(['tag' => ['id' => $id]]);
    }

    /**
     * 获取某个标签下的粉丝列表
     * @param   int     $tagid          微信标签id
     * @param   string  $next_openid    第一个拉取的OPENID，不填默认从头开始拉取
     * @return array
     */
    public function getTagUser(int $tagid, string $next_openid = null)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/user/tag/get?access_token=ACCESS_TOKEN";
        $this->registerHttp($url, __FUNCTION__, func_get_args());
        return $this->httpPostForJson(['tagid'=> $tagid, 'next_openid' => $next_openid]);
    }

    /**
     * 批量为用户打标签
     * @param   int     $tagid          微信标签id
     * @param   array   $openid_list    粉丝列表
     * @return  array
     */
    public function batchBindTag(int $tagid, array $openid_list)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/tags/members/batchtagging?access_token=ACCESS_TOKEN";
        $this->registerHttp($url, __FUNCTION__, func_get_args());
        return $this->httpPostForJson(['tagid' => $tagid, 'openid_list' => $openid_list]);
    }

    /**
     * 批量为用户取消标签
     * @param   int     $tagid          微信标签id
     * @param   array   $openid_list    粉丝列表
     * @return  array
     */
    public function batchUnBindTag(int $tagid, array $openid_list) {
        $url = "https://api.weixin.qq.com/cgi-bin/tags/members/batchuntagging?access_token=ACCESS_TOKEN";
        $this->registerHttp($url, __FUNCTION__, func_get_args());
        return $this->httpPostForJson(['tagid' => $tagid, 'openid_list' => $openid_list]);
    }
    
    /**
     * 获取用户身上的标签
     * @param   string  $openid         openid
     * @return  array
     */
    public function getUserTag(string $openid)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/tags/getidlist?access_token=ACCESS_TOKEN";
        $this->registerHttp($url, __FUNCTION__, func_get_args());
        return $this->httpPostForJson(['openid'=> $openid]);
    }

    /**
     * 设置用户备注名
     * @param   string  $openid         openid
     * @param   string  $remark         备注名
     * @return  array
     */
    public function updateRemark(string $openid, string $remark)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/user/info/updateremark?access_token=ACCESS_TOKEN";
        $this->registerHttp($url, __FUNCTION__, func_get_args());
        return $this->httpPostForJson(['openid' => $openid, 'remark' => $remark]);
    }

    /**
     * 获取用户基本信息(UnionID机制)
     * @param   string  $openid     openid
     * @return  array
     */
    public function getUserinfo(string $openid)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=ACCESS_TOKEN";
        $this->registerHttp($url, __FUNCTION__, func_get_args());
        return $this->httpGetForJson(['openid' => $openid, 'lang' => 'zh_CN']);
    }
    
    /**
     * 批量获取用户基本信息
     * @param   array   $open_list      openid列表
     * @return  array
     */
    public function batchGetUserInfo(array $openid_list)
    {
        $userList = [];
        foreach($openid_list as $item) $userList[] = ['openid' => $item];
        $url = "https://api.weixin.qq.com/cgi-bin/user/info/batchget?access_token=ACCESS_TOKEN";
        $this->registerHttp($url, __FUNCTION__, func_get_args());
        return $this->httpPostForJson(['user_list' => $userList]);
    }

    /**
     * 获取用户列表
     * @param   string  $next_openid    第一个拉取的OPENID，不填默认从头开始拉取
     * @return  array
     */
    public function getUserList(string $next_openid = null)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/user/get?access_token=ACCESS_TOKEN";
        $this->registerHttp($url, __FUNCTION__, func_get_args());
        return $this->httpGetForJson(['next_openid' => $next_openid]);
    }

    /**
     * 获取黑名单列表
     * @param   string  $begin_openid   begin_openid 为空时，默认从开头拉取。
     * @return  array
     */
    public function getBlackList(string $begin_openid = null)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/tags/members/getblacklist?access_token=ACCESS_TOKEN";
        $this->registerHttp($url, __FUNCTION__, func_get_args());
        return $this->httpPostForJson(['begin_openid' => $begin_openid]);
    }
    
    /**
     * 批量拉黑用户
     * @param   array   $openid_list    需要拉入黑名单的用户的openid，一次拉黑最多允许20个
     * @return  array
     */
    public function bacthBlack(array $openid_list)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/tags/members/batchblacklist?access_token=ACCESS_TOKEN";
        $this->registerHttp($url, __FUNCTION__, func_get_args());
        return $this->httpPostForJson(['openid_list' => $openid_list]);
    }

    /**
     * 批量取消拉黑用户
     * @param   array   $openid_list    需要移除黑名单的用户的openid，一次拉黑最多允许20个
     * @return  array
     */
    public function bacthUnBlack(array $openid_list)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/tags/members/batchunblacklist?access_token=ACCESS_TOKEN";
        $this->registerHttp($url, __FUNCTION__, func_get_args());
        return $this->httpPostForJson(['openid_list' => $openid_list]);
    }
}