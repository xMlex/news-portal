<?php

/* @var $this yii\web\View */

/* @var $dataProvider \yii\data\ActiveDataProvider */


use app\widgets\PageSize;
use yii\widgets\ListView;

$this->title = Yii::$app->name;
?>
<div class="site-index">
    <?= PageSize::widget(['label' => 'Новостей на странице']); ?>
    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'itemView' => '_short',
    ]) ?>
</div>
