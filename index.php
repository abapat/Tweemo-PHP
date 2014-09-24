
<html>
	<head>
		<title>Welcome to TweetBeat, the Twitter sentiment site!</title>
	    <!-- Bootstrap -->
	    <link href="styles/bootstrap-3.2.0/css/bootstrap.min.css" rel="stylesheet">	
	    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	    <!-- Custom styles for this template -->
	    <link href="styles/cover.css" rel="stylesheet">
	    <script src="javascript/index.js"></script>
	    <script type="text/javascript" src="https://www.google.com/jsapi"></script>

	    <style type="text/css">
			img{display:inline-block; float:left; height:300px; margin-top:75px; margin-left:50px;}
			#chart_div{width:100%; height:500px; display:inline-block; margin-left:auto; margin-right:auto;}
			ul{list-style-type:none;}
		</style>

	</head>


	<!--################### PHP Starts ################### -->
	<?php
		if (isset($_GET['search'])) {
			$twitterHandle = $_GET['search'];
			openFile("Cache Files/cache".$twitterHandle.".txt");
		}


		function openFile($filename){
			$file = fopen($filename, "r") or die("Unable to open file!");
			if ($file) {
				$latestDate = NULL;
				$totalScore = 0;
				$count = 0;
				$maxPos = array("score"=>0, "tweet"=>"");
				$maxNeg = array("score"=>0, "tweet"=>"");
				
				while (($line = fgets($file)) !== false) {
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
					//if we've past the current date, then create a new row
					else if($latestDate != NULL && strcmp($latestDate, $dateArr[0]) != 0){
						//create the string that would display in a mini-window
						$winStr = "";
						if($totalScore > 0)
							$winStr .= "Most Positive Tweet : ".$maxPos["tweet"];
						if($totalScore < 0)
							$winStr .= "Most Negative Tweet : ".$maxNeg["tweet"];
							
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
				}
			} else {
				echo "ERROR : File Not Found.";
			} 
			fclose($file);
			//finished processing all rows; if we're connected to the google packages then render the graph
			echo("<script text=\"text/javascript\">
					if(doneLoadingGoogle)
						drawChart();
					else{
						doneLoadingRows = true;
					}
				  </script>"
			);
	    }
	?>
	<!--################### HTML Starts ################### -->

	<body>
		<div id="clouds">
				<div class="site-wrapper">

			      <div class="site-wrapper-inner">

			        <div class="cover-container">
			        <!--
			          <div class="masthead clearfix">
			            <div class="inner">
			              <h3 class="masthead-brand">TweetBeat</h3>
			              <ul class="nav masthead-nav">
			                <li class="active"><a href="#">About</a></li>
			              </ul>
			            </div>
			          </div>
					-->
			          <div class="inner cover">
			            <h1 class="cover-heading">TweetBeat</h1>
			            <p class="lead">The Twitter Sentiment Analysis Tool</p>
			          </div>

			          <div id="searchFormDiv">
			          	<input type="text" id="searchBar" placeholder="Enter Twitter Handle Here!"/>
			          	<input text="Search" id="searchButton" type="submit" />
			          </div>


			        </div>
			        <script type="text/javascript">
						loadPackages();
					</script>

					<?php
						
					?>
					
					<br />
					<!--<img src = 'https://pbs.twimg.com/profile_images/1132696610/securedownload_normal.jpeg' /> IMAGE THING--> 
					<span id="chart_div"></span>
					<br />
					<br />
					
					<div id="totalCount"></div>
					<br />
					<div id="count"></div>
					<ul id="tweets">
						<li id="negMsg">
						</li>
						<ul>
							<li id="negScore"></li>
						</ul>
						<li id="posMsg">
						</li>
						<ul>
							<li id="posScore"></li>
						</ul>
					</ul>
			      </div>

				</div>

		</div>
		<div class="cloud x1"></div>
		<!-- Time for multiple clouds to dance around -->
		<div class="cloud x2"></div>
		<div class="cloud x3"></div>
		<div class="cloud x4"></div>
		<div class="cloud x5"></div>
  </body>
</html>