<?php
/** @var \app\models\User $user */

?>

<p>Уважаемый <?= $user->login ?>, вы зарегистрировались на сайте "<?= Yii::$app->name ?>", для продолжения регистрации
    перейдите по ссылке:</p>
<p><?= \yii\helpers\Html::a('Продолжить регистрацию', $user->getRegistrationLink()) ?></p>