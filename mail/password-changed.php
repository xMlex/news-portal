<?php
/**
 * Created by Maxim Novikov
 * Date: 19.10.17 15:35
 */
/** @var \app\models\User $user */
?>
<p>Уважаемый <?= $user->login ?>, вы изменили пароль на сайте "<?= Yii::$app->name ?>" . Теперь вам доступнен полный
    функционал.</p>
<p>Новый пароль: <?= $user->originalPassword ?></p>
<p>Спасибо</p>