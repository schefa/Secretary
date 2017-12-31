<?php
/**
 * @version     3.2.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
 */
 
// No direct access
defined('_JEXEC') or die; 
?>

<div class="control-group">
    <div class="posts multiple-input-selection clearfix" data-source="subjects" data-counter="<?php echo $this->contactsCounts; ?>">
        <div>
        <input class="search-features uk-form-blank" type="text" placeholder="<?php echo JText::_('COM_SECRETARY_SEARCH'); ?>" >
        </div>
    </div>
</div>