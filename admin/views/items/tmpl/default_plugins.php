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

$user = Secretary\Joomla::getUser();

$features = array( 
	'documents' => array( true, JText::_('COM_SECRETARY_DOCUMENTS') ),
	'subjects' => array( true, JText::_('COM_SECRETARY_SUBJECTS') ),
	'products' => array( true, JText::_('COM_SECRETARY_PRODUCTS') ),
	'messages' => array( true, JText::_('COM_SECRETARY_MESSAGES') ),
	'times' => array( true, JText::_('COM_SECRETARY_TIMES') ),
	'accounting' => array( true, JText::_('COM_SECRETARY_ACCOUNTING') ),
	);
	
?>


<table class="table table-hover" id="documentsList">
    <thead>
        <tr>
            
            <th width="1%" class="nowrap center hidden-phone">
            <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.extension_id', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
            </th>
            <th class='left'>
            <?php echo JHtml::_('grid.sort',  'COM_SECRETARY_NAME', 'a.name', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
            </th>
            <th class='left'>
            <?php echo JHtml::_('grid.sort',  'COM_SECRETARY_VERSION', 'a.version', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
            </th>
            <th class='left'>
            <?php echo JHtml::_('grid.sort',  'COM_SECRETARY_AUTHOR', 'a.author', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
            </th>
            <th class='left'>
            <?php echo JHtml::_('grid.sort',  'COM_SECRETARY_STATUS', 'a.enabled', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
            </th>
            
        </tr>
    </thead>
    <tbody>
    	<?php foreach ($this->items as $i => $item) : ?>
		
        <?php $plugin = $this->checkPlugin($item->name); ?>
        <?php $id = (!empty($plugin->extension_id)) ? $plugin->extension_id : 0; ?>
        <tr class="row<?php echo $i % 2; ?>">
            
            <td class="center hidden-phone">
				<?php echo (int) $id; ?>
            </td>
            
            <td>
            
                <a class="hasTooltip" data-original-title="<?php echo JText::_('COM_SECRETARY_CLICK_TO_EDIT'); ?>"  href="<?php echo JRoute::_('index.php?option=com_plugins&task=plugin.edit&extension_id='.(int) $id); ?>">
                <?php echo $item->name; ?>
                </a>
                    
            </td>
            
            <td class="left"><?php echo $item->version; ?></td>
            <td class="left"><?php echo $item->author; ?></td>
            <td class="left">
				<?php if(empty($id)) { ?>
                	<span class="btn btn-danger"><?php echo JText::_('COM_SECRETARY_NOT_INSTALLED'); ?></span>
                <?php } else { ?>
                	<?php echo JHtml::_('jgrid.published', $plugin->enabled, $i, 'plugins.'); ?>
                <?php } ?>
            </td>
            
        </tr>
        <?php endforeach; ?>
    </tbody>
    
</table>


<hr />

<h3 class="documents-title"><?php echo JText::_('COM_SECRETARY_FEATURES'); ?><?php echo '<a class="btn btn-install-features" href="'. JRoute::_("index.php?option=com_secretary&task=item.add&extension=plugins") .'">'. JText::sprintf('COM_SECRETARY_INSTALL_THIS', JText::_('COM_SECRETARY_FEATURES')) .'</a>';
?></h3>

<ul class="secretary-features-list">
<?php foreach ($features as $i => $item) : ?>
    <li class="<?php if($item[0] === true) { echo 'yes'; $icon = '<i class="fa fa-check-circle-o"></i>'; } else { echo 'no'; $icon = '<i class="fa fa-times-circle-o"></i>'; } ?>">
    	
		<?php echo $icon; ?>
    	<?php echo $item[1]; ?>
    </li>
<?php endforeach; ?>
</ul>   
  