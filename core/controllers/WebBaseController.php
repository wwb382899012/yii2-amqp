<?php


/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/19 0019
 * Time: 14:08
 */

namespace core\controllers;

use yii\base\Module;
use yii\web\Controller;

class WebBaseController extends Controller
{
    public function __construct($id, Module $module, array $config = [])
    {
        $this->setHeader();
        parent::__construct($id, $module, $config);
    }

    /**
     * 跨域请求头处理
     */
    public function setHeader()
    {
        \Yii::$app->response->headers->set('Access-Control-Allow-Origin', '*');
        \Yii::$app->response->headers->set('Access-Control-Allow-Credentials', 'true');
        \Yii::$app->response->headers->set('Access-Control-Allow-Headers', 'origin, x-requested-with, content-type');
        \Yii::$app->response->headers->set('Access-Control-Allow-Methods', 'PUT, GET, POST, DELETE, OPTIONS');
    }

}