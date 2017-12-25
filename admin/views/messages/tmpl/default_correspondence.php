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

$user = JFactory::getUser(); 

$canChange	= $user->authorise('core.edit.state', 'com_secretary');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

?>

<h3 class="title"><?php echo JText::_('COM_SECRETARY_CORRESPONDENCE'); ?></h3>

<?php if (empty($this->correspondence)) : ?>

    <div class="alert alert-no-items">
        <?php echo JText::_('COM_SECRETARY_NO_MATCHING_RESULTS'); ?>
    </div>
    
<?php else : ?>

    <table class="table table-hover">
    
        <thead>
            <tr>
                
                <th></th>
                <th><?php echo JHtml::_('grid.sort', 'COM_SECRETARY_MESSAGE_BY', 'a.created_by_alias', $listDirn, $listOrder); ?></th>
                <th class="nowrap">
                    <?php echo JHtml::_('grid.sort', 'COM_SECRETARY_TITLE', 'a.subject', $listDirn, $listOrder); ?>
                </th>
                <th>
                    <?php echo JHtml::_('grid.sort', 'COM_SECRETARY_CATEGORY', 'a.subject', $listDirn, $listOrder); ?>
                </th>
                <th width="25%" class="nowrap">
                    <?php echo JHtml::_('grid.sort', 'COM_SECRETARY_CREATED', 'a.created', $listDirn, $listOrder); ?>
                </th>
            </tr>
        </thead>
        
        <tbody>
            <?php
			
			foreach ($this->correspondence as $i => $item) :
			
            $type = ''; $icon = '';
            $subject = (!empty($item->subject)) ? $item->subject : (JText::_('COM_SECRETARY_CORRESPONDENCE') . ' #'. $item->id);
            
            $canSee = false;
            if(($item->created_by == $user->id && $this->canDo->get('core.show.other')) || $item->created_by == $this->userContactId || $this->canDo->get('core.edit')) 
                $canSee = true;
            
            if(!$canSee) continue;
				
                $link = '<a href="index.php?option=com_secretary&view=message&id='. (int) $item->id.'">'. $subject .'</a>';
                $type = 'correspondence';
                $icon = '<i class="fa fa-envelope-o"></i>&nbsp;';
                
            ?>
                    
            <tr class="row<?php echo ( $i % 2 ) . (' '.$type); ?>">
            
                <td>
                <?php echo $icon; ?>
                </td>
                
                <td>
                    <?php
                        if(is_numeric($item->created_by)) {
                            $fromUser = Secretary\Database::getQuery('subjects', $item->created_by);
                            if((isset($fromUser))) { // has Secretary contact
                                $from = Secretary\Database::getJDataResult('users',(int) $fromUser->created_by,'name');
                            } else { // has only joomla user 
                                $from = Secretary\Database::getJDataResult('users',(int) $item->created_by,'name');
                            } 
                        } else {
                            $from = $item->created_by;
                        }
                        if(!empty($item->created_by_alias)) $from = '<span class="hasTooltip" title="'.$item->created_by_alias.'">'.$from.'</span>';
                        echo $from;
                    ?>
                </td>
                
                <td>
                <?php if(COM_SECRETARY_PDF) { ?>
                    <?php $href = Secretary\Route::create('message', array('id' => $item->id, 'format' => 'pdf')); ?>
                    <a class="hasTooltip printpdf modal" href="<?php echo $href; ?>" data-original-title="<?php echo JText::_('COM_SECRETARY_PDF_PREVIEW') ; ?>" rel="{size: {x: 900, y: 500}, handler:'iframe'}"><img src="<?php echo SECRETARY_MEDIA_PATH; ?>/images/pdf-20.png" /></a>
                <?php } ?>
                    <?php echo $link; ?>
                </td>
                
                <td>
                <?php echo $item->title; ?>
                <?php if(!empty( $item->contact_to_name ) ) echo " (". $item->contact_to_name.")"; ?>
                </td>
                
                <td><span class="badge"><?php echo JHtml::_('date', $item->created, JText::_('DATE_FORMAT_LC2')); ?></span></td>
                
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6">
                    <?php echo $this->pagination->getListFooter(); ?>
                </td>
            </tr>
        </tfoot>
    </table>
<?php endif; ?>

    