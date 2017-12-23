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
defined('_JEXEC') or die ;

use Joomla\String\StringHelper;

$original		= $this->translation['original'];
$translation	= $this->translation[$this->language];
$search			= $this->state->get('filter_search'); 

?>

<div class="secretary-main-container">
<form action="<?php echo JRoute::_('index.php?option=com_secretary&view=language'); ?>" 
method="post" enctype="multipart/form-data" name="adminForm" id="adminForm" class="form-validate">
<div class="secretary-main-area">

	<div class="fullwidth">
		<div class="pull-left">
			<h2 class="documents-title">
			<span class="documents-title-first"><?php echo JText::_('COM_SECRETARY_TRANSLATIONS')?></span>
			</h2>
		</div>
		<div class="pull-right">
			<div class="secretary-search btn-group">
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->state->filter_search;?>" class="text_area search-query" onchange="document.adminForm.submit();" />
			<button onclick="this.form.submit();" class="btn"><i class="fa fa-search"></i></button>
			<button onclick="document.getElementById('filter_search').value='';this.form.submit();" class="btn"><?php echo JText::_( 'COM_SECRETARY_RESET' ); ?></button>
			</div>
		</div>
	</div>
        
	<hr class="fullwidth" />

	<div class="fullwidth clearfix margin-bottom">
		<div class="pull-left">
			<button class="btn btn-default btn-newentry" type="submit" name="task" value="language.save"><i class="fa fa-save"></i> <?php echo JText::_('COM_SECRETARY_TOOLBAR_APPLY')?></button> 
			<button class="btn btn-default" type="submit" name="task" value="language.share"><i class="fa fa-heart"></i> <?php echo JText::_('COM_SECRETARY_TRANSLATIONS_SHARE')?></button> 
		</div> 
        <div class="pull-right select-arrow-control margin-right">
        	<span class="select-label"><?php echo JText::_('COM_SECRETARY_TRANSLATIONS_SELECT_LANGUAGE')?></span>
			<div class="select-arrow select-arrow-white select-small">
			<?php echo $this->lists['filter_language']; ?>
			</div>
        </div>
	</div> 
	
	<table class="table-hover" id="secretary-language-table">
		<tr>
			<th class="left" style="width:5%;"></th>
			<th class="left" style="width:45%;"><?php echo JText::_('COM_SECRETARY_TRANSLATIONS_KEY')?> / <?php echo JText::_('COM_SECRETARY_TRANSLATIONS_ORIGINAL')?></th>
			<th class="left" style="width:50%;"><?php echo JText::_('COM_SECRETARY_TRANSLATION')?></th>
		</tr>		
		<?php
		$x = 1;
		foreach ($original as $key => $value)
		{ 
			 
			$show = true;
			if (isset($translation[$key]))
			{
				$translatedValue = $translation[$key];
				$missing         = false;
			}
			else
			{
				$translatedValue = $value;
				$missing         = true;
			}
			$translatedValue = $this->escape($translatedValue);

			if ((strpos($key, 'COM_SECRETARY_') === false) || ($search && strpos(StringHelper::strtolower($key), $search) === false && strpos(StringHelper::strtolower($value), $search) === false))
			{
				$show = false;
			} 
			
			if($show) {
			?>
			<tr>
				<td class="left"><span class="key"><?php echo $x ?></span></td>
				<td class="left"><span class="key"><?php echo $key; ?></span><?php echo $value; ?></td>
				<td>
					<textarea class="fullwidth"  name="jform[values][<?php echo $key; ?>]" size="100" value="" ><?php echo $translatedValue; ?></textarea>
					<?php if ($missing)	{ ?><span style="color:red;">*</span><?php } ?>
				</td>
			</tr>
			<?php
				$x++;
			} else {
				echo '<input type="hidden" name="jform[values]['.$key.']" size="100" value="'.$translatedValue.'" />';
			} 
		}
	?>
	</table>
	<?php echo JHtml::_('form.token'); ?>
</div>
</form>
</div>