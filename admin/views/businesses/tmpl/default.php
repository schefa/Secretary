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
?>

<div id="businesses" class="secretary-main-container">
<form action="<?php echo JRoute::_('index.php?option=com_secretary&view=businesses'); ?>" method="post" name="adminForm" id="adminForm">

	<?php echo Secretary\Helpers\Access::getAccessMissingMsg($this->view); ?>
    
	<div class="secretary-main-area">
    
        <div class="fullwidth secretary-toolbar">
            <?php $this->addToolbar(); ?>
        </div>
    
        <div class="business-list">
        <?php foreach ($this->items as $i => $item) : ?>
            <div class="business fullwidth">
            <?php if ($item->home == '0' || $item->home == '1'):?>
                <div class="business-default">
                <?php echo Secretary\HTML::_('status.isdefault', ($item->home != '0'), $i, 'businesses.', ($item->canChange && $item->home != '1'));?>
                </div>
            <?php endif; ?>
                <div class="business-text">
                <div class=" business-title">
                <?php if ($item->canEdit) : ?>
                    <a class="hasTooltip" data-original-title="<?php echo JText::_('COM_SECRETARY_CLICK_TO_EDIT'); ?>"  href="<?php echo Secretary\Route::create('business', array('layout'=>'edit', 'id'=> (int) $item->id)); ?>"><?php echo $item->title; ?></a>
                <?php else : ?>
                    <?php echo $item->title; ?>
                <?php endif; ?>
                    </div>
                <div class="business-slogan">
                    <?php echo $item->slogan; ?>
                    </div>
                </div>
                
                <div class="business-checkbox ">
                	<?php echo JHtml::_('grid.id', $i, $item->id); ?>
                	<span class="lbl"></span>
                </div>
                        
                <?php
				if($item->upload > 0 && $logoImage = Secretary\Database::getQuery('uploads', $item->upload,'id','id,business,title,folder,extension,itemID')) {
					\Secretary\Helpers\Uploads::getUploadFile($logoImage, 'business-logo', 180);
				}
				?>
            </div>
        <?php endforeach; ?>
        </div>
        
    </div>
    <?php echo $this->pagination->getListFooter(); ?>
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <?php echo JHtml::_('form.token'); ?>
</form>
</div>
