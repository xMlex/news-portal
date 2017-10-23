<?php

namespace tests\models;

use app\models\LoginForm;
use app\tests\fixtures\UserFixture;
use Codeception\Specify;
use Codeception\Test\Unit;

class LoginFormTest extends Unit
{
    private $model;
    /** @var  UserFixture */
    private $fixture;

    protected function _before()
    {
        $this->fixture = new UserFixture();
        $this->fixture->load();
    }

    protected function _after()
    {
        \Yii::$app->user->logout();
        $this->fixture->unload();
    }

    public function testLoginNoUser()
    {
        $this->model = new LoginForm([
            'login' => 'not_existing_username',
            'password' => 'not_existing_password',
        ]);

        expect_not($this->model->login());
        expect_that(\Yii::$app->user->isGuest);
    }

    public function testLoginWrongPassword()
    {
        $this->model = new LoginForm([
            'login' => 'demo',
            'password' => 'wrong_password',
        ]);

        expect_not($this->model->login());
        expect_that(\Yii::$app->user->isGuest);
        expect($this->model->errors)->hasKey('password');
    }

    public function testLoginCorrect()
    {
        $this->model = new LoginForm([
            'login' => 'admin',
            'password' => 'password',
        ]);

        expect_that($this->model->login());
        expect_not(\Yii::$app->user->isGuest);
        expect($this->model->errors)->hasntKey('password');
    }

}
