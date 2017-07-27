<?php
/**
 * Created by PhpStorm.
 * User: serge
 * Date: 7/27/17
 * Time: 10:52 AM
 */

namespace app\lib\jwt;

use Yii;
use Firebase\JWT\JWT as libJWT;
use \yii\web\UnauthorizedHttpException;
use yii\web\Request as WebRequest;

trait JWT
{
    protected static function getJWTExpire()
    {
        return Yii::$app->params['JWT_EXPIRE'];
    }

    private static function getJWTSecret()
    {
        return Yii::$app->params['JWT_SECRET'];
    }

    public static function getAlgo()
    {
        return 'HS256';
    }

    public static function decodeJWT($token)
    {
        $secret = static::getJWTSecret();
        $errorText = "Incorrect token";
        try {
            $decoded = libJWT::decode($token, $secret, [static::getAlgo()]);
        } catch (\Exception $e) {
            if(YII_DEBUG){
                throw new UnauthorizedHttpException($e->getMessage());
            }
            else{
                throw new UnauthorizedHttpException($errorText);
            }
        }
        $decodedArray = (array)$decoded;
        return $decodedArray;
    }

    public static function encodeJWT($payload = [])
    {
        $secret = static::getJWTSecret();
        $currentTime = time();
        $request = Yii::$app->request;
        $hostInfo = '';

        // There is also a \yii\console\Request that doesn't have this property
        if ($request instanceof WebRequest) {
            $hostInfo = $request->hostInfo;
        }
        $payload['iss'] = $hostInfo;
        $payload['aud'] = $hostInfo;
        $payload['iat'] = $currentTime;
        $payload['nbf'] = $currentTime;

        if (!isset($payload['exp'])) {
            $payload['exp'] = $currentTime + static::getJwtExpire();
        }
        return libJWT::encode($payload, $secret, static::getAlgo());
    }

}