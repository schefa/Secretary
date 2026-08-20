<?php
 
defined('_JEXEC') or die; 
?>

<div class="control-group">
    <div class="posts multiple-input-selection clearfix" data-source="subjects" data-counter="<?php echo $this->contactsCounts; ?>">
        <div>
        <input class="search-features uk-form-blank" type="text" placeholder="<?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_SEARCH'); ?>" >
        </div>
    </div>
</div>