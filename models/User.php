<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use app\lib\jwt\jwt;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $login
 * @property string $password
 *
 * @property AccessToArticle[] $accessToArticles
 * @property Article[] $articles
 * @property Article[] $articles0
 * @property Book[] $books
 */
class User extends ActiveRecord implements IdentityInterface
{
    use jwt;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['login', 'password'], 'string', 'max' => 255],
            [['login'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'login' => 'Login',
            'password' => 'Password',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccessToArticles()
    {
        return $this->hasMany(AccessToArticle::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function hasAccess()
    {
        return $this->hasMany(Article::className(), ['id' => 'article_id'])->viaTable('access_to_article', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArticles()
    {
        return $this->hasMany(Article::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBooks()
    {
        return $this->hasMany(Book::className(), ['user_id' => 'id']);
    }

    public function beforeSave( $insert )
    {
        $saveContinue = parent::beforeSave($insert); // если $saveContinue == false, сохранение будет отменено
        if($insert)
        {
            if ($this->isNewRecord) {
                $this->password = Yii::$app->getSecurity()->generatePasswordHash($this->password);
            }
        }
        return $saveContinue ;
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return User::findOne(JWT::decodeJWT($token)['id']);
    }

    /**
     * Finds an identity by the given ID.
     *
     * @param string|int $id the ID to be looked for
     *
     * @return IdentityInterface the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity( $id ) {
        return static::findOne($id);
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey() {
        // TODO: Implement getAuthKey() method.
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     *
     * @param string $authKey the given auth key
     *
     * @return bool whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey( $authKey ) {
        // TODO: Implement validateAuthKey() method.
}
}
