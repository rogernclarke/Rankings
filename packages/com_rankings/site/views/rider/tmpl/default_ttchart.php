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

$provisionalScoreData 	= array();
$qualifiedScoreData 	= array();
$previousRankingStatus  = null;

foreach ($this->ttriderhistories as $riderhistory)
{
	$historyPoint['x'] 	= strtotime($riderhistory->effective_date) * 1000;
	$historyPoint['y'] 	= (int) $riderhistory->score;

	switch ($riderhistory->ranking_status)
	{
		case "P":

			if ($previousRankingStatus == 'Q')
			{
				array_push($provisionalScoreData, $previousHistoryPoint);
			}

			array_push($provisionalScoreData, $historyPoint);
			$previousRankingStatus = 'P';
			break;

		case "C":
		case "F":
			array_push($qualifiedScoreData, $historyPoint);

			if ($previousRankingStatus == 'P')
			{
				array_push($provisionalScoreData, $historyPoint);
			}

			$previousRankingStatus = 'Q';
			break;

		default:
			break;
	}

	$previousHistoryPoint = $historyPoint;
}

$countingRideData 	= array();
$discountedRideData = array();

foreach ($this->ttRides as $ride)
{
	$point['x'] 		= strtotime($ride->event_date) * 1000;
	$point['y'] 		= (int) $ride->ranking_points;
	$point['name'] 		= $ride->event_name;
	$point['distance'] 	= $ride->distance;

	if ($ride->counting_ride_ind)
	{
		array_push($countingRideData, $point);
	}
	else
	{
		array_push($discountedRideData, $point);
	}
}
$year = $this->state->get('filter.year');
?>

<script>
	Highcharts.setOptions({
		lang: {
			decimalPoint: '.',
			thousandsSep: ','
		}
	});

	document.addEventListener('DOMContentLoaded', function () {
		var options = {
			chart: {
				renderTo: 'ttrides-chart-container'
			},
			plotOptions: {
				column: {
					pointWidth: 15
				}
			},
			title: {
				text: 'Time Trial Performance - <?php echo "$year"; ?>'
			},
			xAxis: {
				type: 'datetime',
				dateTimeLabelFormats: {day: '%d %b'}
			},
			yAxis: {
				title: {
					text: 'Score'
				}
			},
			tooltip: {
				backgroundColor: '#222845',
				shared: false,
				useHTML: true,
				style: {
					color: '#cccccc',
					textAlign: 'center'
				},
				xDateFormat: '%d %B',
				formatter: function() {
					// this = point
					return this.series.tooltipOptions.customTooltip.call(this);
				}
			},
			series: [],
			responsive: {
	        rules: [{
	            condition: {
	                maxWidth: 500
	            },
	            chartOptions: {
	                plotOptions: {
	                    column: {
	                        pointWidth: 10,
	                    }
	                }
	            }
	        }]
	    }
		};
		<?php if(count($qualifiedScoreData) > 0) : ?>
			options.series.push({
				type: 'spline',
				name: 'Qualified',
				data: <?php echo json_encode($qualifiedScoreData); ?>,
				color: '#998345',
				gapSize: 1.5,
				tooltip: {
					customTooltip: function() {
						return '<table class="tt-chart-table"><thead><tr><th>' + Highcharts.dateFormat('%d %B', this.key) + '</th></tr></thead><tbody><tr><td><b>' + this.y + '</b> points</td></tr></tbody></table>'
					}
				},
				zIndex: 4
			});
		<?php endif; ?>
		<?php if(count($provisionalScoreData) > 0) : ?>
			options.series.push({
				type: 'spline',
				name: 'Provisional',
				data: <?php echo json_encode($provisionalScoreData); ?>,
				color: '#cccccc',
				gapSize: 1.5,
				tooltip: {
					customTooltip: function() {
						return '<table class="tt-chart-table"><thead><tr><th>' + Highcharts.dateFormat('%d %B', this.key) + '</th></tr></thead><tbody><tr><td><b>' + this.y + '</b> points</td></tr></tbody></table>'
					}
				},
				zIndex: 3
			});
		<?php endif; ?>
		<?php if(count($countingRideData) > 0) : ?>
			options.series.push({
				type: 'column',
				name: 'Counting Rides',
				data: <?php echo json_encode($countingRideData); ?>,
				color: '#222845',
				tooltip: {
					customTooltip: function() {
						return '<table class="tt-chart-table"><thead><tr><th>' + this.key + '</th></tr></thead><tbody><tr><td>' + this.point.distance + '</td></tr><tr><td><b>' + this.y + '</b> points</td></tr></tbody></table>'
					}
				},
				zIndex: 2
			});
			options.series.push({
				type: 'column',
				name: 'Discounted Rides',
				data: <?php echo json_encode($discountedRideData); ?>,
				color: '#ccc',
				tooltip: {
					customTooltip: function() {
						return '<table class="tt-chart-table"><thead><tr><th>' + this.key + '</th></tr></thead><tbody><tr><td>' + this.point.distance + '</td></tr><tr><td><b>' + this.y + '</b> points</td></tr></tbody></table>'
					}
				},
				zIndex: 1
			});
		<?php else: ?>
			options.series.push({
				type: 'column',
				name: 'Rides',
				data: <?php echo json_encode($discountedRideData); ?>,
				color: '#aaa',
				tooltip: {
					customTooltip: function() {
						return '<table class="tt-chart-table"><thead><tr><th>' + this.key + '</th></tr></thead><tbody><tr><td>' + this.point.distance + '</td></tr><tr><td><b>' + this.y + '</b> points</td></tr></tbody></table>'
					}
				},
				zIndex: 1
			});
		<?php endif; ?>
		var chartTt = new Highcharts.Chart(options);
	});
</script>
