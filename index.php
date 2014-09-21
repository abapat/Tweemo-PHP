<?php
require_once('TwitterAPIExchange.php');
global $settings, $twitter;

class date {
	public $month;
	public $day;
	public $year;
	public $time;
} 

$settings = array(
	'oauth_access_token' => "2822318299-taVXDHTl6kqOVKvk6giWP3ftz3rVi6mVQ6Xqns5",
	'oauth_access_token_secret' => "EtUKXY6qol06EOmAkgBSxCAvbftJ6D9q3szeX4poTR5No",
	'consumer_key' => "obpy1PjaH35sNnOztfBhmFyUX",
	'consumer_secret' => "MCM3hcxhiM09htNE9QzeUzSziaw2JsEcXqOas1pPwrGujKCodx"
);
$twitter = new TwitterAPIExchange($settings);

$name = "kanyewest";
$id = getID($name);
getTweets($name, $id);


/*
 * Given a screen name, outputs ID
*/
function getID($name) {
	global $settings, $twitter;
	$url = "https://api.twitter.com/1.1/users/lookup.json";
	$getfield = "?screen_name=" . $name;
	$requestMethod = "GET";
	$arr = null;
	try {
		$var = $twitter->setGetfield($getfield)
					 ->buildOauth($url, $requestMethod)
					 ->performRequest(); 
		$arr = (json_decode($var));
		
	} catch (Exception $e) {
		echo("Error  $e");
	}
	$id = $arr[0]->id;
	return $id;
}

/*
 * Gets JSON data into array- tweet data. params: id & name of person
*/
function getTweets($name, $id) {
	global $settings, $twitter;
	$url = "https://api.twitter.com/1.1/statuses/user_timeline.json";
	$getfield = "?count=200&user_id=".$name."&screen_name=".$name;
	$requestMethod = "GET";
	$arr = null;
	try {
		$var = $twitter->setGetfield($getfield)
					 ->buildOauth($url, $requestMethod)
					 ->performRequest(); 
		$arr = (json_decode($var));
		
	} catch (Exception $e) {
		echo("Error  $e");
	}
	return $arr;
}

/*
 * @param Array, tweet object
 * @returns Date object, date of tweet 
*/ 
function getDate($arr) {

}


/*
 * Parses JSON object for tweets, gets sentiment object & writes to file with date
 * @param array of JSON data
 * @prerequisite $arr is an array
 */
function parseData($arr) {
	$string = "";
	foreach($arr as &$value){
		$string = $string.$value;
	}
	$string = $string."\n";
	file_put_contents('cache.txt', $string, FILE_APPEND);
}


?>