<?php
/**
 * @version     3.0.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      GNU General Public License version 2 or later.
 */
 
// No direct access
defined('_JEXEC') or die;

$labels = $this->chartData['labels'];
$series = $this->chartData['series'];
?> 
<div id="secretary-chart-1" class="secretary-charts" style="width:100%;"></div>
<script>
    new Secretary.Charts( 'graph', {
         id : 'secretary-chart-1',
         labels : <?php echo json_encode($labels); ?>,
         series : <?php echo json_encode($series, JSON_NUMERIC_CHECK); ?>, 
         yScaleFromZero : false
	});
</script>

<?php /*
?>

<div class="ct-chart" style="height:300px"></div>
<script>
new Chartist.Line('.ct-chart', {
    	labels : <?php echo json_encode($labels); ?>,
       series : [<?php echo json_encode($series, JSON_NUMERIC_CHECK); ?>], 
	}, {
	  fullWidth: true,
	  chartPadding: {
	    right: 40
	  }
	});

</script>
<?php */ ?>