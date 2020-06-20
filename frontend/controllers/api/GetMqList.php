<?php
/**
 * GetList.php
 * @description : demo列表
 * @create date : 2020/3/6
 */
namespace frontend\controllers\api;

use frontend\core\actions\BaseAction;
use Exception;

class GetMqList extends BaseAction
{
    public $checkParams = [
        ['page', 'default', '1'],
        ['pageSize', 'default', '20'],
        ['search', 'default', ''],
    ];


    public function run()
    {
        try {
            $this->checkParams();
            var_dump($this->requestData);

        } catch (Exception $e) {
            $this->error('-1', '系统繁忙，请稍后再试');
        }
    }
}