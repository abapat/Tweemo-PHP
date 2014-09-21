
<html>
	<head>
		<title>Welcome to TweetBeat, the Twitter sentiment site!</title>
	    <!-- Bootstrap -->
	    <link href="styles/bootstrap-3.2.0/css/bootstrap.min.css" rel="stylesheet">	
	    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	    <!-- Custom styles for this template -->
	    <link href="styles/cover.css" rel="stylesheet">

	</head>


	<script type="text/javascript">
    	$(document).ready(function(){
		    $('#searchButton').click(function(){
		        var searchValue = $('#searchBar').val();
		        alert(searchValue);
		        var ajaxurl = 'process.php';
		        data =  {'value': searchValue};
		        $.post(ajaxurl, data, function (response) {
		            alert("action performed successfully");
		        });
		    });

		});
    </script>




	<body>
		<div id="clouds">
				<div class="site-wrapper">

			      <div class="site-wrapper-inner">

			        <div class="cover-container">

			          <div class="masthead clearfix">
			            <div class="inner">
			              <h3 class="masthead-brand">TweetBeat</h3>
			              <ul class="nav masthead-nav">
			                <li class="active"><a href="#">About</a></li>
			              </ul>
			            </div>
			          </div>

			          <div class="inner cover">
			            <h1 class="cover-heading">TweetBeat</h1>
			            <p class="lead">The Twitter Sentiment Analysis Tool</p>
			            
			          </div>

			          <div id="searchFormDiv">
			          	<input type="text" id="searchBar" placeholder="Enter Twitter Handle Here!"/>
			          	<input text="Search" id="searchButton" type="submit" />
			          </div>

			              
			        </div>
			      </div>

			        </div>

			      </div>

			    </div>

				<div class="cloud x1"></div>
				<!-- Time for multiple clouds to dance around -->
				<div class="cloud x2"></div>
				<div class="cloud x3"></div>
				<div class="cloud x4"></div>
				<div class="cloud x5"></div>
		</div>

		</div>
  </body>
</html>



<?php

	function completed(){
		echo "Execution Completed";
	}

?>