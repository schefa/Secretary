<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die; 
?>

<div class="control-group">
    <div class="posts multiple-input-selection clearfix" data-source="subjects" data-counter="<?php echo $this->contactsCounts; ?>">
        <div>
        <input class="search-features uk-form-blank" type="text" placeholder="<?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_SEARCH'); ?>" >
        </div>
    </div>
</div>