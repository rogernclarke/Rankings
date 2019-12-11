<?php
/**
 * Rankings Component for Joomla 3.x
 *
 * @version    2.0
 * @package    Rankings
 * @subpackage Component
 * @copyright  Copyright (C) 2019 Spindata. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
?>

<?php

$seriesName = null;

foreach ($this->districtCounts as $districtCount)
{
	if ($seriesName != "$districtCount->year")
	{
		if ($seriesName != null)
		{
			$series['name'] = $seriesName;
			$output = join($seriesData, ',');
			$series['data'] = [$output];
			$data[$seriesName] = json_encode($series);
			$seriesData = array();
		}

		$seriesName 	= "$districtCount->year";
	}

	$xAxis[] 		= "'$districtCount->district_name'";
	$seriesData[]	= $districtCount->total;
}

$series['name'] = $seriesName;
$output = join($seriesData, ',');
$series['data'] = [$output];
$data[$seriesName] = json_encode($series);

$xAxis = array_unique($xAxis);

$json_without_quotes = str_replace('"', "", join($data, ',')); // Strip the double-quotes out of the JSON; the SQL concatenates single quotes where they are needed.

 // Print the quote-stripped JSON to test it
//echo $json_without_quotes;


$xAxis = array_unique($xAxis);
?>
<script>document.addEventListener('DOMContentLoaded', function () {
		var myChart = Highcharts.chart('container', {
			chart: {
				type: 'column'
			},
			title: {
				text: 'Number of events by district'
			},
			xAxis: {
				categories: [<?php echo join($xAxis, ','); ?>]
			},
			yAxis: {
				min: 0,
				title: {
					text: 'Number of events'
				}
			},
			series: [<?php echo $json_without_quotes; ?>]
		});
	});</script>
