<?php
/*
 * @Description   微信公众平台 模板消息
 * @Author        lifetime
 * @Date          2020-12-19 08:53:10
 * @LastEditTime  2020-12-22 08:51:29
 * @LastEditors   lifetime
 */

namespace service\wechat\official;

use service\wechat\kernel\BasicWeChat;

class Template extends BasicWeChat
{
    /**
     * 设置所属行业
     * @param string $industry_id1 公众号模板消息所属行业编号
     * @param string $industry_id2 公众号模板消息所属行业编号
     * @return array
     */
    public function setIndustry($industry_id1, $industry_id2)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/template/api_set_industry?access_token=ACCESS_TOKEN";
        $this->registerHttp($url, __FUNCTION__, func_get_args());
        return $this->httpPostForJson(['industry_id1' => $industry_id1, 'industry_id2' => $industry_id2]);
    }

    /**
     * 获取设置的行业信息
     * @return array
     */
    public function getIndustry()
    {
        $url = "https://api.weixin.qq.com/cgi-bin/template/get_industry?access_token=ACCESS_TOKEN";
        $this->registerHttp($url, __FUNCTION__, func_get_args());
        return $this->httpGetForJson($url);
    }

    /**
     * 获得模板ID
     * @param string $tpl_id 板库中模板的编号，有“TM**”和“OPENTMTM**”等形式
     * @return array
     */
    public function addTemplate($tpl_id)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/template/api_add_template?access_token=ACCESS_TOKEN";
        $this->registerHttp($url, __FUNCTION__, func_get_args());
        return $this->httpPostForJson(['template_id_short' => $tpl_id]);
    }

    /**
     * 获取模板列表
     * @return array
     */
    public function getAllPrivateTemplate()
    {
        $url = "https://api.weixin.qq.com/cgi-bin/template/get_all_private_template?access_token=ACCESS_TOKEN";
        $this->registerHttp($url, __FUNCTION__, func_get_args());
        return $this->httpGetForJson();
    }

    /**
     * 删除模板
     * @param   string  $tpl_id     模板id
     * @return array
     */
    public function delPrivateTemplate($tpl_id)
    {
        $url = "POST https://api.weixin.qq.com/cgi-bin/template/del_private_template?access_token=ACCESS_TOKEN";
        $this->registerHttp($url, __FUNCTION__, func_get_args());
        return $this->httpPostForJson(['template_id' => $tpl_id]);
    }

    /**
     * 发送模板消息
     * @param   array   $data   模板消息数据
     * @return  array
     */
    public function send(array $data)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=ACCESS_TOKEN";

        $this->registerHttp($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($data);
    }
}
