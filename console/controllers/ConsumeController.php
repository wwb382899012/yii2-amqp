<?php
/**
 * Created by PhpStorm.
 * User: wenwb
 * Date: 2019/8/6
 * Time: 16:35
 */

namespace console\controllers;

use core\controllers\ConsoleController;

class ConsumeController extends ConsoleController{

    public function actions()
    {
        return [
            //同步租户数据
            'consume-normal'=>[
                'class'=>'console\controllers\consume\ConsumeNormal'
            ],
            'consume-transfer'=>[
                'class'=>'console\controllers\consume\ConsumeTransfer'
            ]

        ];
    }
}