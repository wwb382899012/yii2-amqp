<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/20 0020
 * Time: 09:49
 */

namespace core\providers\message\services;

use core\extensions\amqp\Publish;
use core\providers\BaseService;

class SendMqService extends BaseService
{
    /** 发送普通mq消息
     * @param $data string or Array
     * @return bool
     * @throws \Exception
     */
    public function sendNormalMessage($data)
    {
        try {
            $public = new Publish();
            $res = $public->sendMessage($data);
            if ($res) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            throw new \Exception('普通消息入列失败:' . $e->getMessage());
        }
    }

    /** 发送事物消息
     * @param $data string or Array
     * @return bool
     * @throws \Exception
     */
    public function sendTransMessage($data)
    {
        try {
            $public = new Publish();
            $res = $public->sendMessageTrans($data);
            if ($res) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            throw new \Exception('事物消息入列失败:' . $e->getMessage());
        }
    }

    /** 转账 mq 消息
     * @param $data string or Array
     * @return bool
     * @throws \Exception
     */
    public function sendTransferMessage($data, $durable = false)
    {
        try {
            $public = new Publish();
            $res = $public->sendMessage($data, $durable);
            if ($res) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            throw new \Exception('消息入列失败:' . $e->getMessage());
        }
    }

}