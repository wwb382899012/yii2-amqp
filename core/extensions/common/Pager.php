<?php
/**
 * Created by PhpStorm.
 * User: wenwb
 * Date: 2019/7/25
 * Time: 14:33
 */

namespace core\extensions\common;

class Pager
{
    public $_page;
    public $_pageSize;
    public $_totalPage;//总页数
    public $_total;//总记录数
    public $_list;//数据列表

    public function __construct($page = 1, $pageSize = 20)
    {
        $this->_page = $page;
        $this->_pageSize = $pageSize;
    }

    public function getPageSize()
    {
        return $this->_pageSize;
    }

    public function getPageOffset()
    {
        if ($this->_page < 1) {
            throw new \Exception('page非法');
        }
        return (intval($this->_page) - 1) * $this->_pageSize;
    }

    public function getAttribute()
    {
        return [
            'page' => $this->_page,
            'pageSize' => $this->_pageSize,
            'totalPage' => ceil($this->_total / $this->_pageSize),
            'total' => $this->_total,
            'list' => $this->_list
        ];
    }
}