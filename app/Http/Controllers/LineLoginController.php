<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\LineLoginServices;
use Illuminate\Contracts\Encryption\DecryptException;
use Curl, Log, DB;

class LineLoginController extends Controller
{
    public function callback(Request $request)
    {
        try {
            $error = $request->input('error', false);

            if ($error) {
                $clientIp = request()->ip();
                Log::error($clientIp);
                Log::error($request->all());
                $errorMsg = '必須允許 個人檔案(必要資訊), 用戶識別資訊(必要資訊)';
                echo '<script>alert("' . $errorMsg . '");location.href="/"</script>';
            }

            $code = $request->input('code', '');
            $state = $request->input('state', '');
            $returnUrl = '';

            if ($code == '' || $state == '') {
                Log::error('params error');
                return 'params error';
            }

            try {
                $returnUrl = decrypt($state);
            } catch (DecryptException $e) {
                Log::error('decode error: ' . $state);
                return 'decode error';
            }

            if ($returnUrl == '') {
                Log::error('auth error');
                return 'auth error';
            }

            $response = LineLoginServices::getLineToken($code);

            if (isset($response->error)) {
                Log::error('token error code: ' . $code);
                return 'token error';
            }

            $userProfile = LineLoginServices::getUserProfile($response->access_token);

            if (!isset($response->error)) {
                $userId = $userProfile->userId;
                $displayName = $userProfile->displayName;
                $pictureUrl = isset($userProfile->pictureUrl) ? $userProfile->pictureUrl : '';

                //do login
                if ($userId != '' && !is_null($userId)) {
                    $auth = LineLoginServices::doLogin($userId, $displayName, $pictureUrl);

                    if (!$auth) {
                        return 'login error';
                    }

                    if (!preg_match("/doting/i", $returnUrl)) {
                        return redirect('/');
                    }

                    //success
                    return redirect($returnUrl);
                }
            }
        } catch (Exception $e) {
            Log::error($e);
        }
    }

    public function logout()
    {
        LineLoginServices::doLogout();

        return redirect()->back();
    }
}