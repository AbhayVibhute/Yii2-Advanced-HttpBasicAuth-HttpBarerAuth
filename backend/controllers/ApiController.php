<?php

namespace backend\controllers;

use yii\rest\ActiveController;
use yii\filters\auth\HttpBasicAuth;
use common\models\User;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;

/**
 * CustomerController implements the CRUD actions for Customer model.
 * http://localhost:8888/ad/backend/web/api //Wapp server
 * HttpBasicAuth: provide username and password
 * HttpBearerAuth: provide access_token
 * Run ./yii migrate command
 * create access_token column in user table
 */
class ApiController extends ActiveController
{
    public $modelClass = 'backend\models\Blog';

    //first call
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
            'authMethods' => [
                ['class' => HttpBasicAuth::className(), 'auth' => [$this, 'auth']],
                HttpBearerAuth::className(),
            ],
        ];
        return $behaviors;
    }

    public function Auth($username, $password)
    {
        // username, password are mandatory fields
        if (empty($username) || empty($password))
            return null;

        // get user using requested email
        $user = User::findOne([
            'username' => $username,
        ]);

        // if no record matching the requested user
        if (empty($user))
            return null;

        // validate password
        $isPass = $user->validatePassword($password);

        // if password validation fails
        if (!$isPass)
            return null;

        // if user validates (both user_email, user_password are valid)
        return $user;
    }
}
