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

$user       = Secretary\Joomla::getUser();
$business	= Secretary\Application::company('currency,taxvalue');
$currency	= $business['currency'];
$extension	= 'accountings';

$this->datafields		= \Secretary\Helpers\Items::makeFieldsReadyForList($this->item->fields);
$fields			= $this->datafields['fields'];

if(!empty($this->item->accounting) && !is_array($this->item->accounting))
	$this->document->addScriptDeclaration('var accJSON = '.$this->item->accounting );

?>

<div class="secretary-main-container">
    
    <?php echo Secretary\HTML::_('datafields.item'); ?>
    
    <div class="secretary-acc-row clearfix" style="display:none;">
        <div class="secretary-acc-row-1"><div class="btn acc-row-remove"><i class="fa fa-remove"></i></div></div>
        <div class="secretary-acc-row-2">
            <input class="search-accounts form-control" type="text" value="##account##" placeholder="<?php echo JText::_('COM_SECRETARY_KONTO'); ?>" />
            <input name="jform[accounting][##type##][##counter##][id]" class="acc_##type##_konto" type="hidden" value="##accountid##" />
        </div>
        <div class="secretary-acc-row-3">
            <span><?php echo $currency; ?></span>
            <input name="jform[accounting][##type##][##counter##][sum]" class="form-control secretary-acc-total acc_##type##_sum" type="number" min="0" step="0.01" value="##sum##" />
        </div>
        
    </div>
    
    <form action="<?php echo JRoute::_('index.php?option=com_secretary&view=accounting&layout=edit&id='.(int)$this->item->id.'&extension='.$this->escape($this->extension)); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="adminForm" class="form-validate">
      
        
        <div class="secretary-main-area">
            
            <div class="secretary-toolbar fullwidth">
                <div class="secretary-title">
                <span><?php echo $this->title; ?>&nbsp;<i class="fa fa-angle-right"></i>&nbsp;</span>
                <?php $this->addToolbar(); ?>
                
                <div class="btn-toolbar pull-right">
                    <?php if($this->extension == 'accounting') { ?>
                    <div class="btn-group" style="width:50px;"><?php echo $this->form->getInput('year'); ?></div>
                    <?php } ?>
                </div>
                
                </div>
            </div>
            
            <fieldset>
                <?php  echo $this->loadTemplate($this->extension); ?>
            </fieldset>
            
        </div>
        
        <?php echo $this->form->getInput('id'); ?>
    
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="extension" value="<?php echo $this->extension; ?>" />
    <?php echo JHtml::_('form.token'); ?>
    </form>

    
    <script type="text/javascript">
    jQuery.noConflict();
    jQuery( document ).ready(function( $ ) {
    <?php if(isset($fields)) :?>
        var secretary_fields = [<?php echo $fields;?>];
    <?php else : ?>
        var secretary_fields = [];
    <?php endif;?>
	Secretary.Fields( secretary_fields );
    });
    </script>

</div>