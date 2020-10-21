<!DOCTYPE html>
<html>
	<head>
		<title>Your portfolio</title>
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
		<style type="text/css">
			input[type="number"] { text-align:right; }
			input.amt { width:100px; }
			input.per { width:50px; }
			input.req { background-color:yellow; }
			.table {width:800px;}
			@media screen and (max-width: 800px){
				.table{
					width:100%;
				}
			}
		</style>
	</head>
	<body onload="checkCookies()">
		<?php include "navdefault.php" ?>
		<h1>Your portfolio</h1>
		<div>
			<h3>Total invest : <span>$<input type="number" class="assets req" id="total_invest" name="total_invest"></span><span id="profitloss"></span></h3>
		</div>
		<div>
			<table class="table table-bordered">
				<thead>
					<tr>
						<td rowspan="2">Type</td>
						<td colspan="2">Actual</td>
						<td colspan="3">To be</td>
					</tr>					
					<tr>
						<td>Amount</td>
						<td>%</td>
						<td>Amount</td>
						<td>%</td>
						<td>Gap</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>Total</td>
						<td>$<input class="assets amt actual req" type="number" min=0.01 name="total_actual_amount"></td>
						<td id="total_actual_percent">100%</td>
						<td id="total_tobe_amount"></td>
						<td><input class="assets per tobe" type="number" min=0.01 name="total_tobe_percent" value=100>%</td>
						<td>$0.00</td>
					</tr>
					<tr>
						<td>Safe</td>
						<td>$<input class="assets amt actual req" type="number" min=0.01 name="safe_actual_amount"></td>
						<td id="safe_actual_percent"></td>
						<td id="safe_tobe_amount"></td>
						<td><input class="assets per tobe req" type="number" min=0.01 name="safe_tobe_percent" value=50>%</td>
						<td id="safe_gap_amount"></td>
					</tr>
					<tr>
						<td>Risky</td>
						<td>$<input class="assets amt actual" type="number" min=0.01 name="risky_actual_amount" read-only></td>
						<td id="risky_actual_percent"></td>
						<td id="risky_tobe_amount"></td>
						<td><input class="assets per tobe req" type="number" min=0.01 name="risky_tobe_percent" value=50>%</td>
						<td id="risky_gap_amount"></td>
					</tr>
				</tbody>
			</table>
			<button class="save">Local save for later</button>
		</div>
		** <span style="background-color:yellow;"> Yellow fields</span>are required to enter.
		<div id="chart_div"></div>
		<div id="msg"></div>
		<button id="btnPrev" disabled="true">Previous</button>
		<button id="btnNext" disabled="true">Next</button>
		<button id="btnZoom">Change Zoom</button>
		<!--Load the AJAX API-->
		<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
		<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script type="text/javascript">
		// Load the Visualization API and the stackedchart package.
		google.charts.load('current', {'packages':['corechart', 'line']});
		
		// Set a callback to run when the Google Visualization API is loaded.
		google.charts.setOnLoadCallback(drawChart);
		var current = 1;
		var ddata = [];
		var MAX = new Date();
		var MIN = new Date(2018, 3, 1)
		var M6 = new Date();
		M6.setMonth(M6.getMonth()-3);
		var options = {
				// width: '90%',
				// height: 900,
				height: 900,
				chartArea: {
				height: '50%',
				width: '50%',
				top: '10%',
				left: '10%'
				},
				legend: { position: 'top', maxLines: 3 },
				bar: { groupWidth: '75%' },
				isStacked: true,//,'percent',//true,
				animation: {
					duration: 5000,
					easing: 'linear',// out linear in
					startup: true
				},
				vAxis: {
					title: "Value",
				},
				hAxis: {
					title: "Time",
					viewWindow : {min:M6, max: MAX}
				},
				trendlines: {
					0: {type: 'exponential', color: '#333', opacity: 1},
					1: {type: 'linear', color: '#111', opacity: .3}
				}
			};
		options['title'] = 'My stock chart';
		var prevButton = document.getElementById('btnPrev');
		var nextButton = document.getElementById('btnNext');
		var changeZoomButton = document.getElementById('btnZoom');
		var msg = document.getElementById('msg');
		function drawChart() {
			prevButton.disabled = true;
    		nextButton.disabled = true;
    		changeZoomButton.disabled = true;
			function getData() {
				var dd = $.ajax({
					url: "api/getData.php",
					dataType: "json",
					data: {"mode":"stock", "type":current},
					async: false
					}).responseText;
				var tmp = JSON.parse(dd);
				return tmp;
			}

			if(ddata[current] === undefined) ddata[current] = getData();
			//sample data. remove it and made 
			ddata[current] = { "cols":{"Date":"date","total_invest":"number","total":"number","safe":"number","risky":"number"},
				"rows":[ 
						{"pdate":"2019-04-05T00:00:00Z","total_invest":"50.00","total_actual_amount":"50.00","safe_actual_amount":"25.56","risky_actual_amount":"24.44"},
						{"pdate":"2019-05-08T00:00:00Z","total_invest":"520.00","total_actual_amount":"522.00","safe_actual_amount":"260.02","risky_actual_amount":"222.22"},
						{"pdate":"2019-06-17T00:00:00Z","total_invest":"550.00","total_actual_amount":"559.00","safe_actual_amount":"5.06","risky_actual_amount":"94.94"},
						{"pdate":"2019-07-10T00:00:00Z","total_invest":"600.00","total_actual_amount":"609.00","safe_actual_amount":"233.39","risky_actual_amount":"66.61"},
						{"pdate":"2019-08-01T00:00:00Z","total_invest":"550.00","total_actual_amount":"559.00","safe_actual_amount":"131.39","risky_actual_amount":"68.61"},
						{"pdate":"2019-09-06T00:00:00Z","total_invest":"450.00","total_actual_amount":"459.00","safe_actual_amount":"97.11","risky_actual_amount":"402.89"},
						{"pdate":"2019-10-10T00:00:00Z","total_invest":"520.00","total_actual_amount":"529.00","safe_actual_amount":"34.27","risky_actual_amount":"565.73"},
						{"pdate":"2019-11-01T00:00:00Z","total_invest":"50.00","total_actual_amount":"59.00","safe_actual_amount":"32.46","risky_actual_amount":"17.54"},
						{"pdate":"2019-12-04T00:00:00Z","total_invest":"50.00","total_actual_amount":"59.00","safe_actual_amount":"20.62","risky_actual_amount":"259.38"},
						{"pdate":"2020-01-03T00:00:00Z","total_invest":"50.00","total_actual_amount":"59.00","safe_actual_amount":"39.41","risky_actual_amount":"121.59"},
						{"pdate":"2020-02-03T00:00:00Z","total_invest":"600.00","total_actual_amount":"609.00","safe_actual_amount":"50.98","risky_actual_amount":"149.02"},
						{"pdate":"2020-03-03T00:00:00Z","total_invest":"700.00","total_actual_amount":"709.00","safe_actual_amount":"311.19","risky_actual_amount":"688.81"},
						{"pdate":"2020-04-01T00:00:00Z","total_invest":"1200.00","total_actual_amount":"1209.00","safe_actual_amount":"68.29","risky_actual_amount":"831.71"},
						{"pdate":"2020-05-05T00:00:00Z","total_invest":"1600.00","total_actual_amount":"1609.00","safe_actual_amount":"989.98","risky_actual_amount":"810.02"},
						{"pdate":"2020-06-01T00:00:00Z","total_invest":"210.00","total_actual_amount":"219.00","safe_actual_amount":"17.08","risky_actual_amount":"982.92"},
						{"pdate":"2020-07-01T00:00:00Z","total_invest":"260.00","total_actual_amount":"269.00","safe_actual_amount":"82.40","risky_actual_amount":"217.60"},
						{"pdate":"2020-08-05T00:00:00Z","total_invest":"310.00","total_actual_amount":"319.00","safe_actual_amount":"93.31","risky_actual_amount":"148.69"},
						{"pdate":"2020-09-01T00:00:00Z","total_invest":"348.00","total_actual_amount":"349.00","safe_actual_amount":"342.56","risky_actual_amount":"59.44"},
						{"pdate":"2020-10-01T00:00:00Z","total_invest":"348.00","total_actual_amount":"349.00","safe_actual_amount":"293.44","risky_actual_amount":"148.56"},
						{"pdate":"2020-10-20T10:25:56Z","total_invest":"350.00","total_actual_amount":"459.00","safe_actual_amount":"153.93","risky_actual_amount":"106.07"}
					]
				};
			// msg.innerHTML =JSON.stringify(ddata[current]);
			var data = new google.visualization.DataTable();
			Object.keys(ddata[current].cols).forEach(key => data.addColumn(ddata[current].cols[key], key));
			var ROWS = [];
			ROWS = ddata[current].rows.map((val) => [new Date(val.pdate), parseFloat(val.total_invest),  parseFloat(val.total_actual_amount),  parseFloat(val.safe_actual_amount),  parseFloat(val.risky_actual_amount)]);
			data.addRows(ROWS);

			var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
			// Instantiate and draw our chart, passing in some options.
			google.visualization.events.addListener(chart, 'ready',
				function() {
					prevButton.disabled = options.hAxis.viewWindow.min <= MIN;
					nextButton.disabled = options.hAxis.viewWindow.max >= MAX;
					changeZoomButton.disabled = false;
				});
			chart.draw(data, options);
		}
		prevButton.onclick = function() {
			options.hAxis.viewWindow.min.setMonth(options.hAxis.viewWindow.min.getMonth()-1);
			options.hAxis.viewWindow.max.setMonth(options.hAxis.viewWindow.max.getMonth()-1);
			console.log(options.hAxis.viewWindow.max);
			drawChart();
		}
		nextButton.onclick = function() {
			options.hAxis.viewWindow.min.getMonth()+1;
			options.hAxis.viewWindow.max.getMonth()+1;
			drawChart();
		}

		var zoomed = false;
		changeZoomButton.onclick = function() {
			if (zoomed) {
				options.hAxis.viewWindow.min = M6;
				options.hAxis.viewWindow.max = MAX;
			} else {
				options.hAxis.viewWindow.min = MIN;
				options.hAxis.viewWindow.max = MAX;
			}
			zoomed = !zoomed;
			drawChart();
		}
	</script>
	</body>
	<script type="text/javascript">
		let names = [
						"risky_actual_amount",
						"risky_remark",
						"risky_tobe_percent",
						"safe_actual_amount",
						"safe_remark",
						"safe_tobe_percent",
						"total_actual_amount",
						"total_invest",
						"total_remark",
						"total_tobe_percent"
					];
		let expdays = 10;
		let myTotalActualAmount = document.querySelector("input[name='total_actual_amount']");
		let mySafeActualAmount = document.querySelector("input[name='safe_actual_amount']");
		let myRiskyActualAmount = document.querySelector("input[name='risky_actual_amount']");

		let mySafeActualPercent = document.querySelector("td#safe_actual_percent");
		let myRiskyActualPercent = document.querySelector("td#risky_actual_percent");
		let myTotalTobeAmount = document.querySelector("td#total_tobe_amount");
		let mySafeTobeAmount = document.querySelector("td#safe_tobe_amount");
		let myRiskyTobeAmount = document.querySelector("td#risky_tobe_amount");

		let mySafeTobePercent = document.querySelector("input[name='safe_tobe_percent']");
		let myRiskyTobePercent = document.querySelector("input[name='risky_tobe_percent']");

		let mySafeGapAmount = document.querySelector("td#safe_gap_amount");
		let myRiskyGapAmount = document.querySelector("td#risky_gap_amount");

		let myProfit = document.querySelector("span#profitloss");
		let myInvest = document.querySelector("input#total_invest");

		let btnSave = document.querySelector("button.save");
		
		btnSave.onclick = save;

		myTotalActualAmount.onchange = updateByTotal;
		mySafeActualAmount.onchange = updateRiskyAmount;
		mySafeTobePercent.onchange = updateRiskyTobePercent;
		myRiskyTobePercent.onchange = updateSafeTobePercent;

		function updateRiskyTobePercent(e) {
			myRiskyTobePercent.value =  100 - e.target.value;
			updateRiskyAmount();
		}

		function updateSafeTobePercent(e) {
			mySafeTobePercent.value =  100 - e.target.value;
			updateRiskyAmount();
		}

		function updateByTotal(e) {
			myTotalTobeAmount.innerHTML =  "$ " + myTotalActualAmount.value;
			updateRiskyAmount();
			updateProfit();
		}

		function updateRiskyAmount(){
			myRiskyActualAmount.value = Math.round((myTotalActualAmount.value - mySafeActualAmount.value)*100)/100;
			mySafeActualPercent.innerHTML =  Math.round((mySafeActualAmount.value/myTotalActualAmount.value)*100) + "%";
			myRiskyActualPercent.innerHTML =  Math.round((myRiskyActualAmount.value/myTotalActualAmount.value)*100) + "%";
			mySafeTobeAmount.innerHTML = "$ " + Math.round(myTotalActualAmount.value * mySafeTobePercent.value )/100;
			myRiskyTobeAmount.innerHTML = "$ " + Math.round(myTotalActualAmount.value * myRiskyTobePercent.value)/100;

			mySafeGapAmount.innerHTML = "$ " + Math.round(mySafeActualAmount.value - parseFloat(mySafeTobeAmount.innerHTML.replace("$ ", "")));
			myRiskyGapAmount.innerHTML = "$ " + Math.round(myRiskyActualAmount.value - parseFloat(myRiskyTobeAmount.innerHTML.replace("$ ", "")));
		}

		function save() {
			let qString = "" ;
			let d = new Date() ;
			let vv = {};
			inputValues = document.querySelectorAll("input.assets");
			inputValues.forEach(cook);
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					drawChart();
				}
			};
			xhttp.open("GET", "api/putData.php", true);
			var data = {"aa":"bb"};
			xhttp.send(data);
			function cook(elem){
				nname = elem.getAttribute("name");
				vvalue = elem.value;
				setCookie(nname, vvalue, expdays);
			}
		}
		

		function checkCookies() {
		  if (navigator.cookieEnabled == true) {
		  	let ddata = setCookie2Object();
		  	names.forEach(function(name){
		  		if(ddata[name]){
		  			document.querySelector("input[name='"+name+"']").value=ddata[name];
		  		}
		  	})
		  	updateRiskyAmount();
			updateProfit();

		  } else {
		     alert("Cookies are not enabled.");
		  }
		}

		function updateProfit(){
		  	let pl = Math.round((myTotalActualAmount.value - document.querySelector("input#total_invest").value)*100)/100;
		  	if(pl < 0) {
		  		myProfit.style.color = "red";
		  	} else {
		  		myProfit.style.color = "blue";
		  	}
		  	myProfit.innerHTML= "$" + pl + "(" + Math.round(pl*100/myInvest.value) + "%)";
		}

		function setCookie(cname, cvalue, exdays) {
		  var d = new Date();
		  d.setTime(d.getTime() + (exdays*24*60*60*1000));
		  var expires = "expires="+ d.toUTCString();
		  document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";

		}

		function getCookie(cname) {
		  var name = cname + "=";
		  var decodedCookie = decodeURIComponent(document.cookie);
		  var ca = decodedCookie.split(';');
		  for(var i = 0; i <ca.length; i++) {
		    var c = ca[i];
		    while (c.charAt(0) == ' ') {
		      c = c.substring(1);
		    }
		    if (c.indexOf(name) == 0) {
		      return c.substring(name.length, c.length);
		    }
		  }
		  return "";
		}

		function setCookie2Object() {
			let name = "=";
			let tmpArray = {};
			let decodedCookie = decodeURIComponent(document.cookie);
			let ca = decodedCookie.split(';');
			for(var i = 0; i <ca.length; i++) {
				let c = ca[i];
				while (c.charAt(0) == ' ') {
					c = c.substring(1);
				}
				let cc = c.split('=');
				tmpArray[cc[0]] = cc[1];
			}
			return tmpArray;
		}

	</script>
</html>
