<?php 


$client_id = '2660149137580603';
$client_secret ='e3b2be4390b617ba815389753b5a1736';
$redirect_uri = 'https://socialsizzle.herokuapp.com/auth/';
$code ='AQD_lBoIFFDrGnX1GsMFf3S1pk4mcYDEReXGPXZ6ifJGkFX_lu-SWBFhPduVW9371j7m2ARRMVRVtRsiY_FLGDfmGGYFseLJqegujqMzASlzSE-RRxFYnZQFLnuJObjbEWoH5BUYevO08FGRvY_CSXHOjQvcKeioEEgeMZz-mHXmqThAhfGajoKFcdyegY5eT7diQUSLPq5L0GhQOnueR98w0FiNh0IjCZedrw0OczRfdw';

$url = "https://api.instagram.com/oauth/access_token";
$access_token_parameters = array(
    'client_id'                =>     $client_id,
    'client_secret'            =>     $client_secret,
    'grant_type'               =>     'authorization_code',
    'redirect_uri'             =>     $redirect_uri,
    'code'                     =>     $code
);


$url = 'https://api.instagram.com/oauth/access_token';
$curl = curl_init($url);    // we init curl by passing the url
curl_setopt($curl,CURLOPT_POST,true);   // to send a POST request
curl_setopt($curl,CURLOPT_POSTFIELDS,$access_token_parameters);   // indicate the data to send
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);   // to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly.
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);   // to stop cURL from verifying the peer's certificate.
$result = curl_exec($curl);   // to perform the curl session
curl_close($curl);   // to close the curl session

var_dump($result);