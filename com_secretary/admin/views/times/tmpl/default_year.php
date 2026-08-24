<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>

<div class="fullwidth">
	
<?php for ( $x = 1; $x <= 12; $x++)
{
    ?>

	<?php if ( $x == 1 || $x == 4 || $x == 7 || $x == 10 )
	{
        ?>
		<div class="row-fluid">
	<?php } ?>
	
		<div class="col-md-4">
			<?php echo $this->months[$x]; ?>
		</div>
	
	<?php if ( $x == 3 || $x == 6 || $x == 9 || $x == 12 )
	{
        ?>
	</div>
	<?php } ?>
	
<?php	} ?>
	
</div>