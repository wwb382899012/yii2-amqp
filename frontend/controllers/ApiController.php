<?php
namespace frontend\controllers;

use core\controllers\ApiBaseController;

/**
 * MqController
 */
class ApiController extends ApiBaseController
{
    public function actions()
    {
        return [
            // demo列表
            'list' => [
                'class' => 'frontend\controllers\mq\DemoList',
            ],

        ];
    }
}
