<?php
/**
 * @version     3.2.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * 
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