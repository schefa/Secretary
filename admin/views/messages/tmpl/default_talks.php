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
$userContact = Secretary\Database::getQuery('subjects',(int) $user->id,'created_by','id','loadResult');
$userContactId = (isset($userContact)) ? (int) $userContact : -1;

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

$enableChat = (bool) $this->params->get('messages_chat',false);
?>
<table class="table table-hover">

    <thead>
        <tr>
            <th></th>
            <th><?php echo Secretary\HTML::_('status.checkall'); ?><span class="lbl"></span></th>
            <th></th>
            <th><?php echo JHtml::_('grid.sort', 'COM_SECRETARY_MESSAGE', 'a.subject', $listDirn, $listOrder); ?> / <?php echo JHtml::_('grid.sort', 'COM_SECRETARY_STARTED_BY', 'a.created_by', $listDirn, $listOrder); ?></th>
            <th>
                <?php echo JHtml::_('grid.sort', 'COM_SECRETARY_CATEGORY', 'c.title', $listDirn, $listOrder); ?>
            </th>
            <th width="25%" class="nowrap">
                <?php echo JHtml::_('grid.sort', 'COM_SECRETARY_LAST_MESSAGE', 'a.created', $listDirn, $listOrder); ?>
            </th>
            <th><?php echo JHtml::_('grid.sort', 'COM_SECRETARY_TOTAL', 'sum', $listDirn, $listOrder); ?></th>
        </tr>
    </thead>
    
    <tbody>
        <?php
        foreach ($this->items as $i => $item) :
        
        if($item->refer_to < 1)
        	continue;

        $mitem = Secretary\Database::getQuery('messages',(int) $item->refer_to, 'id');
        if(empty($mitem)) continue;
        
        // Permission message
        $canSee = false; $canChange = false; $canEdit = false;
		
		// Own Post
        if(!empty($mitem) && ($userContactId == $mitem->created_by)) {
            $canSee = true; 
            if($user->authorise('core.edit.own', 'com_secretary.message') ||  $user->authorise('core.edit', 'com_secretary.message')){
                $canEdit = true; $canChange	= true;
            }
        } elseif($user->id == $mitem->created_by) {
            $canSee = true; $canEdit = false; $canChange = false;
		}
		
		if(!$canSee && ((int) $mitem->contact_to === $userContactId)) { $canSee = true; }
		elseif(!$canSee) { $canSee = $user->authorise('core.show','com_secretary.message.'.$mitem->id) 
		                          || $user->authorise('core.show.other','com_secretary.message'); }
		
		if( !$canSee ) continue;
				
        $type = ''; $icon = '';
        $subject = (!empty($mitem->subject)) ? Secretary\Utilities::cleaner(($mitem->subject),true) : (JText::_('COM_SECRETARY_CORRESPONDENCE') . ' #'. $mitem->id);
        
		// No contact = no Talk
        if($mitem->contact_to == 0 && !isset($mitem->contact_to_alias)) continue;
		
        if(!empty($mitem->contact_to_alias))
        {
            // Contact 2 Contact
            $catid = (!empty($item->catid)) ? ("&catid=". (int) $item->catid) : "";
            if($item->sum > 1) $mitem->id = $item->refer_to;
            $link = '<a href="index.php?option=com_secretary&view=messages&layout=talk'.$catid."&rid=".(int) $mitem->id.'">'. $subject .'</a>';
            $type = 'c2contact';
            $icon = '<i class="fa fa-comments-o"></i>&nbsp;';
            
        }
        elseif($item->catid > 0)
        {
            // Contact 2 Category
            $link = '<a href="index.php?option=com_secretary&view=messages&layout=talk&catid='. (int) $item->catid .'">'. $subject .'</a>';
            $type = 'c2category';
            $icon = '<i class="fa fa-folder-o"></i>&nbsp;';
        } else {
            
        	$link = '<a href="index.php?option=com_secretary&view=message&id='. (int) $mitem->id.'">'. $subject .'</a>';
            $type = 'correspondence';
            $icon = '<i class="fa fa-envelope-o"></i>&nbsp;';
            
        }
        
        ?>
                
        <tr class="row<?php echo ( $i % 2 ) . (' '.$type); ?>">
        
            <td><?php echo $icon; ?></td>
            
            <td><?php echo JHtml::_('grid.id', $i, $mitem->id); ?><span class="lbl"></span></td>
            
            <td>
            <ul class="messages-details-list">
            	<li><a class="hasTooltip" href="<?php echo Secretary\Route::create('messages',array('layout'=>'talk','rid'=>$mitem->id)); ?>" title="<?php echo JText::_('COM_SECRETARY_SHOW')?>"><i class="fa fa-newspaper-o"></i></a></li>
            	<?php if(true === $enableChat){ ?><li><a href="<?php echo Secretary\Route::create('messages',array('layout'=>'chat','rid'=>$mitem->id)); ?>">
            <?php echo JText::_('COM_SECRETARY_CHAT')?></a></li><?php } ?>
            </ul>
            </td>
            
            <td>
                <?php echo $link; ?> - 
                <?php
					$from = '---';
					if(is_numeric($mitem->created_by)) {
						$fromObject = Secretary\Database::getQuery('subjects',(int) $mitem->created_by,'id','firstname,lastname');
						if(isset($fromObject)) {
							$from = (isset($fromObject->lastname)) ? $fromObject->lastname : '';
							$from .=  (isset($fromObject->firstname)) ? ' '. $fromObject->firstname : '';
						}
                    } else {
                    	$from = $mitem->created_by;
                    }
                    if(!empty($mitem->created_by_alias)) $from = '<span class="hasTooltip" title="'.$mitem->created_by_alias.'">'.$from.'</span>';
                    echo $from;
                ?>
            </td>
            
            
            <td>
            <?php echo $item->title; ?>
            <?php if(!empty( $item->contact_to_name ) ) echo " (". $mitem->contact_to_name.")"; ?>
            </td>
            
            <td><span class="badge"><?php echo JHtml::_('date', $mitem->created, JText::_('DATE_FORMAT_LC2')); ?></span></td>
            
            <td><?php echo $item->sum; ?></td>
            
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="8">
                <?php echo $this->pagination->getListFooter(); ?>
            </td>
        </tr>
    </tfoot>
</table>
