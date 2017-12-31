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

<div class="form-group">
    <div class="secretary-template-format">
    	<select name="jform[format]" id="secretary_format" />
        <?php
        foreach($this->item->dim->formate as $format) { 
            $class = ($format['value'] == $this->item->format) ? ' selected' : '';
            echo '<option '.$class.' value="'.$format['value'].'">'.$format['title'].'</option>'; 
        }
        ?>
        </select>
    </div>
    <div class="secretary-template-margins secretary-control-group">
            	
        <h4 class="secretary-tooltip"><?php echo JText::_('COM_SECRETARY_MARGINS'); ?>&nbsp;<i class="fa fa-question-circle"></i>
          <span class="tooltip-toggle"><img src="<?php echo SECRETARY_MEDIA_PATH; ?>/images/margins.png" /></span>
        </h4>

    	<div class="secretary-input-prepend">
            <div class="secretary-add-on"><?php echo JText::_('COM_SECRETARY_LEFT'); ?></div>
    		<input name="jform[margins][0]" type="number" min="0" step="1" value="<?php echo $this->item->margins[0]?>" />
            <div class="secretary-add-on">mm</div>
        </div>
    	<div class="secretary-input-prepend">
            <div class="secretary-add-on"><?php echo JText::_('COM_SECRETARY_RIGHT'); ?></div>
    		<input name="jform[margins][1]" type="number" min="0" step="1" value="<?php echo $this->item->margins[1]?>" />
            <div class="secretary-add-on">mm</div>
        </div>
    	<div class="secretary-input-prepend">
            <div class="secretary-add-on"><?php echo JText::_('COM_SECRETARY_TOP'); ?></div>
    		<input name="jform[margins][2]" type="number" min="0" step="1" value="<?php echo $this->item->margins[2]?>" />
            <div class="secretary-add-on">mm</div>
        </div>
    	<div class="secretary-input-prepend">
            <div class="secretary-add-on"><?php echo JText::_('COM_SECRETARY_BOTTOM'); ?></div>
    		<input name="jform[margins][3]" type="number" min="0" step="1" value="<?php echo $this->item->margins[3]?>" />
            <div class="secretary-add-on">mm</div>
        </div>
    </div>
    <div class="secretary-designer-hr"></div>
</div>


<div id="secretary-template-designer-options" style="display:none;">

    <h4><?php echo JText::_('COM_SECRETARY_RESOLUTION'); ?></h4>
    
    <label>dpi</label>
    <div class="btn-group secretary-template-dpi">
        <?php
        foreach($this->item->dim->dpis as $dpi) { 
            $class = ($dpi == $this->item->dpi) ? ' active' : '';
            echo '<div class="btn'.$class.'">'.$dpi.'</div>'; 
        }
        ?>
    </div>
	<div class="secretary-designer-hr"></div>
    <div class="add-box btn btn-newentry btn-default" data-type="textarea"><?php echo JText::_('COM_SECRETARY_TEMPLATE_DESIGNER_ADD_BOX'); ?></div>
	<div class="secretary-designer-hr"></div>
	
<div class="secretary-designer-fields-desc"><?php echo JText::_('COM_SECRETARY_TEMPLATE_DESIGNER_MOVE_FIELDS'); ?></div>
</div>
    
