
<html>
	<head>
		<title>Welcome to TweetBeat, the Twitter sentiment site!</title>
	    <!-- Bootstrap -->
	    <link href="styles/bootstrap-3.2.0/css/bootstrap.min.css" rel="stylesheet">	
	    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	    <!-- Custom styles for this template -->
	    <link href="styles/cover.css" rel="stylesheet">

	    <style type="text/css">
			img{display:inline-block; float:left; height:300px; margin-top:75px; margin-left:50px;}
			#chart_div{width:100%; height:500px; display:inline-block; margin-left:auto; margin-right:auto;}
			ul{list-style-type:none;}
		</style>

	 </head>

	<script type="text/javascript">
    	$(document).ready(function(){
   
		    $('#searchButton').click(function(){
		        var searchValue = $('#searchBar').val();
		        var ajaxurl = 'process.php';
		        $('#chart_div').show();
		        data =  {'value': searchValue};
		        $.post(ajaxurl, data, function (response) {
		        	window.open("/TweetBeat/index.php?search="+searchValue, "_self");
		        });
		        //ajaxurl = 'index.php';
		        //alert(searchValue);
		        //$.post(ajaxurl, data, function (response) {
		    
		        //});
		    });
		});
    </script>

    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
		<script type="text/javascript">
			var data, options;
			var rows = new Array();
			var doneLoadingGoogle = false;
			var doneLoadingRows = false;
			var chart;
			var tweets = new Array();
			
			function loadPackages(){
				google.load("visualization", "1", {packages:["corechart"]});
				google.setOnLoadCallback(setChart);
			}
			
			//sets up the columns and options
			function setChart(){
				data = new google.visualization.DataTable();
				data.addColumn('string', 'Month');
				data.addColumn('number', 'Sentiment');
				//data.addColumn({type: 'string', role: 'tooltip'});
				
				options = {
					backgroundColor: 'transparent',
					
					//title: 'AMIT',
					pointSize: 10,
					vAxis: {
						title: 'Happiness Level', 
						gridlines: {color: '#0084b4'}, 
						baseline: 0, 
						baselineColor: 'red', 
						minValue: -1, 
						maxValue: 1, 
						titleTextStyle: {color: '#0084b4'},

						textStyle: {color: '#0084b4'}
					},
					hAxis: {
						title: 'Month',
						gridlines: {color: '#0084b4'},
						titleTextStyle: {color: '#0084b4'},
						textStyle: {color: '#0084b4'}
						//slantedTextAngle: {'90'};
					},
					legend: {
						textStyle: {color: 'blue'},
					},
					legend: {
						position: 'none'
					},
					series: {0:{color:'#0084b4'}},
					titleTextStyle: {
						color: 'black',
						fontSize: 24
					}/*,
					curveType: 'function'*/
				};
				
				drawChart();
			}
			
			//renders the chart
			function drawChart() {
				if(!doneLoadingRows){
					doneLoadingGoogle = true;
					return;
				}
				data.addRows(rows);
				chart = new google.visualization.LineChart(document.getElementById('chart_div'));

				chart.draw(data, options);
				google.visualization.events.addListener(chart, 'select', selectHandler	); 
				
				setTotalCount();
			}
			
			//creates a pop-up when a dot is selected
			function selectHandler() {
				var selectedItem = chart.getSelection()[0];
				if (selectedItem) {
					var twtArr = tweets[selectedItem.row];
					document.getElementById("count").innerHTML="Number of Tweets : "+twtArr[0];
					if(twtArr[1] !=""){
						document.getElementById("negMsg").innerHTML="Most Negative Tweet : "+twtArr[1];
						document.getElementById("negScore").innerHTML="Sentiment : "+twtArr[2];
					}
					else{
						document.getElementById("negMsg").innerHTML="";
						document.getElementById("negScore").innerHTML="";
					}
					if(twtArr[3] != ""){
						document.getElementById("posMsg").innerHTML="Most Positive Tweet : "+twtArr[3];
						document.getElementById("posScore").innerHTML="Sentiment : "+twtArr[4];
					}
					else{
						document.getElementById("posMsg").innerHTML="";
						document.getElementById("posScore").innerHTML="";
					}
				}
			}
			
			function setTotalCount(){
				var total = 0;
				for(i = 0; i < tweets.length; i++)
					total += tweets[i][0];
				document.getElementById("totalCount").innerHTML = "Total Tweets : "+total;
			}
		</script>

		<!-- ********************* PHP STARTS HERE *************************** -->

		<?php
			if (isset($_GET['search'])) {
				$twitterHandle = $_GET['search'];
    			openFile("cache".$twitterHandle.".txt");
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



<?php
	
	
	function echoString($string){
		echo $string;
	}


?>