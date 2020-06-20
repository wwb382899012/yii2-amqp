<?php
/**
 * BaseService.php
 */

namespace core\providers;


class BaseService
{
    public function exec($className, $methodName, $params)
    {
        if (empty($className) || empty($methodName)) {
            return false;
        }
        //1.反射类
        $ref = new \ReflectionClass($className);
        //2.创建反射实例
        $instance = $ref->newInstance($params);
        //3.获取反射实例方法
        $doAsyncMethod = $ref->getmethod($methodName);
        //4.执行方法
        $doAsyncMethod->invoke($instance);
    }

}