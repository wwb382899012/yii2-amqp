<?php
namespace frontend\controllers;

use core\controllers\WebBaseController;

/**
 * MqController
 */
class MqController extends WebBaseController
{
    public function actions()
    {
        return [
            // demo列表
            'normal' => [
                'class' => 'frontend\controllers\mq\Normal',
            ],
            // 转账
            'transfer' => [
                'class' => 'frontend\controllers\mq\Transfer',
            ],
            // 生产者确认机制
            'publish-ack' => [
                'class' => 'frontend\controllers\mq\PublishAck',
            ],

        ];
    }
}
