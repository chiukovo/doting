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
                Log::error($request->all());
            }

            $code = $request->input('code', '');
            $state = $request->input('state', '');
            $returnUrl = '';

            if ($code == '' || $state == '') {
                $errorMsg = '必須允許 個人檔案(必要資訊), 用戶識別資訊(必要資訊)';
                echo '<script>alert("' . $errorMsg . '");location.href="/"</script>';
            }

            try {
                $returnUrl = decrypt($state);
            } catch (DecryptException $e) {
                return 'decode error';
            }

            if ($returnUrl == '') {
                return 'auth error';
            }

            $response = LineLoginServices::getLineToken($code);

            if (isset($response->error)) {
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