<div class="form-group">
    <h4><?php echo JText::_('COM_SECRETARY_FIELDS'); ?></h4>
    <div class="nav">
        <div class="secretary-drag-field"><div class="box-text">{logo}</div></div>
        <div class="secretary-drag-field"><div class="box-text">{business-title}</div></div>
        <div class="secretary-drag-field"><div class="box-text">{address}</div></div>
        <div class="secretary-drag-field"><div class="box-text">{slogan}</div></div>
        <div class="secretary-drag-field"><div class="box-text">{taxvalue}</div></div> 
    </div>
    <h4><?php echo JText::_('COM_SECRETARY_USER_CONTACT'); ?></h4>
    <div class="nav">
        <div><div class="secretary-desc"><?php echo JText::_('COM_SECRETARY_USER_CONTACT_DESC') ?></div></div>
        <div class="secretary-drag-field"><div class="box-text">{user-name}</div></div>
        <div class="secretary-drag-field"><div class="box-text">{createdby-gender}</div></div>
        <div class="secretary-drag-field"><div class="box-text">{createdby-firstname}</div></div>
        <div class="secretary-drag-field"><div class="box-text">{createdby-lastname}</div></div>
        <div class="secretary-drag-field"><div class="box-text">{createdby-street}</div></div>
        <div class="secretary-drag-field"><div class="box-text">{createdby-zip}</div></div>
        <div class="secretary-drag-field"><div class="box-text">{createdby-location}</div></div>
        <div class="secretary-drag-field"><div class="box-text">{createdby-country}</div></div>
        <div class="secretary-drag-field"><div class="box-text">{createdby-phone}</div></div>
        <div class="secretary-drag-field"><div class="box-text">{createdby-email}</div></div>
        <div class="secretary-drag-field"><div class="box-text">{createdby-number}</div></div> 
        <div class="secretary-drag-field"><div class="box-text">{createdby-upload}</div></div>
		<div class="secretary-drag-field"><div class="box-text">{createdby-category-title}</div></div>
    </div>
    <div class="nav">
    <?php
        if(!empty($this->item->fields)) { 
            if($templateFields = json_decode($this->item->fields))
            {
                foreach($templateFields AS $rowCount => $value)
                { 
                    echo '<div class="secretary-drag-field"><div class="box-text">{field-'. Secretary\Route::safeURL($value[1]).'}</div></div>';
                }	
            }
        } 
        ?>
    <h4><?php echo JText::_('COM_SECRETARY_FIELDS').' - '. JText::_('COM_SECRETARY_BUSINESS').' & '. JText::_('COM_SECRETARY_CATEGORIES');?></h4>
    <?php
        if(!empty($this->item->extrafields)) { 
            if($extraFields = json_decode($this->item->extrafields))
            {
                foreach($extraFields AS $value)
                {
                    echo '<div class="secretary-drag-field"><div class="box-text">{field-'.Secretary\Route::safeURL($value[1]).'}</div></div>';
                }	
            }
        } 
        ?>
    </div>
</div>

<?php if($this->item->extension === 'documents' || $this->item->extension === 'subjects') {?>
<div class="form-group">
    <h4><?php echo JText::_('COM_SECRETARY_SUBJECT'); ?></h4>
    <div class="nav">
        <div class="secretary-drag-field"><div class="box-text">{contact-gender}</div></div>
        <div class="secretary-drag-field"><div class="box-text">{contact-firstname}</div></div>
        <div class="secretary-drag-field"><div class="box-text">{contact-lastname}</div></div>
        <div class="secretary-drag-field"><div class="box-text">{contact-street}</div></div>
        <div class="secretary-drag-field"><div class="box-text">{contact-zip}</div></div>
        <div class="secretary-drag-field"><div class="box-text">{contact-location}</div></div>
        <div class="secretary-drag-field"><div class="box-text">{contact-country}</div></div>
        <div class="secretary-drag-field"><div class="box-text">{contact-phone}</div></div>
        <div class="secretary-drag-field"><div class="box-text">{contact-email}</div></div>
        <div class="secretary-drag-field"><div class="box-text">{contact-number}</div></div> 
        <div class="secretary-drag-field"><div class="box-text">{contact-upload}</div></div>
        <div class="secretary-drag-field"><div class="box-text">{contact-connection}</div></div>
		<div class="secretary-drag-field"><div class="box-text">{contact-category-title}</div></div>
    </div>
</div>
<?php } elseif($this->item->extension === 'products') {?>
<div class="form-group">
    <h4><?php echo JText::_('COM_SECRETARY_PRODUCT'); ?></h4>
    <div class="nav">
        <div class="secretary-drag-field"><div class="box-text">{product-nr}</div></div>
        <div class="secretary-drag-field"><div class="box-text">{product-title}</div></div>
        <div class="secretary-drag-field"><div class="box-text">{product-description}</div></div>
        <div class="secretary-drag-field"><div class="box-text">{product-upload}</div></div>
        <div class="secretary-drag-field"><div class="box-text">{product-entity}</div></div>
        <div class="secretary-drag-field"><div class="box-text">{product-price-input}</div></div>
        <div class="secretary-drag-field"><div class="box-text">{product-price-output}</div></div>
        <div class="secretary-drag-field"><div class="box-text">{product-quantity-input}</div></div>
        <div class="secretary-drag-field"><div class="box-text">{product-quantity-output}</div></div>
        <div class="secretary-drag-field"><div class="box-text">{product-total-input}</div></div>
        <div class="secretary-drag-field"><div class="box-text">{product-total-output}</div></div>
    </div>
</div>
<?php } ?>

