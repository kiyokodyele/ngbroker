<?php

$username = "<your_ngrok_email>";
$password = "<your_ngrok_password>";

// ############################################################################################################################### //

$url = "https://dashboard.ngrok.com/user/login";
$cookie = getcwd() . "\koo.keys";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6"); 
curl_setopt ($ch, CURLOPT_REFERER, $url); 

$response = curl_exec($ch);

if (curl_errno($ch)) die(curl_error($ch));
$dom = new DomDocument();
$dom->loadHTML($response);
$tokens = $dom->getElementsByTagName("input");
for ($i = 0; $i < $tokens->length; $i++)
{
    $input = $tokens->item($i);
    if($input->getAttribute('name') == 'csrf_token'){
        $token = $input->getAttribute('value');
    }
}

if (!isset($token )) die ('csrd_token could not be found!');

$postinfo = "email=".$username."&password=".$password."&csrf_token=".$token;

curl_setopt($ch, CURLOPT_URL, "https://dashboard.ngrok.com/user/login");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postinfo);

$response = curl_exec($ch);
    
if (curl_errno($ch)) {
    print curl_error($ch);
}
else {
    curl_setopt($ch, CURLOPT_URL, "https://dashboard.ngrok.com/status");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_POST, false);
    $response = curl_exec($ch);
    //print($response);
}


$dom = new DomDocument();
$dom->loadHTML($response);
$div = $dom->getElementById("preloaded");
$data_value = $div->getAttribute('data-value'); 
$json = json_decode($data_value, true);
$http = $json['online_tunnels'][0];
$url1 = $http['url'];
$https = $json['online_tunnels'][1];
$url2 = $https['url'];

echo 'url1: ' . $url1 . '<br>';
echo 'url2: ' . $url2;
//die($data_value);
    
curl_close($ch);

unlink($cookie);

header('Location: ' . $url2);