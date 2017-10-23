<?php
/**
 * Created by Maxim Novikov
 * Date: 19.10.17 15:33
 */

use yii\helpers\Html;

/** @var \app\models\User $user */
?>
<p>Уважаемый <?= $user->login ?>, вы только что сменили пароль на сайте "<?= Yii::$app->name ?>" .</p>
<p>Внимание! Если это сделали не вы, воспользуйтесь ссылкой на восстановление пароля: <?= Html::a('Восстановить пароль', $user->getPasswordResetLink()) ?></p>
