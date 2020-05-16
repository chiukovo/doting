<?php

namespace App\Services;

use Curl, DB, Session;

class LineLoginServices
{
    public static function getLineToken($code)
    {
        return Curl::to(config('lineLogin.get_token_url'))
            ->withData([
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => config('lineLogin.redirect_uri'),
                'client_id' => config('lineLogin.channel_id'),
                'client_secret' => config('lineLogin.secret')
            ])
            ->withContentType('application/x-www-form-urlencoded')
            ->asJsonResponse()
            ->post();
    }

    public static function getUserProfile($token)
    {
        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Accept'        => 'application/json',
        ];

        return Curl::to(config('lineLogin.get_user_profile_url'))
                ->withHeaders($headers)
                ->asJsonResponse()
                ->get();
    }

    public static function doLogin($userId, $displayName, $pictureUrl)
    {
        $clientIp = request()->ip();
        $date = date('Y-m-d H:i:s');
        $token = md5($userId . $date . env('APP_KEY'));

        try {
            //判斷是否有資料
            $user = DB::table('web_user')
                ->where('line_id', $userId)
                ->first();

            if (is_null($user)) {
                //create
                DB::table('web_user')->insert([
                    'line_id' => $userId,
                    'display_name' => $displayName,
                    'picture_url' => $pictureUrl,
                    'login_ip' => $clientIp,
                    'remember_token' => $token,
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);
            } else {
                //update
                DB::table('web_user')->where('line_id', $userId)
                    ->update([
                        'display_name' => $displayName,
                        'picture_url' => $pictureUrl,
                        'login_ip' => $clientIp,
                        'remember_token' => $token,
                        'updated_at' => $date,
                    ]);
            }

            //logining
            Session::put('web', [
                'lineId' => $userId,
                'displayName' => $displayName,
                'pictureUrl' => $pictureUrl,
                'token' => $token,
            ]);

            return true;
        } catch (Exception $e) {
            return false;
        }

        return false;
    }
}