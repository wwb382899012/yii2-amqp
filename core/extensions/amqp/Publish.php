<?php

namespace core\extensions\amqp;

class Publish extends BaseMq
{
    public $exchangeName = 'money'; //默认交换机名称
    public $routeKey = 'web';     //路由键
    public $exchangeType = 'direct';  //交换机类型
    public $queueName = '';  //

    public function __construct($exchangeName = null, $routeKey = null, $exchangeType = null)
    {
        $this->exchangeName = empty($exchangeName) ? $this->exchangeName : $exchangeName;
        $this->routeKey = empty($routeKey) ? $this->routeKey : $routeKey;
        $this->exchangeType = empty($exchangeType) ? $this->exchangeType : $exchangeType;
        parent::__construct($this->exchangeName, $this->queueName, $this->routeKey);
    }

    public function doProcess($msg)
    {

    }

}