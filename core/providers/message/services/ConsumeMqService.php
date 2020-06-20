<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/20 0020
 * Time: 09:49
 */

namespace core\providers\message\services;

use core\extensions\amqp\Consume;
use core\providers\BaseService;
use core\providers\message\business\ConsumeUserAccountService;

class ConsumeMqService extends BaseService
{
    const CONSUME_BUSINESS_TYPE_NORMAL = 'normal';
    const CONSUME_BUSINESS_TYPE_TRANSFER = 'transfer';//转账

    public $amqp_consume_config = [

        self::CONSUME_BUSINESS_TYPE_NORMAL => [
            'class' => ConsumeUserAccountService::class,
            'method' => 'normal'
        ],

        self::CONSUME_BUSINESS_TYPE_TRANSFER => [
            'class' => ConsumeUserAccountService::class,
            'method' => 'transfer'
        ],
    ];

    /** 消费处理
     * @param $data string or Array
     * @return bool
     * @throws \Exception
     */
    public function consumeMessage($businessType = null)
    {
        try {
            if (isset($this->amqp_consume_config[$businessType])) {
                $config = $this->amqp_consume_config[$businessType];
                $consume = new Consume($config);
                $res = $consume->dealMq(true); //true自动应答
                if ($res) {
                    return true;
                } else {
                    return false;
                }
            }

        } catch (\Exception $e) {
            throw new \Exception('消息入列失败:' . $e->getMessage());
        }
    }

}