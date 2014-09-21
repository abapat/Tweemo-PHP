<html>
	<head>
		<style type="text/css">
			body{color:white; background-color:black; font-family:arial;}
			h1{color:yellow;}
			img{display:inline-block; float:left; height:300px; margin-top:75px; margin-left:50px;}
			#chart_div{width:900px; height:500px; display:inline-block; float:right; margin-left:auto; margin-right:auto;}
			#friendsList{margin-top:500px;}
		</style>
		
		<script type="text/javascript" src="https://www.google.com/jsapi"></script>
		<script type="text/javascript">
			var data, options;
			function loadPackages(){
				google.load("visualization", "1", {packages:["corechart"]});
				google.setOnLoadCallback(setChart);
			}
			
			function setChart(){
				data = new google.visualization.DataTable();
				data.addColumn('string', 'Month');
				data.addColumn('number', 'USER');
				data.addColumn({type: 'string', role: 'tooltip'});
				
				
				options = {
					backgroundColor: 'black',
					
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
						color: 'white',
						fontSize: 24
					}/*,
					curveType: 'function'*/
				};
				
				addRows();
				drawChart();
			}
			
			function addRows(){
				data.addRows([
					['Jan', 1, 'postive tweet : =)\nnegative tweet : =('],
					['Feb', 0.9, 'postive tweet : =)\nnegative tweet : =('],
					['Mar', -0.7, 'postive tweet : =)\nnegative tweet : =('],
					['Apr', 0.8, 'postive tweet : =)\nnegative tweet : =('],
					['May', -0.2, 'postive tweet : =)\nnegative tweet : =(']
				]);
				data.addRows([
					['Jun', 0.5, 'postive tweet : =)\nnegative tweet : =('],
					['Jul', 0.7, 'postive tweet : =)\nnegative tweet : =('],
					['Aug', 0.8, 'postive tweet : =)\nnegative tweet : =('],
					['Sept', -0.4, 'postive tweet : =)\nnegative tweet : =(']
				]);
			}
			
			function drawChart() {
				var chart = new google.visualization.LineChart(document.getElementById('chart_div'));

				chart.draw(data, options);
				//google.visualization.events.addListener(chart, 'select', selectHandler); 
			}
			/*
			function selectHandler() {
				alert('A table row was selected');
			}
			*/
		</script>
	</head>
	<body>
		<h1 id="team"> ASIAN SENSATION </h1>
		<div> --GHETTO ASS SEARCH BAR-- </div>
		
		<br />
		<img src = 'https://pbs.twimg.com/profile_images/1132696610/securedownload_normal.jpeg' />
		<span id="chart_div"></span>
		
		<div id="friendsList"> --BESTIES-- 
			<ul>
				<li>MOM</li>
				<li>f0r3vr@10ne</li>
			</ul>
		</div>
		
		<script type="text/javascript">
			loadPackages();
		</script>
	</body>
</html>