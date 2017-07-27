<?php

namespace app\modules\v1\controllers;

use app\modules\v1\models\User;
use app\lib\jwt\jwt;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;

class UserController extends ActiveController
{
    use jwt;

    public $modelClass = 'app\modules\v1\models\User';

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['delete'], $actions['update']);

        return $actions;
    }

    public function actionAuth()
    {
        $parsedJSON = json_decode(file_get_contents("php://input"));
        $user = User::find()->where(['login' => $parsedJSON->login])->one();
        if (\Yii::$app->getSecurity()->validatePassword($parsedJSON->password, $user->password)) {
            return [
                'token' => JWT::encodeJWT(['id' => $user->id]),
            ];
        } else {
            throw new NotFoundHttpException('User not found. Please, check your credentials.');
        }
    }

}