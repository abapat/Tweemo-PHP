$(document).ready(function(){

    $('#searchButton').click(function(){
        var searchValue = $('#searchBar').val();
        var ajaxurl = 'PHP/process.php';
        $('#chart_div').show();
        data =  {'value': searchValue};
        $.post(ajaxurl, data, function (response) {
        	window.open("/TweetBeat/index.php?search="+searchValue, "_self");
        });
    });
});

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