<?php if($this->item->extension === 'documents') { ?>
    <div class="form-group">
        <h4><?php echo JText::_('COM_SECRETARY_DOCUMENT'); ?></h4>
        <div class="nav">
            <div class="secretary-drag-field"><div class="box-text">{document-title}</div></div>
            <div class="secretary-drag-field"><div class="box-text">{title}</div></div>
            <div class="secretary-drag-field"><div class="box-text">{created}</div></div>
            <div class="secretary-drag-field"><div class="box-text">{deadline}</div></div>
            <div class="secretary-drag-field"><div class="box-text">{nr}</div></div>
            <div class="secretary-drag-field"><div class="box-text">{note}</div></div>
            <div class="secretary-drag-field"><div class="box-text">{subtotal}</div></div>
            <div class="secretary-drag-field"><div class="box-text">{total}</div></div>
            <div class="secretary-drag-field"><div class="box-text">{currency}</div></div>
            <div class="secretary-drag-field"><div class="box-text">{discount}</div></div>
            <div class="secretary-drag-field"><div class="box-text">{taxtotal_start}</div></div>
            <div class="secretary-drag-field"><div class="box-text">{taxtotal_percent}</div></div>
            <div class="secretary-drag-field"><div class="box-text">{taxtotal_value}</div></div>
            <div class="secretary-drag-field"><div class="box-text">{taxtotal_end}</div></div>
        </div>
    </div>
    <div class="form-group">
        <h4 class="secretary-drag-fields-items"><?php echo JText::_('COM_SECRETARY_DOCUMENT') .' - '. JText::_('COM_SECRETARY_LIST'); ?></h4>
        <div class="nav">
            <div class="secretary-drag-field"><div class="box-text">{item_start}</div></div>
            <div class="secretary-drag-field"><div class="box-text">{item_nr}</div></div>
            <div class="secretary-drag-field"><div class="box-text">{item_quantity}</div></div>
            <div class="secretary-drag-field"><div class="box-text">{item_entity}</div></div>
            <div class="secretary-drag-field"><div class="box-text">{item_title}</div></div>
            <div class="secretary-drag-field"><div class="box-text">{item_desc}</div></div>
            <div class="secretary-drag-field"><div class="box-text">{item_price}</div></div>
            <div class="secretary-drag-field"><div class="box-text">{item_taxrate}</div></div>
            <div class="secretary-drag-field"><div class="box-text">{item_taxamount}</div></div>
            <div class="secretary-drag-field"><div class="box-text">{item_total}</div></div>
            <div class="secretary-drag-field"><div class="box-text">{item_end}</div></div>
            <br>
            <div class="secretary-drag-field"><div class="box-text">{item_doc_start}</div></div>
            <div class="secretary-drag-field"><div class="box-text">{item_doc_title}</div></div>
            <div class="secretary-drag-field"><div class="box-text">{item_doc_created}</div></div>
            <div class="secretary-drag-field"><div class="box-text">{item_doc_deadline}</div></div>
            <div class="secretary-drag-field"><div class="box-text">{item_doc_subtotal}</div></div>
            <div class="secretary-drag-field"><div class="box-text">{item_doc_tax}</div></div>
            <div class="secretary-drag-field"><div class="box-text">{item_doc_total}</div></div>
            <div class="secretary-drag-field"><div class="box-text">{item_doc_end}</div></div>
        </div>
    </div>
<?php } ?>

<?php if( $this->extension == 'newsletters') { ?>

<div class="form-group">
    <div class="nav">
        <div class="secretary-drag-field"><div class="box-text">{unsubscribe text=[Unsubscribe our newsletter]}</div></div>
    </div>
</div>

<?php } ?>
   
<div class="form-group">
    <div class="nav">
        <div class="secretary-drag-field"><div class="box-text">{PAGENO}</div></div>
    </div>
</div>


