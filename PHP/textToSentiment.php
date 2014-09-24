<?php 
/**
	Object that encapsulates the sentiment of a tweet 
*/
class sentiment
{
	public $score=""; //The score the tweet has recieved between 1->-1
	public $type=""; //The type of tweet, positve, negative, neutral
}

function getTweetSentiment($tweet){
	require_once 'Alchemy/alchemyapi_php/alchemyapi.php';
	$alchemyapi = new AlchemyAPI(); //Object that represents the alchemy API
	

	$firstChar = $tweet[0]; //Gets first character of the tweet
	$tweetSentiment = new sentiment;

	if($firstChar == 'R' || $firstChar == '@'){ //If it's retweet or mention, get to the meat of the tweet
		$colonIndex = strpos($tweet, ':');
		$tweet = substr($tweet, $colonIndex+1);
	}

	try{
		
		$response = $alchemyapi->sentiment("text", $tweet, null); //Send a sentiment alchemy request
		
		if (strcmp($response['status'], "ERROR") != 0) {			
			$tweetSentiment->type = $response['docSentiment']['type']; //Set the type 
			if (strcmp($tweetSentiment->type, "neutral") == 0)
				$tweetSentiment->score = 0.00;
			else
				$tweetSentiment->score = $response['docSentiment']['score']; //Set the score 
		}
	}

	catch(Exception $e){
		echo 'Caught Exception: ', $e->getMessage(), "\n"; //Catch the exception if the request breaks somehow
	}

	return $tweetSentiment; // return object
}


function getNumHashTags($line){
	return substr_count($line, '#');
}



?>