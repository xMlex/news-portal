<?php
/**
 * Created by Maxim Novikov
 * Date: 20.10.17 11:18
 */

namespace app\tests\fixtures;


use yii\test\DbFixture;

class AuthAssignmentFixture extends DbFixture
{
    public function init()
    {
        parent::init();
        \Yii::$app->authManager->removeAllAssignments();
        $admin = \Yii::$app->authManager->getRole('admin');
        $manager = \Yii::$app->authManager->getRole('manager');
        $user = \Yii::$app->authManager->getRole('user');

        \Yii::$app->authManager->assign($admin, 1);
        \Yii::$app->authManager->assign($manager, 2);
        \Yii::$app->authManager->assign($user, 3);
    }
}