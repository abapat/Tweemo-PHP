<?php
include 'textToSentiment.php';
require_once('TwitterAPIExchange.php');
global $settings, $twitter, $name;

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
$name = "ConanOBrien";
$id = getID($name);
$tweets = getTweets($name, $id, 400);
parseData($tweets);


/*
 * Gets URL of profile pic
 * @param userID, screen name
 * @return string, URL of profile pic
 */
function getProfilePic($id, $name) {
	$url = "https://api.twitter.com/1.1/users/show.json";
	$getfield = "?user_id=".$id."&screen_name=".$name;
	$requestMethod = "GET";
	$arr = null;
	try {
		$var = $twitter->setGetfield($getfield)
					 ->buildOauth($url, $requestMethod)
					 ->performRequest(); 
		$arr = (json_decode($var));
	} catch (Exception $e) {
		echo("Error $e");
	}
	return $arr->profile_image_url;

}

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
		//print_r($arr);
		
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
	$getfield = "?count=".$num."&exclude_replies=true&include_rts=false&user_id=".$name."&screen_name=".$name;
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

/*
 * Cleans links from tweet
 */
function cleanTweet($str) {
	$pos = stripos($str, "http");
	if ($pos === false) {
		
	}
	else {
		$str = substr($str, 0, $pos);
	}
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
		$str = cleanTweet($str);
		if (strlen($str) < 5)
			continue;
		//using alchamy
		$sentiment = getTweetSentiment($str);
		$result = array();
		$result[0] = $id;
		$result[1] = $date;
		$result[2] = $str;
		$result[3] = $sentiment->type;
		$result[4] = $sentiment->score;
		
		$resultString = getCacheString($result);
		writeData($resultString);

		echo('<br>');
		echo $resultString;
		echo('<br>');

		if ($count > 400)
			break;
		$count++;
	}

}

/**
* Given a string, it writes the string to the a file for the particular twitter user declared in the
* Global name identifier. It creates a file if a file for that user doesn't exist, and appends to it 
* if it does.
* @param: String to write to file
*/
function writeData($string){
	global $name;

	$filename = "cache".$name.".txt";
	if (!file_exists($filename)) {
    	$file = fopen($filename, "a");
	}

	file_put_contents($filename, $string, FILE_APPEND);
}

/** 
* Returns the string that needs to be written to the cache file. Modular method for getting the string, can be echoed or
* written to file.
*/
function getCacheString($arr){
	$count = 0;
	foreach($arr as &$value){
		if($count==1){
			$tweetDate = $arr[1];
			$string = $string.($tweetDate->year)."-".($tweetDate->month)."-".($tweetDate->day)."@".($tweetDate->time)." ";
		}
		else{
			$string = $string.$value." ";
		}
		$count++;
	}
	$string = $string."\n";
	return $string;
}

/**
* Return the oldest ID received in cache
* @param String to file
* @return String representation of oldest ID
*/
function getNextID($filepath){
	$line = '';

	$f = fopen($filepath, 'r');
	$cursor = -1;

	fseek($f, $cursor, SEEK_END);
	$char = fgetc($f);

	/**
	 * Trim trailing newline chars of the file
	 */
	while ($char === "\n" || $char === "\r") {
	    fseek($f, $cursor--, SEEK_END);
	    $char = fgetc($f);
	}

	/**
	 * Read until the start of file or first newline char
	 */
	while ($char !== false && $char !== "\n" && $char !== "\r") {
	    /**
	     * Prepend the new char
	     */
	    $line = $char.$line;
	    fseek($f, $cursor--, SEEK_END);
	    $char = fgetc($f);
	}

	$indexOfFirstSpace = stripos($line, ' ');
	$line = substr($line, 0, $indexOfFirstSpace);
	return $line;
}

?>