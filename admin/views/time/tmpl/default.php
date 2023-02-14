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
?>

<div class="secretary-main-container">

<?php echo Secretary\HTML::_('datafields.item'); ?>
<div class="secretary-main-area entry-default">

    <div class="secretary-toolbar fullwidth">
      
        <div class="pull-left margin-right">
            <div class="secretary-title">
				<?php if($this->item->extension === 'tasks') { ?>  
                <span><a href="<?php echo Secretary\Route::create('time',array('extension'=>'projects','id'=>$this->item->projectID)); ?>"><?php echo JText::_('COM_SECRETARY_PROJECT'); ?></a>&nbsp;<i class="fa fa-angle-right"></i>&nbsp;</span>
                <?php } ?>
    			<span><a href="<?php echo Secretary\Route::create('times',array('extension'=>$this->item->extension)); ?>"> <?php echo JText::_('COM_SECRETARY_'. $this->item->extension); ?></a>&nbsp;<i class="fa fa-angle-right"></i>&nbsp;</span>
           		<span><?php echo $this->title; ?></span>
                        
				<?php if($this->checkedOut == false && (\Secretary\Helpers\Access::edit('time',$this->item->id,$this->item->created_by))) { ?>
                <a class="pull-right btn btn-saveentry" href="index.php?option=com_secretary&task=time.edit&id=<?php echo $this->item->id;?>&extension=<?php echo $this->item->extension;?>"><?php echo JText::_('COM_SECRETARY_TOOLBAR_EDIT'); ?></a>
                <?php } ?>
           	</div>
        </div>
        
    </div>
    
	<?php if($this->item->id > 0 && (in_array($this->extension,array('events','projects','tasks')))) echo $this->loadTemplate('subscription'); ?>
    
    <?php echo $this->loadTemplate($this->extension); ?>

	<hr />
    
    <div class="fullwidth">
        <div class="col-md-3">
            <div class="control-label"><label><?php echo JText::_('COM_SECRETARY_BUSINESS'); ?></label></div>
            <div class="controls"><?php echo Secretary\Database::getQuery('businesses',$this->item->business,'id','title','loadResult'); ?></div>
        </div>
    </div>
    
</div>
</div>

