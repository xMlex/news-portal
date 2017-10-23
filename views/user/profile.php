<?php

use app\models\User;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = 'Профиль ' . Yii::$app->user->identity->login;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="panel panel-default">
        <div class="panel-heading">Настройки</div>
        <div class="panel-body">
            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'post_notification_type')->dropDownList([
                User::POST_NOTIFICATION_NONE => 'Не уведомлять',
                User::POST_NOTIFICATION_EMAIL => 'Почта',
                User::POST_NOTIFICATION_MESSAGE => 'Бразуер',
            ]) ?>

            <div class="form-group">
                <?= Html::submitButton('Сохранить настройки', ['class' => 'btn btn-primary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
