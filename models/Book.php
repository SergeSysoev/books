<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;
use app\lib\jwt\jwt;

/**
 * This is the model class for table "book".
 *
 * @property integer $id
 * @property string $title
 * @property integer $user_id
 *
 * @property Article[] $articles
 * @property User $user
 */
class Book extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'book';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['user_id'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'user_id' => 'User ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArticles()
    {
        return $this->hasMany(Article::className(), ['book_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function beforeSave( $insert )
    {
        $saveContinue = parent::beforeSave($insert); // если $saveContinue == false, сохранение будет отменено
        if($insert)
        {
            if ($this->isNewRecord) {
                $this->user_id = Yii::$app->user->getId();
            }
        }
        return $saveContinue ;
    }
}
