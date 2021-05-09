<?php
include('cfg.php');
// auth handler. There's nothing to change because auth process is strictly standardised
if (!isset($_COOKIE['access_token'])) {
    // auth, nothing interesting
    // step 2
    $headers = array();
    if (isset($_GET['code'])) {
        
        $headers['Content-Type'] = "application/x-www-form-urlencoded";
        $code = $_GET['code'];
        $scope = $_GET['scope'];
        $url = "https://accounts.google.com/o/oauth2/token";
        $params = array(
            "code" => $code,
            "redirect_uri" => $redirect_uri,
            "client_id" => $client_id,
            "client_secret" => $client_secret,
            "scope" => $scope,
            "grant_type" => "authorization_code"
            );
        $params = http_build_query($params);
    }
    else {
        // auth, nothing interesting
        // step 1
        $url = "https://accounts.google.com/o/oauth2/auth";
        // change scope in $params if necessary
        $params = array(
        "redirect_uri" => $redirect_uri,
        "prompt" => "consent",
        "client_id" => $client_id,
        "response_type" => "code",
        "scope" => "https://www.googleapis.com/auth/youtube.force-ssl",
        "access_type" => "offline"
        );
    }
    // sending request to googleapis
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $json_response = curl_exec($curl);
    curl_close($curl);

    // if we're in step when we don't have token
    if (strpos($json_response, 'access_token') === false) {
        echo $json_response;
        exit(0);
    }

    // if we got token in response
    $json_response = json_decode($json_response, true);
    setcookie("access_token", $json_response['access_token'],
    time() + $json_response['expires_in']);
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Создание трансляции</title>
</head>
<body>
    <h1>Wow! It works! You've just passed Google OAuth!</h1>
</body>
</html>
