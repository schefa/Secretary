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

$this->document->addScriptDeclaration("
	var featuresList = ". json_encode( $this->item->contacts ) .";
");
?>

<div class="control-group search-features-newsletter">
    <div class="posts multiple-input-selection clearfix" data-source="subjects" data-counter="<?php echo count($this->item->contacts); ?>">
        <div>
        <input class="search-features" type="text" placeholder="<?php echo JText::_('COM_SECRETARY_SEARCH'); ?>" >
        </div>
    </div>
</div>