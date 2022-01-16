<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    public function index()
    {
        if (!empty(session('access_token'))) {
            return "HOME PAGE";
        }
        return redirect('/auth/twitch?redirectTo=home');
    }

    public function authTwitchAPI(Request $request)
    {
        $code = $request->query('code');
        $redirectToPage = $request->query('redirectTo');
        if (!empty($redirectToPage))
            session(['redirectTo' => $redirectToPage]);

        if (!empty($code)) {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://id.twitch.tv/oauth2/token?client_id=mgyp247gosmi3pyj0h30he5ojy45kn&client_secret=ovnb3pwqigsnuwa412uzgbp5ac5xdf&code=' . $code . '&grant_type=authorization_code&redirect_uri=http://localhost:8000/auth/twitch',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
            ));

            $response = curl_exec($curl);
            curl_close($curl);

            if (!empty($response)) {
                $authData = json_decode($response);
                if (!empty($authData->access_token)) {
                    session(['access_token' => $authData->access_token]);
                    $redirectToPage = session('redirectTo');
                    session(['redirectTo' => null]);
                    if ($redirectToPage == 'home') {
                        return redirect('/');
                    } else if ($redirectToPage == 'streams') {
                        return redirect('/streams');
                    } else if ($redirectToPage == 'seed-streams-data') {
                        return redirect('/streams/seed-data');
                    }
                }
            }
            return "Login Failed!";
        } else {
            $redirectURL = "https://id.twitch.tv/oauth2/authorize";
            $redirectURL .= "?client_id=" . env('TWITCH_CLIENT_ID');
            $redirectURL .= "&redirect_uri=http://localhost:8000/auth/twitch";
            $redirectURL .= "&response_type=code";
            $redirectURL .= "&scope=user:edit";


            return redirect($redirectURL);
        }
    }
}
