<?php 
/*
require_once 'Alchemy/alchemyapi_php/alchemyapi.php';
$alchemyapi = new AlchemyAPI();


$myText = "Amit is the most awesome ever";
$response = $alchemyapi->sentiment("text", $myText, null);
echo "Sentiment: ", $response["docSentiment"]["type"], " Score: ", $response["docSentiment"]["score"], PHP_EOL;
*/

require_once 'Alchemy/alchemyapi_php/alchemyapi.php';
$alchemyapi = new AlchemyAPI();

$handle = fopen("sampleoutput.txt", "r");
$count = 0;
$hashTagCount = 0;

if ($handle) {
    while (($line = fgets($handle)) !== false) {
        //$response = $alchemyapi->sentiment("text", $line, null);
		//echo "Count: ", $count, "Sentiment: ", $response["docSentiment"]["type"], " Score: ", $response["docSentiment"]["score"], PHP_EOL;
		if (strpos($line,'text') !== false && strpos($line, 'color') == false && strpos($line, 'RT') == false) {

			$count++;
			if($hashTagCount>0){
				$hashTagCount--;
			}
			else{
				$hashTagCount += getNumHashTags($line);
		    	echo $count.": ".$line."Hash Tag Count: "."<br/>";
		    	$response = $alchemyapi->sentiment("text", $line, null);
				echo "Sentiment: ", $response["docSentiment"]["type"], " Score: ", $response["docSentiment"]["score"]."<br/>";
			}

		}
    }
} else {
    // error opening the file.
} 
fclose($handle);

/*$response = $alchemyapi->sentiment("text", $myText, null);
echo "Sentiment: ", $response["docSentiment"]["type"], " Score: ", $response["docSentiment"]["score"], PHP_EOL;*/


function getNumHashTags($line){
	$multiplier = 1;
	if(strpos($line,'RT')){
		$multiplier = 2;
	}
	return substr_count($line, '#');
}



?>