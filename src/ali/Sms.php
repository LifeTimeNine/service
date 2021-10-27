<?php

namespace service\ali;

use service\ali\kernel\BasicSms;
use service\exceptions\InvalidArgumentException;

/**
 * 短信服务
 * @class   Sms
 */
class Sms extends BasicSms
{
    /**
     * 发送短信
     * @param   string  $phones         接收短信的手机号，支持对多个手机号码发送短信，手机号码之间以英文逗号（,）分隔
     * @param   array   $param          模板参数
     * @param   string  $signName       短信签名名称(传空表示从配置文件中获取[sms_signName])
     * @param   string  $templateCode   短信模板ID(传空表示从配置文件中获取[sms_templateCode])
     * @param   string  $extendCode     上行短信扩展码
     * @param   string  $outId          外部流水扩展字段
     * @return  mixed
     */
    public function send(string $phones, array $param,string $signName=null,string $templateCode=null,string $extendCode=null,string $outId=null)
    {
        $this->initParam();
        $this->setParam('Action', 'SendSms');
        $this->setParam('PhoneNumbers', $phones);
        if (empty($signName)) $signName = $this->config['sms_signName'];
        $this->setParam('SignName', $signName);
        if (empty($templateCode)) $templateCode = $this->config['sms_templateCode'];
        $this->setParam('TemplateCode', $templateCode);
        $this->setParam('TemplateParam', json_encode($param));
        if (!empty($extendCode)) $this->setParam('SmsUpExtendCode', $extendCode);
        if (!empty($outId)) $this->setParam('OutId', $outId);
        return $this->request();
    }

    /**
     * 批量发送短信
     * @param   array   $data           参数[[number=>手机号, signName=>签名名称(传空表示从配置文件中获取), param => 参数, extndCode=>扩展码(可选,如果有所有的都必须有)]]
     * @param   string  $templateCode   模板Code(传空表示从配置文件中获取[sms_templateCode])
     * @return  array
     */
    public function sendBatch(array $data, string $templateCode='')
    {
        $this->initParam();
        $number = []; $signName = []; $param = []; $extendCode = [];
        foreach($data as $k => $v)
        {
            if (empty($v['number'])) throw new InvalidArgumentException("Missing Option [number:key=>{$k}]");
            $number[] = $v['number'];
            $signName[] = empty($v['signName']) ? $this->config['sms_signName'] : $v['signName'];
            $param[] = json_encode($v['param']);
            if (!empty($v['extendCde'])) $extendCode[] = $v['extendCode'];
        }
        if (!empty($extendCode) && count($number) <> count($extendCode)) throw new InvalidArgumentException("Missing Option [extendCode]");
        $this->setParam('Action', 'SendBatchSms');
        $this->setParam('PhoneNumberJson', json_encode($number));
        $this->setParam('SignNameJson', json_encode($signName));
        $this->setParam('TemplateCode', empty($templateCode) ? $this->config['sms_templateCode'] : $templateCode);
        $this->setParam('TemplateParamJson', json_encode($param));
        if (!empty($extendCode)) $this->setParam('SmsUpExtendCodeJson', $extendCode);
        return $this->request();
    }

    /**
     * 添加模板
     * @param   int     $type       短信类型[0-验证码,1-短信通知,2-推广短信,3-国际/港澳台消息]
     * @param   string  $name       模板名称
     * @param   string  $content    模板内容
     * @param   string  $remark     申请说明
     * @return  array
     */
    public function addTemplate(int $type,string $name,string $content,string $remark)
    {
        $this->initParam();
        $this->setParam('Action', 'AddSmsTemplate');
        $this->setParam('TemplateType', $type);
        $this->setParam('TemplateName', $name);
        $this->setParam('TemplateContent', $content);
        $this->setParam('Remark', $remark);
        return $this->request();
    }

    /**
     * 查询模板审核状态
     * @param   string  $code       模板code
     * @return  array
     */
    public function queryTemplate(string $code)
    {
        $this->initParam();
        $this->setParam('Action', 'QuerySmsTemplate');
        $this->setParam('TemplateCode', $code);
        return $this->request();
    }

    /**
     * 修改未通过审核的短信模板
     * @param   string  $code       模板code
     * @param   int     $type       短信类型[0-验证码,1-短信通知,2-推广短信,3-国际/港澳台消息]
     * @param   string  $name       模板名称
     * @param   string  $content    模板内容
     * @param   string  $remark     申请说明
     * @return  array
     */
    public function modifyTemplate(string $code,int $type,string $name,string $content,string $remark)
    {
        $this->initParam();
        $this->setParam('Action', 'ModifySmsTemplate');
        $this->setParam('TemplateCode', $code);
        $this->setParam('TemplateType', $type);
        $this->setParam('TemplateName', $name);
        $this->setParam('TemplateContent', $content);
        $this->setParam('Remark', $remark);
        return $this->request();
    }

    /**
     * 删除短信模板
     * @param   string  $code       模板code
     * @return  array
     */
    public function deleteTemplate(string $code)
    {
        $this->initParam();
        $this->setParam('Action', 'DeleteSmsTemplate');
        $this->setParam('TemplateCode', $code);
        return $this->request();
    }
}