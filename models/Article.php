<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "article".
 *
 * @property integer $id
 * @property string $title
 * @property string $text
 * @property integer $user_id
 * @property integer $book_id
 * @property integer $is_private
 *
 * @property AccessToArticle[] $accessToArticles
 * @property User[] $users
 * @property Book $book
 * @property User $user
 */
class Article extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'article';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['text'], 'string'],
            [['user_id', 'book_id', 'is_private'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['book_id'], 'exist', 'skipOnError' => true, 'targetClass' => Book::className(), 'targetAttribute' => ['book_id' => 'id']],
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
            'text' => 'Text',
            'user_id' => 'User ID',
            'book_id' => 'Book ID',
            'is_private' => 'Is Private',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccessToArticles()
    {
        return $this->hasMany(AccessToArticle::className(), ['article_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['id' => 'user_id'])->viaTable('access_to_article', ['article_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBook()
    {
        return $this->hasOne(Book::className(), ['id' => 'book_id']);
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
