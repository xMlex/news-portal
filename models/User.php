<?php

namespace app\models;

use app\behaviors\UserNotification;
use yii\base\Security;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\IdentityInterface;

/**
 * Class User
 * @package app\models
 * @property int $id
 * @property string $login
 * @property string $email
 * @property string $password
 * @property string $access_token
 * @property string $activation_code
 * @property string $password_reset_code
 * @property boolean $is_active
 * @property boolean $is_validated
 * @property integer $post_notification_type
 * @property string $created_at
 * @property string $logged_at
 *
 * @property PostNotifications[] $postNotifications
 * @property PostNotifications[] $unreadPostNotifications
 */
class User extends ActiveRecord implements IdentityInterface
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';
    const SCENARIO_REGISTRATION = 'registration';
    const SCENARIO_VALIDATION = 'validation';
    const SCENARIO_RESET_PASSWORD = 'resetPassword';

    const POST_NOTIFICATION_NONE = 0;
    const POST_NOTIFICATION_MESSAGE = 1;
    const POST_NOTIFICATION_EMAIL = 2;

    public $originalPassword;

    public static function tableName()
    {
        return '{{%users}}';
    }

    public function attributeLabels()
    {
        return [
            'login' => 'Логин',
            'password' => 'Пароль',
            'is_active' => 'Активен',
            'is_validated' => 'Пользователь подтвердил email',
            'post_notification_type' => 'Уведомлять о новых новостях',
            'created_at' => 'Дата регистрации',
            'logged_at' => 'Последний вход на сайт',
        ];
    }

    public function scenarios()
    {
        return [
            self::SCENARIO_DEFAULT => ['password', 'post_notification_type'],
            self::SCENARIO_CREATE => ['is_active', 'login', 'email', 'password', 'post_notification_type'],
            self::SCENARIO_UPDATE=> ['is_active', 'password', 'post_notification_type'],
            self::SCENARIO_REGISTRATION => ['login', 'email', 'password'],
            self::SCENARIO_VALIDATION => ['is_active', 'is_validated'],
            self::SCENARIO_RESET_PASSWORD => ['password', 'password_reset_code'],
        ];
    }

    public function rules()
    {
        return [
            [['login', 'email'], 'unique'],
            [['login', 'password', 'email'], 'required'],
            [['logged_at', 'password_reset_code'], 'safe'],
            [['email'], 'email'],
            [['password'], 'string', 'min' => 6],
            [['created_at', 'logged_at'], 'safe'],
            [['post_notification_type'], 'integer'],
            [['is_active', 'is_validated'], 'boolean'],
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'logged_at',
                'value' => new Expression('NOW()'),
            ],
            [
                'class' => UserNotification::className(),
                'fieldEmail' => 'email',
                'fieldPassword' => 'password',
            ]
        ];
    }

    public function beforeSave($insert)
    {
        if ($insert || $this->isAttributeChanged('password')) {
            $this->generateAuthKey();
            $this->cryptPassword();
        }
        return parent::beforeSave($insert);
    }

    /**
     * @return bool
     */
    public function sendResetPassword()
    {
        return \Yii::$app->mailer->compose('reset-password-link', ['user' => $this])
            ->setTo($this->email)
            ->setSubject('Восстановление доступа')
            ->send();
    }

    public function getRegistrationLink()
    {
        return Url::to([
            '/user/registration-validate',
            'id' => $this->id,
            'code' => $this->getRegistrationCode(),
        ], true);
    }

    public function getRegistrationCode()
    {
        return md5(\Yii::$app->request->cookieValidationKey . $this->login . $this->email);
    }

    public function generatePasswordResetCode()
    {
        $this->password_reset_code = md5(\Yii::$app->request->cookieValidationKey . $this->password . $this->email);
        return $this;
    }

    public function getPasswordResetLink()
    {
        return Url::to([
            '/user/reset-password',
            'id' => $this->id,
            'code' => $this->password_reset_code
        ], true);
    }

    public function validateRegistrationCode($code)
    {
        return $this->getRegistrationCode() == $code;
    }

    public function cryptPassword()
    {
        $this->originalPassword = $this->password;
        $this->password = md5(md5($this->password));
    }

    public function updatePassword($password)
    {
        $this->password = $password;
        $this->cryptPassword();
        return $this->save();
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    /**
     * Finds user by username
     *
     * @param string $login
     * @return static|null
     */
    public static function findByLogin($login)
    {
        return static::find()->andWhere(['login' => $login, 'is_active' => 1])->one();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->access_token;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->access_token === $authKey;
    }

    public function generateAuthKey()
    {
        $this->access_token = \Yii::$app->security->generateRandomString();
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return $this->password === md5(md5($password));
    }

    public static function afterLogin($event)
    {
        $event->identity->touch('logged_at');
    }

    /**
     * @return ActiveQuery
     */
    public static function findActive()
    {
        return self::find()->where(['is_active' => 1]);
    }

    public function getPostNotifications()
    {
        return $this->hasMany(PostNotifications::className(), ['user_id' => 'id']);
    }

    public function getUnreadPostNotifications()
    {
        return $this->getPostNotifications()->where(['is_read' => 0]);
    }
}
