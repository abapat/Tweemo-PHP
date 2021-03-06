<?php
include 'textToSentiment.php';
require_once('TwitterAPIExchange.php');
global $settings, $twitter, $name, $pic, $path, $TweetsPulled, $TweetsAnalyzed, $tweets;

class date {
	public $month;
	public $day;
	public $year;
	public $time;
}
//******************** 
$TweetsPulled = 200;
$TweetsAnalyzed = 200;
//*********************
$settings = array(
	'oauth_access_token' => "2822318299-taVXDHTl6kqOVKvk6giWP3ftz3rVi6mVQ6Xqns5",
	'oauth_access_token_secret' => "EtUKXY6qol06EOmAkgBSxCAvbftJ6D9q3szeX4poTR5No",
	'consumer_key' => "obpy1PjaH35sNnOztfBhmFyUX",
	'consumer_secret' => "MCM3hcxhiM09htNE9QzeUzSziaw2JsEcXqOas1pPwrGujKCodx"
);
$twitter = new TwitterAPIExchange($settings);

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

/*
 * Searches for handle and prints semantic analysis to cache
 */
function search($term) {
	global $name, $path, $TweetsPulled, $TweetsAnalyzed, $tweets;
	
	$name = $term;
	$path = "Cache Files/cache".$name.".txt";
	$id = getID($name);
	$pic = getProfilePic($id, $name);
	$max_id = getNextID($path); //gets next tweet to cache, creates file if new cache to be made
	$tweets = getTweets($name, $id, $TweetsPulled, $max_id);
	
	
	
	if (!isset($tweets) || count($tweets) < 1) {
		echo("<script> alert('Bad Twitter Handle'); </script>");
		return;
	}
	$res = parseData($tweets, $TweetsAnalyzed);
	
}

/*
 * Gets all friends of user
 
function getFriends($name) {
	$url = "https://api.twitter.com/1.1/friends/ids.json";
	$getfield = "?screen_name=".$name."&count=5000";
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
	return $arr;
} */

/*
 * Gets URL of profile pic
 * @param userID, screen name
 * @return string, URL of profile pic
 */
function getProfilePic($id, $name) {
	global $settings, $twitter;
	
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
	$img = $arr->profile_image_url;
	return str_replace("_normal", "", $img);
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
		if (isset($arr->errors)) {
			error($arr->errors);
			return;
		}
	} catch (Exception $e) {
		echo("Error  $e");
	}
	$id = $arr[0]->id;
	return $id;
}

/*
 * Shows error, refreshes page 
 */
function error($err) {
	$str = $err[0]->message;
	echo("<script> alert('".$str."'); window.open('index.php','_self'); </script>");
}

/**
 * Gets JSON data into array- tweet data. params: id, name of person, num of tweets to pull, num < 200
 * if max ID is -1, gets most recent. Else, gets tweets older than maxID
 */
