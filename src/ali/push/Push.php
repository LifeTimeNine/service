<?php

namespace service\ali\push;

use service\ali\kernel\BasicPush;

/**
 * 推送高级接口
 * @package service\ali\push
 */
class Push extends BasicPush
{
    /**
     * 批量推送
     * @access public
     * @param   int             $appKey         AppKey
     * @param   array           $optional       参数集合（必选参数参考 push 方法）
     * @return array
     * @throws  \service\exceptions\InvalidArgumentException
     * @throws  \service\exceptions\InvalidResponseException
     */
    public function massPush(int $appKey, array $optional = [])
    {
        $this->initParam();
        $this->setOptionalParamsKeys([
            'Body','DeviceType','PushType','Target','TargetValue','JobKey','iOSSilentNotification',
            'StoreOffline','iOSSubtitle','AndroidNotificationHuaweiChannel','AndroidNotificationChannel',
            'iOSApnsEnv','iOSBadgeAutoIncrement','AndroidNotificationXiaomiChannel','AndroidPopupTitle',
            'iOSRemindBody','AndroidNotifyType','AndroidOpenUrl','AndroidBigTitle','ExpireTime','AndroidOpenType',
            'AndroidExtParameters','AndroidXiaoMiNotifyBody','AndroidXiaomiBigPictureUrl','iOSMusic','iOSRemind',
            'iOSBadge','Title','AndroidMusic','iOSNotificationCollapseId','AndroidRenderStyle','iOSNotificationCategory',
            'iOSNotificationThreadId','AndroidActivity','AndroidBigBody','iOSMutableContent','AndroidNotificationNotifyId',
            'AndroidNotificationVivoChannel','AndroidPopupActivity','AndroidRemind','AndroidPopupBody','iOSExtParameters',
            'AndroidNotificationBarPriority','AndroidNotificationBarType','PushTime','AndroidBigPictureUrl','AndroidInboxBody',
            'AndroidImageUrl','AndroidXiaomiImageUrl',
        ]);
        $this->setMassOptionalParams($optional);
        $this->setParam('Action', 'MassPush');
        $this->setParam('AppKey', $appKey);

        return $this->request();
    }
    /**
     * 推送
     * @access public
     * @param   int             $appKey         AppKey
     * @param   string          $title          标题
     * @param   string          $body           消息内容
     * @param   string          $deviceType     设备类型
     * @param   string          $pushType       推送类型
     * @param   string          $target         推送目标
     * @param   string|array    $targetValue    目标值
     * @param   array           $optional       可选参数集合
     * @return  array
     * @throws  \service\exceptions\InvalidArgumentException
     * @throws  \service\exceptions\InvalidResponseException
     */
    public function push(int $appKey, string $title, string $body, string $deviceType, string $pushType, string $target, $targetValue, array $optional = [])
    {
        $this->initParam();
        $this->setOptionalParamsKeys([
            'JobKey','StoreOffline','PushTime','ExpireTime','iOSApnsEnv','iOSRemind',
            'iOSRemindBody','iOSBadge','iOSBadgeAutoIncrement','iOSSilentNotification','iOSMusic',
            'iOSSubtitle','iOSNotificationCategory','iOSMutableContent','iOSExtParameters','AndroidNotifyType',
            'AndroidOpenType','AndroidActivity','AndroidMusic','AndroidOpenUrl','AndroidPopupActivity','AndroidPopupTitle',
            'AndroidPopupBody','AndroidNotificationBarType','AndroidNotificationBarPriority','AndroidExtParameters',
            'AndroidRemind','AndroidNotificationChannel','AndroidNotificationXiaomiChannel','SmsTemplateName','SmsSignName',
            'SmsParams','SmsDelaySecs','SmsSendPolicy','AndroidNotificationVivoChannel','AndroidNotificationHuaweiChannel',
            'AndroidNotificationNotifyId','iOSNotificationCollapseId','AndroidRenderStyle','AndroidBigTitle','AndroidBigBody',
            'AndroidXiaomiBigPictureUrl','iOSNotificationThreadId','AndroidBigPictureUrl','AndroidInboxBody','AndroidImageUrl',
            'AndroidXiaomiImageUrl',
        ]);
        $this->setOptionalParams($optional);
        $this->setParam('Action', 'Push');
        $this->setParam('AppKey', $appKey);
        $this->setParam('Title', $title);
        $this->setParam('Body', $body);
        $this->setParam('DeviceType', $deviceType);
        $this->setParam('PushType', $pushType);
        $this->setParam('Target', $target);
        if ($target == Options::TARGET_ALL) {
            $this->setParam('TargetValue', Options::TARGET_ALL);
        } elseif($target == Options::TARGET_TBD) {
            $this->setParam('TargetValue', Options::TARGET_TBD);
        } elseif ($target == Options::TARGET_TAG) {
            $this->setParam('TargetValue', is_string($targetValue)?$targetValue:json_encode($targetValue, JSON_UNESCAPED_UNICODE));
        } else {
            $this->setParam('TargetValue', is_string($targetValue)?$targetValue:implode(',',$targetValue));
        }
        return $this->request();
    }

    /**
     * 持续推送
     * @access public
     * @param   int     $appKey         AppKey
     * @param   string  $messageId      消息ID
     * @param   string  $target         推送目标
     * @param   string  $targetValue    目标值
     * @return  array
     * @throws  \service\exceptions\InvalidArgumentException
     * @throws  \service\exceptions\InvalidResponseException
     */
    public function continuouslyPush(int $appKey, string $messageId, string $target, $targetValue)
    {
        $this->initParam();
        $this->setParam('Action', 'ContinuouslyPush');
        $this->setParam('AppKey', $appKey);
        $this->setParam('MessageId', $messageId);
        $this->setParam('Target', $target);
        if (is_array($targetValue)) {
            $this->setParam('TargetValue', implode(',', $targetValue));
        } else {
            $this->setParam('TargetValue', $targetValue);
        }

        return $this->request();
    }

    /**
     * 取消定时推送任务
     * @access public
     * @param   int     $appKey     AppKey
     * @param   string  $messageId  消息ID
     * @return array
     * @throws  \service\exceptions\InvalidArgumentException
     * @throws  \service\exceptions\InvalidResponseException
     */
    public function cancelPush(int $appKey, string $messageId)
    {
        $this->initParam();
        $this->setParam('Action', 'CancelPush');
        $this->setParam('AppKey', $appKey);
        $this->setParam('MessageId', $messageId);

        return $this->request();
    }
}