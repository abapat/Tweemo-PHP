<?php
include 'textToSentiment.php';
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
$tweets = getTweets($name, $id, 15);
parseData($tweets);

/**
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

/**
 * Gets JSON data into array- tweet data. params: id, name of person, num of tweets to pull, num < 200
 */
function getTweets($name, $id, $num) {
	global $settings, $twitter;
	$url = "https://api.twitter.com/1.1/statuses/user_timeline.json";
	$getfield = "?count=".$num."&user_id=".$name."&screen_name=".$name;
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

/**
 * @param Array, tweet object
 * @return Date object, date of tweet 
 */
function getTweetDate($arr) {
	$str = $arr->created_at;
	$sArr = explode(" ", $str);
	$date = new Date();
	$date->month = $sArr[1];
	$date->day = $sArr[2];
	$date->year = $sArr[5];
	$date->time = $sArr[3];
	
	return $date;
}

/**
 * @param Array, tweet object
 * @return String, ID of tweet
 */
function getTweetID($arr) {
	$id = $arr->id_str;
	return $id;
}

/**
 * @param Array, tweet object
 * @return String, Tweet text
 */
function getTweetText($arr) {
	$str = $arr->text;
	return $str;
}
 
/**
 * Parses JSON object for tweets, gets sentiment object & writes to file with date
 * @param array of JSON data
 */
function parseData($arr) {
	$count = 0;
	foreach ($arr as $tweet) {
		$date = getTweetDate($tweet);
		$id = getTweetID($tweet);
		$str = getTweetText($tweet);
		$sentiment = getTweetSentiment($str);
		$result = array();
		$result[0] = $id;
		$result[1] = $date;
		$result[2] = $str;
		$result[3] = $sentiment->type;
		$result[4] = $sentiment->score;
		
		print_r($result);
		echo('<br>');
		if ($count > 10)
			break;
		$count++;
	}

}

function writeData($arr){
	$string = "";
	$count = 0;
	foreach($arr as &$value){
		if($count==1){
			$tweetDate = $arr["Date"];
			$string = $string.($tweetDate->year)."-".($tweetDate->month)."-".($tweetDate->day)."@".($tweetDate->time)." ";
		}
		else{
			$string = $string.$value." ";
		}
		$count++;
	}
	$string = $string."\n";
	echo $string;
	file_put_contents('cache.txt', $string, FILE_APPEND);
}


?>