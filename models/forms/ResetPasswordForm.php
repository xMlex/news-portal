<?php

namespace app\models\forms;


use app\models\User;
use yii\base\Model;

class ResetPasswordForm extends Model
{
    public $loginOrEmail;
    public $verifyCode;

    /** @var  User */
    public $user;

    public function rules()
    {
        return [
            [['loginOrEmail', 'verifyCode'], 'required'],
            ['verifyCode', 'captcha'],
            ['loginOrEmail', 'validateLoginOrEmail'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'loginOrEmail' => 'Логин или пароль',
            'verifyCode' => 'Код подтверждения',
        ];
    }

    public function validateLoginOrEmail($attribute)
    {
        /** @var User $user */
        $user = User::find()->orWhere(['login' => $this->$attribute])->orWhere(['email' => $this->$attribute])->one();

        if (is_null($user)) {
            $this->addError($attribute, 'Такой пользователь не найден');
            return;
        }
        $user->setScenario(User::SCENARIO_RESET_PASSWORD);
        $this->user = $user;
        $user->generatePasswordResetCode()->save();
    }

}