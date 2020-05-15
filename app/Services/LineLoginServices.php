<?php

namespace App\Services;

use Curl;

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
}