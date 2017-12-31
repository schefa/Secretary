<?php
/**
 * @version     3.0.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
 */
 
// No direct access
defined('_JEXEC') or die;
?>

<div class="fullwidth">
	
<?php for ( $x = 1; $x <= 12; $x++) { ?>

	<?php if( $x == 1 or $x == 4 or $x == 7 or $x == 10 ) { ?>
		<div class="row-fluid">
	<?php } ?>
	
		<div class="col-md-4">
			<?php echo $this->months[$x]; ?>
		</div>
	
	<?php if( $x == 3 or $x == 6 or $x == 9 or $x == 12 ) { ?>
	</div>
	<?php } ?>
	
<?php	} ?>
	
</div>