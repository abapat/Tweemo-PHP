<?php
//https://github.com/J7mbo/twitter-api-php
//http://iag.me/socialmedia/build-your-first-twitter-app-using-php-in-8-easy-steps/
require_once('TwitterAPIExchange.php');
//echo(function_exists('curl_version'));
$settings = array(
    'oauth_access_token' => "2822318299-taVXDHTl6kqOVKvk6giWP3ftz3rVi6mVQ6Xqns5",
    'oauth_access_token_secret' => "EtUKXY6qol06EOmAkgBSxCAvbftJ6D9q3szeX4poTR5No",
    'consumer_key' => "obpy1PjaH35sNnOztfBhmFyUX",
    'consumer_secret' => "MCM3hcxhiM09htNE9QzeUzSziaw2JsEcXqOas1pPwrGujKCodx"
);

/** Note: Set the GET field BEFORE calling buildOauth(); **/
$url = "https://api.twitter.com/1.1/statuses/user_timeline.json";

$getfield = "?user_id=155388294&screen_name=VarunForTheHill";


$requestMethod = "GET";
$twitter = new TwitterAPIExchange($settings);

echo $twitter->setGetfield($getfield)
             ->buildOauth($url, $requestMethod)
             ->performRequest(); 
echo('ok');
//phpinfo();
?>