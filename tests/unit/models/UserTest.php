<?php

namespace tests\models;


use app\tests\fixtures\AuthAssignmentFixture;
use app\tests\fixtures\PostFixture;
use app\tests\fixtures\UserFixture;
use yii\codeception\DbTestCase;
use app\models\User;

class UserTest extends DbTestCase
{
    public function fixtures()
    {
        return [
            'user' => UserFixture::className(),
            'roles' => AuthAssignmentFixture::className(),
            'post' => PostFixture::className(),
        ];
    }


    public function testFindUserById()
    {
        expect_that($user = User::findIdentity(1));
        expect($user->login)->equals('admin');

        expect_not(User::findIdentity(999));
    }

    public function testFindUserByAccessToken()
    {
        expect_that($user = User::findIdentityByAccessToken('nGoo8BNlN8ZQES5-2vkbEM06i_9a2RYJ'));
        expect($user->login)->equals('admin');

        expect_not(User::findIdentityByAccessToken('non-existing'));
    }

    public function testFindUserByUsername()
    {
        expect_that($user = User::findByLogin('admin'));
        expect_not(User::findByLogin('not-admin'));
    }

    public function testValidateUser()
    {
        expect_that($user = User::findByLogin('admin'));
        $this->assertTrue($user->validateAuthKey('nGoo8BNlN8ZQES5-2vkbEM06i_9a2RYJ'), 'Не корректный asses_tocken');
        $this->assertFalse($user->validateAuthKey('test102key'), 'Не корректный asses_tocken');

        $this->assertTrue($user->validatePassword('password'));
        $this->assertFalse($user->validatePassword('123456'));
    }

    public function testValidateManagerRules()
    {
        expect_that($user = User::findByLogin('manager'));
        expect_that(\Yii::$app->user->login($user));
        $this->assertFalse(\Yii::$app->user->can('administration'));
        $this->assertFalse(\Yii::$app->user->can('updatePost', ['postId' => 1]));
        $this->assertFalse(\Yii::$app->user->can('deletePost', ['postId' => 1]));
        $this->assertTrue(\Yii::$app->user->can('updatePost', ['postId' => 2]));
        $this->assertTrue(\Yii::$app->user->can('deletePost', ['postId' => 2]));
        $this->assertTrue(\Yii::$app->user->can('createPost'));
    }

    public function testValidateAdminRules()
    {
        expect_that($user = User::findByLogin('admin'));
        expect_that(\Yii::$app->user->login($user));
        $this->assertTrue(\Yii::$app->user->can('administration'));
        $this->assertTrue(\Yii::$app->user->can('updatePost', ['postId' => 1]));
        $this->assertTrue(\Yii::$app->user->can('deletePost', ['postId' => 1]));
        $this->assertTrue(\Yii::$app->user->can('createPost'));
    }

    public function testValidateUserRules()
    {
        expect_that($user = User::findByLogin('user'));
        expect_that(\Yii::$app->user->login($user));
        $this->assertFalse(\Yii::$app->user->can('administration'));
        $this->assertFalse(\Yii::$app->user->can('updatePost', ['postId' => 1]));
        $this->assertFalse(\Yii::$app->user->can('updatePost', ['postId' => 2]));
        $this->assertFalse(\Yii::$app->user->can('deletePost', ['postId' => 1]));
        $this->assertTrue(\Yii::$app->user->can('viewPost'));
    }

}
