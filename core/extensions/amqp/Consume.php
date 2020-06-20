<?php

namespace core\extensions\amqp;

class Consume extends BaseMq
{
    public $exchangeName = 'money'; //

    public $queueName = 'change.money';    //队列名,已和交换机绑定

    public $routeKey = 'web';     //路由键

    public $exchangeType = 'direct';  //交换机类型

    public $consumeClass; //消费者业务逻辑类

    public $consumeFunction; //消费者业务逻辑实现

    public function __construct($config)
    {
        $this->setAttribute($config);
        parent::__construct($this->exchangeName, $this->queueName, $this->routeKey);
    }

    private function setAttribute($config)
    {
        $this->exchangeName = empty($config['exchangeName']) ? $this->exchangeName : $config['exchangeName'];
        $this->routeKey = empty($config['routeKey']) ? $this->routeKey : $config['routeKey'];
        $this->queueName = empty($config['queueName']) ? $this->queueName : $config['queueName'];
        $this->exchangeType = empty($config['exchangeType']) ? $this->exchangeType : $config['exchangeType'];
        $this->consumeClass = $config['class'];
        $this->consumeFunction = $config['method'];
    }

    public function doProcess($msg)
    {
        $this->exec($this->consumeClass, $this->consumeFunction, $msg);
    }

    public function exec($className, $methodName, $msg)
    {
        if (empty($className) || empty($methodName)) {
            return false;
        }
        //1.反射类
        $ref = new \ReflectionClass($className);
        //2.创建反射实例
        $instance = $ref->newInstance();
        //3.获取反射实例方法
        $doAsyncMethod = $ref->getmethod($methodName);
        //4.执行方法
        $doAsyncMethod->invoke($instance, $msg);
    }
}

