
<html>
	<head>
		<title>Welcome to TweetBeat, the Twitter sentiment site!</title>
	    <!-- Bootstrap -->
	    <link href="styles/bootstrap-3.2.0/css/bootstrap.min.css" rel="stylesheet">	
	    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	    <!-- Custom styles for this template -->
	    <link href="styles/cover.css" rel="stylesheet">
	    <link href="styles/index.css" rel="stylesheet">
	    <link href="styles/clouds.css" rel="stylesheet">

	    <script src="javascript/index.js"></script>
	    <script type="text/javascript" src="https://www.google.com/jsapi"></script>

		<!--<script type="text/javascript"></script>-->
	</head>


	<!-- ~~~~~~~~~~~~~~~~~~~~ PHP Starts~~~~~~~~~~~~~~~~	 -->
	<?php
		include 'PHP/process.php';

		if (isset($_POST['value'])) {
			$twitterHandle = $_POST['value'];
		    search($twitterHandle);
		}
		
		if (isset($_GET['search'])) {
			$twitterHandle = $_GET['search'];
			openFile("Cache Files/cache".$twitterHandle.".txt");
		}
		
		//finished processing all rows; if we're connected to the google packages then render the graph
		echo("<script text=\"text/javascript\">
				if(doneLoadingGoogle)
					drawChart();
				else{
					doneLoadingRows = true;
				}
			  </script>"
		);
	?>
	<!-- ~~~~~~~~~~~~~~~~~~ HTML Starts ~~~~~~~~~~~~~~~~~~~ -->

	<body>
		<div id="clouds">
			<div class="site-wrapper">

		      <div class="site-wrapper-inner">

		        <div class="cover-container">
		        <!--
		          POTENTIALLY USEFUL NAVBAR CODE
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
		          	<!--<div id="balanceDiv"></div>-->
		          </div>
		        </div>

		        <script type="text/javascript">
					loadPackages();
				</script>

				<div>

				</div>
				<div id="chart_container">
					<br />
					<!--<img src = 'https://pbs.twimg.com/profile_images/1132696610/securedownload_normal.jpeg' /> IMAGE THING--> 
					<span id="chart_div"></span>
					<br />
					<br />
				</div>

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
