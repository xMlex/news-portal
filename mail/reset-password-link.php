<?php
/** @var \app\models\User $user */

use yii\helpers\Html;

?>

<p>Уважаемый <?= $user->login ?>, вы запросили восстановление доступа на сайте "<?= Yii::$app->name ?>", для продолжения
    перейдите по ссылке:</p>
<p><?= Html::a('Восстановить пароль', $user->getPasswordResetLink()) ?></p>