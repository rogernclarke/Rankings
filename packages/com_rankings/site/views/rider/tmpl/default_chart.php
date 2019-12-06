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

<?php foreach ($this->riderhistories as $riderhistory)
{
	$effectiveDate 	= strtotime($riderhistory->effective_date) * 1000;
	$score 			= $riderhistory->score;
	$data[] 		= "[$effectiveDate, $score]";
}
?>
<script>document.addEventListener('DOMContentLoaded', function () {
		var myChart = Highcharts.chart('container', {
			/*chart: {
				type: 'bar'
			},*/
			title: {
				text: 'TT Score'
			},
			xAxis: {
				type: 'datetime',
				dateTimeLabelFormats: {day: '%b'}
				//categories: ['Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov']
			},
			yAxis: {
				title: {
					text: 'Score'
				}
			},
			series: [{
				data: [<?php echo join($data, ','); ?>]
			}]
			/*series: [{
				name: '2018',
				data: [2166, 2140, 2066, 1498, 1419, 1286, 1147, 1094, 1107, 1105]
			}, {
				name: '2019',
				data: [1101, 1119, 1107, 1091, 1149, 1148, 1196, 1199, 1093, 1083]
			}]*/
		});
	});</script>
