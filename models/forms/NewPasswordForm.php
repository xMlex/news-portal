<?php

namespace app\models\forms;


use app\models\User;
use yii\base\Model;

class NewPasswordForm extends Model
{
    public $password;
    public $password_verify;

    public function rules()
    {
        return [
            [['password', 'password_verify'], 'required'],
            ['password_verify', 'compare', 'compareAttribute' => 'password'],
            [['password', 'password_verify'], 'string', 'min' => 6]
        ];
    }

    public function attributeLabels()
    {
        return [
            'password' => 'Новый пароль',
            'password_verify' => 'Подтверждение пароля',
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

        if ($user->sendResetPassword() && $user->generatePasswordResetCode()->save()) {
            \Yii::$app->session->addFlash('success', 'Мы выслали вам ссылку, для восстановления доступа на email, указанный при регистрации');
        } else {
            \Yii::$app->session->addFlash('danger', 'Из за технических проблем, мы не смогли выслать вам письмо, попробуйте позже');
        }
    }
}