function getTweets($name, $id, $num, $maxID) {
	global $settings, $twitter;
	//$maxID = (int) $maxID;
	if ($num > 200) {
		$num = 200;
	}
		
	$url = "https://api.twitter.com/1.1/statuses/user_timeline.json";
	$getfield = "?count=".$num."&exclude_replies=true&include_rts=false&user_id=".$name."&screen_name=".$name;
	if (strcmp($maxID,"-1") != 0) {
		$getfield = $getfield."&max_id=".$maxID;
	}
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
 * Gets ID of last tweet in array parsed from JSON
 */
function getLastTweetID($arr) {
	$ind = count($arr) - 1;
	$id = $arr[$ind]->id_str;
	return $id;
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
	$str = preg_replace('/[[:^print:]]/', '', $str); //parse out weird chars
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
 * @param array of JSON data, number of tweets to analyze
 */
function parseData($arr, $num) {
	global $path;
	
	$count = 0;
	
	$flag = file_exists($path);
	$res = array(); //for debug
	$ind = 0;
	foreach ($arr as $tweet) {
		if ($flag == true) {
			$flag = false;
			continue;
		}
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
		writeInfo($result);
		writeData($resultString);
		$res[$ind++] = $resultString;
	
		if ($count > $num)
			break;
		$count++;
	}
	return $res;
}

function writeInfo($res) {
	$str = "Name: " .$name. "\tMonth: " . $result[1]->month . "\tScore: " . $res[4] . "\r\nTweet: " . $res[2] . "\r\n\r\n";
	write($str);
}

function write($str) {
	$filename = "TweetData.txt";
	if (!file_exists($filename)) {
    	$file = fopen($filename, "a");
	}

	file_put_contents($filename, $string, FILE_APPEND);
}

/**
* Given a string, it writes the string to the a file for the particular twitter user declared in the
* Global name identifier. It creates a file if a file for that user doesn't exist, and appends to it 
* if it does.
* @param: String to write to file
*/
function writeData($string){
	global $path;

	$filename = $path;
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
	$string = "";
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
	$string = $string."\r\n";
	return $string;
}

/**
* Return the oldest ID received in cache
* @param String to file
* @return String representation of oldest ID
*/
function getNextID($filepath){
	$exists = file_exists($filepath);
	if ($exists == false)
		return "-1";
	
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

function openFile($filename){
	$file = fopen($filename, "r") or die("Unable to open file!");
	if ($file) {
		$latestDate = NULL;
		$totalScore = 0;
		$count = 0;
		$maxPos = array("score"=>0, "tweet"=>"");
		$maxNeg = array("score"=>0, "tweet"=>"");
		while(true) {
			if(($line = fgets($file)) !== false){
				//gets the tweet id
				$idx = strpos($line, " ");
				$id = substr($line, 0, $idx);
				$line = substr($line, $idx+1, strlen($line));
				
				//gets the date
				$idx = strpos($line, " ");
				$date = substr($line, 0, $idx);
				$line = substr($line, $idx+1, strlen($line));
				$dateArr = explode("@", $date);
				//alters $dateArr[0] so that the graph aggregates data monthly
				$idx = strrpos($dateArr[0], "-");
				$dateArr[0] = substr($dateArr[0], 0, $idx);
				
				//gets the score
				$idx = strrpos($line, " ");
				$score = substr($line, $idx, strlen($line));
				$line = substr($line, 0, $idx);
				//this is done again b/c there was an extra space at the end of each line.
				$idx = strrpos($line, " ");
				$score = (double)substr($line, $idx, strlen($line));
				$line = substr($line, 0, $idx);
				
				//gets the tweet
				$idx = strrpos($line, " ");
				$tweet = str_replace("\"","&quot;",substr($line, 0, $idx));
			}
			//initializing first date
			if($latestDate == NULL){
				$latestDate = $dateArr[0];
				$totalScore += $score;
				//sets the maxPos/maxNeg tweet
				if($score >= 0){
					$maxPos["score"] = $score;
					$maxPos["tweet"] = $tweet;
				}
				else{
					$maxNeg["score"] = $score;
					$maxNeg["tweet"] = $tweet;
				}
			}
			//if we've past the current date or reached the end of file, then create a new row
			else if($line===false || ($latestDate != NULL && strcmp($latestDate, $dateArr[0]) != 0)){
				//create the string that would display in a mini-window					
				echo("<script text=\"text/javascript\">
						tweets.push([".($count+1).",\"".$maxNeg["tweet"]."\", ".$maxNeg["score"].", \"".$maxPos["tweet"]."\", ".$maxPos["score"]."]);
						rows.push(['".$latestDate."',".($totalScore/($count+1))."]);
					  </script>");
				$latestDate = $dateArr[0];
				$totalScore = $score;
				
				//[re]sets the maxPos and maxNeg tweet
				if($score >= 0){
					$maxPos["score"] = $score;
					$maxPos["tweet"] = $tweet;
					
					$maxNeg["score"] = 0;
					$maxNeg["tweet"] = "";
				}
				else{
					$maxNeg["score"] = $score;
					$maxNeg["tweet"] = $tweet;
					
					$maxPos["score"] = 0;
					$maxPos["tweet"] = "";
				}
				$count = 0;
			}
			else{
				$totalScore += $score;
				$count++;
				//sets the maxPos/maxNeg tweet
				if($score > $maxPos["score"]){
					$maxPos["score"] = $score;
					$maxPos["tweet"] = $tweet;
				}
				if($score < $maxNeg["score"]){
					$maxNeg["score"] = $score;
					$maxNeg["tweet"] = $tweet;
				}
			}
			//if we reached the end of the file, then exit loop
			if($line === false)
				break;
		}
	} else {
		echo "ERROR : File Not Found.";
	} 
	fclose($file);
}

?>
