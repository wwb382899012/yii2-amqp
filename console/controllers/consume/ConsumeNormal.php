<?php
/**
 * Created by PhpStorm.
 * User: wenwb
 * Date: 2019/8/6
 * Time: 16:42
 */

namespace console\controllers\consume;

use core\providers\message\services\ConsumeMqService;
use yii\base\Action;

class ConsumeNormal extends Action
{
    public function run()
    {
        try {
            $service = new ConsumeMqService();
            $service->consumeMessage(ConsumeMqService::CONSUME_BUSINESS_TYPE_NORMAL);
        } catch (\Exception $e) {
            \Yii::error($e);
            echo '消费异常：' . $e->getMessage();
        }
    }


}