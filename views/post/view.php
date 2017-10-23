<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Posts */

$this->title = $model->title;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="posts-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Yii::$app->user->can('updatePost', ['post' => $model]) ? Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary link-as-dialog']) : '' ?>
        <?= Yii::$app->user->can('deletePost', ['post' => $model]) ? Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Подтвердите удаление новости?',
                'method' => 'post',
            ],
        ]) : '' ?>
    </p>
    <p class="image-container"><?= $model->photo ? Html::img($model->getPhotoUrl(), ['style' => 'max-width: 100%;']) : '' ?></p>
    <p><?= Html::encode($model->post) ?></p>
</div>
