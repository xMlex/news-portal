<?php

use yii\helpers\Html;
use yii\helpers\HtmlPurifier;

/** @var $model \app\models\Posts */

?>

<div class="row short-post-item">
    <h2><?= Yii::$app->user->can('viewPost') ?
            Html::a($model->title, ['/post/view/', 'id' => $model->id]) :  Html::encode($model->title) ?></h2>
    <?= Yii::$app->user->can('updatePost',['post' => $model]) ?
        Html::a('Изменить', ['/post/update/', 'id' => $model->id], ['class' => 'btn btn-info link-as-dialog']) : '' ?>
    <p><?= $model->photo ? Html::img($model->getPhotoUrl(), ['class' => 'photo']) : '' ?></p>
    <p><?= HtmlPurifier::process($model->description) ?></p>
</div>
