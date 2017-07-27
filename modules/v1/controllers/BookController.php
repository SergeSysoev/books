<?php

namespace app\modules\v1\controllers;

use app\modules\v1\models\Book;
use app\modules\v1\models\User;
use Yii;
use yii\filters\auth\CompositeAuth;
use yii\rest\ActiveController;
use yii\filters\auth\HttpBearerAuth;

class BookController extends ActiveController
{
    public $modelClass = 'app\modules\v1\models\Book';

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
        unset($actions['view']);
        return $actions;
    }

    public function checkAccess($action, $model = null, $params = [])
    {
        // check if the user can access $action and $model
        // throw ForbiddenHttpException if access should be denied
        if ($action === 'update' || $action === 'delete') {
            if ($model->user_id !== Yii::$app->user->id)
                throw new \yii\web\ForbiddenHttpException(sprintf('You can only %s books that you\'ve created.', $action));
        }
    }

    public function actionView($id)
    {
        return Book::findOne($id)->articles;
    }

    public function actionMy()
    {
        $user = User::findOne(Yii::$app->user->id);
        return $user->books;
    }
}