<?php

/* @var $this yii\web\View */

$this->title = '站点';
?>
<div class="site-index">
    <div class="body-content">

        <div class="row">
            <div class="col-lg-4">
                <p>生产普通消息</p>
                <p><a class="btn btn-default" href="/mq/normal">Go &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <p>生产消息-事物</p>
                <p><a class="btn btn-default" href="/mq/trans">Go &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <p>转账</p>
                <p><a class="btn btn-default" href="/mq/transfer"> Go &raquo;</a></p>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4">
                <p>生产端confirm确认</p>
                <p><a class="btn btn-default" href="/mq/publish-ack">Go &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <p>生产消息-事物</
                <p><a class="btn btn-default" href="/mq/">Go &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <p>转账</p>
                <p><a class="btn btn-default" href="/mq/"> Go &raquo;</a></p>
            </div>
        </div>

    </div>
</div>
