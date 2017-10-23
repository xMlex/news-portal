<?php

use app\models\User;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'is_active')->checkbox() ?>

    <?= $form->field($model, 'post_notification_type')->dropDownList([
        User::POST_NOTIFICATION_NONE => 'Не уведомлять',
        User::POST_NOTIFICATION_EMAIL => 'Почта',
        User::POST_NOTIFICATION_MESSAGE => 'Бразуер',
    ]) ?>

    <?= $model->isNewRecord ? $form->field($model, 'login')->textInput(['maxlength' => true]) : '' ?>

    <?= $model->isNewRecord ? $form->field($model, 'email')->textInput(['maxlength' => true]) : '' ?>

    <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Изменить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
