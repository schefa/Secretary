<?php
/**
 * @version     3.2.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * 
 */
 
// No direct access
defined('_JEXEC') or die;
 
$user		= \Secretary\Joomla::getUser(); 
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

$category = (!empty($this->categoryId)) ? '&catid='.$this->categoryId :  '&catid=0';

?>

<div class="secretary-main-container">

	<?php echo \Secretary\Helpers\Access::getAccessMissingMsg($this->view); ?>

	<form action="<?php echo JRoute::_('index.php?option=com_secretary&view=messages'.$category); ?>" method="post" name="adminForm" id="adminForm">
	<div class="secretary-main-area">
	
	<?php /* ?>
	 	<a class="hasTooltip open-modal" 
	 	data-url="<?php echo Secretary\Route::create('message', array('layout'=>'edit','tmpl'=>'component')); ?>" 
	 	data-original-title="<?php echo JText::_('COM_SECRETARY_MESSAGE'); ?>"><?php echo JText::_('COM_SECRETARY_MESSAGE'); ?></a> 
	<?php */ ?>
                           
	
		<div class="fullwidth clearfix">
			<div class="pull-left">
        		<div class="secretary-title"> 
                    <span class="select-arrow select-arrow-white">
                        <select id="subjects_category">
                            <?php echo JHtml::_('select.options', $this->folders, 'id', 'title', $this->categoryId, true);?>
                        </select> 
                    </span>
                    <?php if(true === $user->authorise('core.show','com_secretary.folder')) { ?>
                    <a class="secretary-title-second" href="<?php echo JRoute::_('index.php?option=com_secretary&view=folders&extension=messages'); ?>"><?php echo JText::_('COM_SECRETARY_CATEGORIES'); ?></a>
                    <?php } ?>
                    <?php if(true === $user->authorise('core.show','com_secretary.template')) { ?>
                    <a class="secretary-title-second" href="<?php echo JRoute::_('index.php?option=com_secretary&view=templates&extension=messages'); ?>"><?php echo JText::_('COM_SECRETARY_MESSAGE_CONTACT_FORM'); ?></a>
                    <?php } ?>
                </div>
			</div>
        </div>
        
        <hr />
        
		<div class="fullwidth clearfix">
			<?php $this->addToolbar(); ?>
		</div>
	</div>

	<div class="secretary-main-area"> 
        
        <h3 class="title"><?php echo JText::_('COM_SECRETARY_TALKS'); ?></h3>
        
		<?php if (empty($this->items)) : ?>
        
			<div class="alert alert-no-items">
				<?php echo JText::_('COM_SECRETARY_NO_MATCHING_RESULTS'); ?>
			</div>
            
		<?php else : ?>
        
    		<?php echo $this->loadTemplate('talks'); ?>
        
		<?php endif; ?>
	</div>

	<div class="secretary-main-area"> 
    	<?php echo $this->loadTemplate('correspondence'); ?>
	</div>
        
	<?php echo $this->loadTemplate('batch'); ?>

    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
    <?php if( !empty($this->contactID) ) { ?>
    <input type="hidden" name="contact_to" value="<?php echo $this->contactID; ?>" />
    <?php } ?>
    <?php echo JHtml::_('form.token'); ?>
    
	</form>

</div>
<?php echo Secretary\HTML::modal(); ?>