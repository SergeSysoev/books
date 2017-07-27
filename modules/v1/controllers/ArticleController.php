<?php

namespace app\modules\v1\controllers;

use app\models\AccessToArticle;
use Yii;
use app\models\User;
use app\modules\v1\models\Article;
use yii\filters\auth\CompositeAuth;
use yii\rest\ActiveController;
use yii\filters\auth\HttpBearerAuth;
use yii\web\ForbiddenHttpException;

class ArticleController extends ActiveController
{
    public $modelClass = 'app\modules\v1\models\Article';

	public function behaviors()
	{
		return array_merge(parent::behaviors(), [
			'authenticator' => [
			    'except' => ['index'],
				'class' => CompositeAuth::className(),
                'authMethods' => [HttpBearerAuth::className(),],
			]
		]);
	}

    public function actions(){
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

	public function actionIndex()
    {
        if(Yii::$app->request->headers->get('Authorization'))
        {
            Yii::$app->user->setIdentity(User::findIdentityByAccessToken(
                str_replace('Bearer ', '', Yii::$app->request->headers->get('Authorization'))
            ));
        }
        $articles = Article::find()
            ->where(['user_id' => Yii::$app->user->id])
            ->orWhere(['is_private' => 0])
            ->all();
        if(!Yii::$app->user->isGuest)
        {
            $user = User::findOne(Yii::$app->user->id);
            $articles = (object) array_merge((array)$user->hasAccess()->all(), (array) $articles);
        }
        return $articles;
    }

    public function checkAccess($action, $model = null, $params = [])
    {
        if ($action === 'update' || $action === 'delete') {
            if ($model->user_id !== Yii::$app->user->id)
            {
                throw new \yii\web\ForbiddenHttpException(sprintf('You can only %s articles that you\'ve created.', $action));
            }
        } elseif($action === 'view') {
            if(!AccessToArticle::findOne(['article_id' => $model->id, 'user_id' => Yii::$app->user->id]))
            {
                throw new \yii\web\ForbiddenHttpException('You have no access to this article.');
            }
        }
    }

    public function actionOpen($articleId)
    {
        $data = json_decode(file_get_contents('php://input'));
        if(Article::findOne(['id' => $articleId, 'user_id' => Yii::$app->user->getId()]))
        {
            $access = new AccessToArticle;
            $access->user_id = $data->user_id;
            $access->article_id = $articleId;
            if($access->save())
            {
                return $access;
            }
        }
        throw new ForbiddenHttpException('You have no access to this article');
//        return ['message' => 'You have no access to this article'];
    }

    public function actionMy()
    {
        $user = User::findOne(Yii::$app->user->id);
        return $user->articles;
    }
}