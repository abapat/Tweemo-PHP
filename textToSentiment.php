<?php 

function getTweetSentiment($tweet){
	require_once 'Alchemy/alchemyapi_php/alchemyapi.php';
	$alchemyapi = new AlchemyAPI();

	$firstChar = $tweet[0];


	if($firstChar == 'R' || $firstChar == '@'){
		$colonIndex = strpos($tweet, ':');
		$tweet = substr($tweet, $colonIndex+1);
	}

	try{
		$response = $alchemyapi->sentiment("text", $tweet, null);
		echo "Sentiment: ", $response["docSentiment"]["type"], " Score: ", $response["docSentiment"]["score"]."<br/>";
	}
	catch(Exception $e){
		echo 'Caught Exception: ', $e->getMessage(), "\n";
	}


}


function getNumHashTags($line){
	$multiplier = 1;
	if(strpos($line,'RT')){
		$multiplier = 2;
	}
	return substr_count($line, '#');
}



?>