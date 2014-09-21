<html>
	<head>
		<style type="text/css">
			body{color:white; background-color:black; font-family:arial;}
			h1{color:yellow;}
			img{display:inline-block; float:left; height:300px; margin-top:75px; margin-left:50px;}
			#chart_div{width:900px; height:500px; display:inline-block; float:right; margin-left:auto; margin-right:auto;}
			ul{list-style-type:none;}
			#tweets{margin-top:500px;}
		</style>
		
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
				data.addColumn('number', 'USER');
				data.addColumn({type: 'string', role: 'tooltip'});
				
				options = {
					backgroundColor: 'white',
					
					title: 'AMIT',
					pointSize: 10,
					vAxis: {
						title: 'Happiness Level', 
						gridlines: {color: 'blue'}, 
						baseline: 0, 
						baselineColor: 'red', 
						minValue: -1, 
						maxValue: 1, 
						titleTextStyle: {color: 'blue'},
						textStyle: {color: 'cornflowerblue'}
					},
					hAxis: {
						title: 'Month',
						gridlines: {color: 'blue'},
						titleTextStyle: {color: 'blue'},
						textStyle: {color: 'cornflowerblue'}
					},
					legend: {
						textStyle: {color: 'blue'},
					},
					legend: {
						position: 'none'
					},
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
			}
			
			//creates a pop-up when a dot is selected
			function selectHandler() {
				var selectedItem = chart.getSelection()[0];
				if (selectedItem) {
					var twtArr = tweets[selectedItem.row];
					if(twtArr[0] !=""){
						document.getElementById("negMsg").innerHTML="Most Negative Tweet : "+twtArr[0];
						document.getElementById("negScore").innerHTML=twtArr[1];
					}
					else{
						document.getElementById("negMsg").innerHTML="";
						document.getElementById("negScore").innerHTML="";
					}
					if(twtArr[2] != ""){
						document.getElementById("posMsg").innerHTML="Most Positive Tweet : "+twtArr[2];
						document.getElementById("posScore").innerHTML=""+twtArr[3];
					}
					else{
						document.getElementById("posMsg").innerHTML="";
						document.getElementById("posScore").innerHTML="";
					}
				}
			}
			
		</script>
		<?php
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
									tweets.push([\"".$maxNeg["tweet"]."\", ".$maxNeg["score"].", \"".$maxPos["tweet"]."\", ".$maxPos["score"]."]);
									rows.push(['".$latestDate."',".($totalScore/($count+1)).",\"".($winStr)."\"]);
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
	</head>
	<body>
		<script type="text/javascript">
			loadPackages();
		</script>
		<?php
			openfile("cacheConanOBrien.txt");
		?>
		<h1 id="team"> ASIAN SENSATION </h1>
		<div> --GHETTO ASS SEARCH BAR-- </div>
		
		<br />
		<img src = 'https://pbs.twimg.com/profile_images/1132696610/securedownload_normal.jpeg' />
		<span id="chart_div"></span>
		<br />
		<br />
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
	</body>
</html>