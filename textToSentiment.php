<?php 

require_once 'Alchemy/alchemyapi_php/alchemyapi.php';
$alchemyapi = new AlchemyAPI();


$myText = "Amit is the most awesome ever";
$response = $alchemyapi->sentiment("text", $myText, null);
echo "Sentiment: ", $response["docSentiment"]["type"], " Score: ", $response["docSentiment"]["score"], PHP_EOL;

?>