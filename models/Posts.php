<?php

namespace app\models;

use app\behaviors\PostNotification;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\Url;
use yii\web\UploadedFile;

/**
 * This is the model class for table "dev_posts".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $title
 * @property string $description
 * @property string $post
 * @property string $photo
 * @property boolean $is_active
 * @property string $created_at
 * @property string $updated_at
 *
 * @property User $user
 */
class Posts extends ActiveRecord
{
    /** @var UploadedFile */
    public $uploadPhoto;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%posts}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'title', 'description', 'post'], 'required'],
            [['user_id'], 'integer'],
            [['description', 'post'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['title'], 'string', 'max' => 255],
            [['title'], 'unique'],
            [['uploadPhoto'], 'file', 'extensions' => 'png, jpg'],
            ['is_active', 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Автор',
            'title' => 'Заголовок новости',
            'description' => 'Краткое описание новости',
            'post' => 'Полная новость',
            'photo' => 'Картинка новости',
            'is_active' => 'Показывать новость',
            'created_at' => 'Дата/время создания',
            'updated_at' => 'Дата/время обновления',
            'uploadPhoto' => 'Картинка новости',
        ];
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ],
            [
                'class' => PostNotification::className(),
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }


    /**
     * @return null|string
     */
    public function getPhotoUrl()
    {
        return empty($this->photo) ? null : Url::to([$this->photo, 'updated' => $this->updated_at]);
    }

    public function getStaticUrl()
    {
        return Url::to(['/post/view', 'id' => $this->id], true);
    }

    /**
     * загрузка картинки новости. если загрузили новую и у новости уже есть фото - удаляем его
     * @return bool
     */
    public function uploadPhoto()
    {
        $this->uploadPhoto = UploadedFile::getInstance($this, 'uploadPhoto');

        if (!is_object($this->uploadPhoto)) {
            return false;
        }

        $filePath = '/upload/' . Yii::$app->user->id . date('-d-m-Y-H-i-s') . '.' . $this->uploadPhoto->extension;

        if ($this->uploadPhoto->saveAs(Yii::getAlias('@webroot' . $filePath))) {

            if (!empty($this->photo)) {
                @unlink(Yii::getAlias('@webroot' . $this->photo));
                $this->photo = null;
            }

            $this->photo = $filePath;
            return true;
        }
        return false;
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        $this->uploadPhoto();

        return parent::beforeSave($insert);
    }
}
