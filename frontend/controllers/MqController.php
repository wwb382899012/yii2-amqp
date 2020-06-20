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
            'demo-list' => [
                'class' => 'frontend\controllers\mq\DemoList',
            ],
            // 转账
            'transfer' => [
                'class' => 'frontend\controllers\mq\Transfer',
            ],

        ];
    }
}
