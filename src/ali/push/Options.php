<?php

namespace service\ali\push;

/**
 * 移动推送相关参数
 * @package service\ali\push
 */
class Options
{
    /**
     * 设备类型 ios
     * @var string
     */
    const DEVICE_TYPE_IOS = 'IOS';
    /**
     * 设备类型 andriod
     * @var string
     */
    const DEVICE_TYPE_ANDROID = 'ANDROID';
    /**
     * 设备类型 all
     * @var string
     */
    const DEVICE_TYPE_ALL = 'ALL';

    /**
     * 推送类型 消息
     * @var string
     */
    const PUSH_TYPE_MESSAGE = 'MESSAGE';
    /**
     * 推送类型 通知
     * @var string
     */
    const PUSH_TYPE_NOTICE = 'NOTICE';

    /**
     * 推送目标类型 设备
     * @var string
     */
    const TARGET_DEVICE = 'DEVICE';
    /**
     * 推送目标类型 账号
     * @var string
     */
    const TARGET_ACCOUNT = 'ACCOUNT';
    /**
     * 推送目标类型 别名
     * @var string
     */
    const TARGET_ALIAS = 'ALIAS';
    /**
     * 推送目标类型 标签
     * @var string
     */
    const TARGET_TAG = 'TAG';
    /**
     * 推送目标类型 全部
     */
    const TARGET_ALL = 'ALL';
    /**
     * 推送目标类型 持续推送
     * @var string
     */
    const TARGET_TBD = 'TBD';

    /**
     * 提醒方式 震动
     * @var string
     */
    const NOTIFY_TYPE_VIBRATE = 'VIBRATE';
    /**
     * 提醒方式 声音
     * @var string
     */
    const NOTIFY_TYPE_SOUND = 'SOUND';
    /**
     * 提醒方式 声音和震动
     * @var string
     */
    const NOTIFY_TYPE_BOTH = 'BOTH';
    /**
     * 提醒方式 静音
     * @var string
     */
    const NOTIFY_TYPE_NONE = 'NONE';

    /**
     * 点击通知动作 打开应用
     * @var string
     */
    const OPEN_TYPE_APPLICATION = 'APPLICATION';
    /**
     * 点击通知动作 打开应用AndroidActivity
     * @var string
     */
    const OPEN_TYPE_ACTIVITY = 'ACTIVITY';
    /**
     * 点击通知动作 打开URL
     * @var string
     */
    const OPEN_TYPE_URL = 'URL';
    /**
     * 点击通知动作 无跳转
     * @var string
     */
    const OPEN_TYPE_NONE = 'NONE';

    /**
     * ClientKey类型 设备
     * @var string
     */
    const KEY_TYPE_DEVICE = 'DEVICE';
    /**
     * ClientKey类型  账号
     * @var string
     */
    const KEY_TYPE_ACCOUNT = 'ACCOUNT';
    /**
     * ClientKey类型 别名
     * @var string
     */
    const KEY_TYPE_ALIAS = 'ALIAS';
}