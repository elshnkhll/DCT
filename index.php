<?php

/*
┌───────────┬─┐
│ ELSHNKHLL │ │ 
├───────────┼─┤ 
└───────────┴─┘ 
*/

// https://en.wikipedia.org/wiki/Discrete_cosine_transform
// show all errors ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

// initial data points (x,y)
$x_rr = array( 	'Jan 2014', 'Feb 2014', 'Mar 2014', 'Apr 2014', 'May 2014', 'Jun 2014', 'Jul 2014', 'Aug 2014', 'Sep 2014', 'Oct 2014', 'Nov 2014', 'Dec 2014',
		'Jan 2015', 'Feb 2015', 'Mar 2015', 'Apr 2015', 'May 2015', 'Jun 2015', 'Jul 2015', 'Aug 2015', 'Sep 2015', 'Oct 2015', 'Nov 2015', 'Dec 2015',
		'Jan 2016', 'Feb 2016', 'Mar 2016', 'Apr 2016', 'May 2016', 'Jun 2016', 'Jul 2016', 'Aug 2016', 'Sep 2016', 'Oct 2016', 'Nov 2016', 'Dec 2016',
		'Jan 2017', 'Feb 2017', 'Mar 2017', 'Apr 2017', 'May 2017', 'Jun 2017', 'Jul 2017', 'Aug 2017', 'Sep 2017', 'Oct 2017', 'Nov 2017', 'Dec 2017',
		'Jan 2018', 'Feb 2018', 'Mar 2018', 'Apr 2018', 'May 2018', 'Jun 2018', 'Jul 2018', 'Aug 2018', 'Sep 2018', 'Oct 2018', 'Nov 2018', 'Dec 2018',
	      
	        // extra points
		'Jan 2019', 'Feb 2019', 'Mar 2019', 'Apr 2019', 'May 2019', 'Jun 2019', 'Jul 2019', 'Aug 2019', 'Sep 2019', 'Oct 2019', 'Nov 2019', 'Dec 2019' );

// values
$y_rr = array( 66.42,67.27,66.53,61.17,53.58,49.00,51.28,55.53,58.35,61.58,63.35,67.54,73.90,74.06,70.09,62.35,55.65,51.39,52.30,55.89,60.03,64.26,63.67,67.48,72.16,73.11,70.48,61.80,51.68,49.17,49.14,50.33,53.66,55.69,57.98,60.91,82.39,83.04,78.23,69.51,64.90,60.00,59.70,62.06,64.76,63.75,58.37,62.45,71.34,73.66,70.09,62.05,56.89,52.73,53.10,55.10,57.25,59.38,60.38);

// formatting with 2 decimal places
$y_rr = array_map(function($num){ return number_format($num, 2, '.', ''); }, $y_rr);

// finding frequencies using DCT-I
$freq = DCT_I( $y_rr );

// using inverse of DCT and getting 12 extrapolated values
$res = DCT_III( $freq, 12);

// formatting with 2 decimal places and imploding an array
$res_txt = implode(",", array_map(function($num){return number_format($num,2);}, $res));

// HTML
?> <!DOCTYPE html><html><head>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8">
  <title>Demo - extrapolion using Discrete cosine transform algorithm</title>
  <meta name="viewport" content="width=device-width, initial-scale=1"> 
</head>
<body>
    <script src="https://code.highcharts.com/highcharts.js"></script>
	<script src="https://code.highcharts.com/modules/exporting.js"></script>
	<script src="https://code.highcharts.com/modules/export-data.js"></script>

	<div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
<script type="text/javascript">
Highcharts.chart('container', {
    chart: {
        type: 'line'
    },
    title: {
        text: 'Monthly DOM'
    },
    subtitle: {
        text: 'extrapolated using Discrete cosine transform algorithm'
    },
    xAxis: {
        categories: ['<?php echo implode("', '", $x_rr); ?>']
    },
    yAxis: {
        title: {
            text: 'days'
        }
    },
    plotOptions: {
        line: {
            dataLabels: {
                enabled: true
            },
            enableMouseTracking: false
        }
    },
    series: [{
        name: 'existing data',
        data: [<?php echo implode(", ", $y_rr); ?>],
		color: 'blue'
    }, {
        name: 'extrapolated model data',
        data: [<?php echo $res_txt; ?>],
		color: 'red'
    }]
});
</script></body></html><?php

// function DCT-I
function DCT_I($x) {
    $results = array();
    $N = count($x);
    for ($k = 0; $k < $N; $k++) {
        if (in_array($k, [0,$N-1])) {
            $x[0] *= sqrt(2);
        }		
        $sum = 0.5*( $x[0] + pow(-1, $k) * $x[$N-1]);		
        for ($n = 1; $n <= $N-2; $n++) {
             $sum += $x[$n] * cos(  (pi()*$n*$k)/($N-1) );
        }
        $sum *= sqrt(2 / $N);
        if (in_array($k, [0,$N-1])) {
            $sum *= 1/sqrt(2);
        }
        $results[$k] = $sum;
    }
    return $results;
}

// function DCT-III
function DCT_III($x, $extr) {
    $results = array();
    $N = count($x);
    for ($k = 0; $k < $N; $k++) {
        if (in_array($k, [0])) {
            $x[0] = $x[0]/sqrt(2);
        }
        $sum = $x[0];
        for ($n = 1; $n <= $N-2; $n++) {
             $sum += $x[$n] * cos(  (pi()/$N)*$n*($k+0.5) );
        }
        $sum *= sqrt(2 / $N);
        if (in_array($k, [0])) {
            $sum *= sqrt(2)/2;
        }
        $results[$k] = $sum;
    }
	// calculating extrapolated values
    for ($k = $N+$extr; $k >= $N ; $k--) {
        $sum = $x[0];
        for ($n = 1; $n <= $N-2; $n++) {
             $sum += $x[$n] * cos(  (pi()/$N)*$n*($k-0.5) );
        }
        $sum *= sqrt(2 / $N);
        $results[$k] = $sum;
    }
    return $results;
}
