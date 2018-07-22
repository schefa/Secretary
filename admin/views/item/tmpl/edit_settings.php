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

//JHtml::_('bootstrap.tooltip');
?>

<ul id="secretary_tabs_list" class="nav nav-tabs fullwidth margin-bottom">
    <li class="active">
        <a class="btn btn-link" href="#" data-tabcontent="settings_general"><?php echo JText::_('JDETAILS', true); ?></a>
    </li>
    <li>
        <a class="btn btn-link" href="#" data-tabcontent="settings_areas"><?php echo JText::_('COM_SECRETARY_SECTIONS', true); ?></a>
    </li>
    <li>
        <a class="btn btn-link" href="#" data-tabcontent="settings_access"><?php echo JText::_('COM_SECRETARY_ACCESS_CONTROLS'); ?></a>
    </li>
</ul>
     
<div class="secretary_tabs_content"> 

    <div class="secretary_tab_pane" style="display:block;" id="settings_general">
    
    	<table class="table">
        	<tbody> 
				<?php /*
            	<tr>
                	<td><?php echo $this->form->getLabel('downloadID'); ?></td>
                	<td colspan="2"><?php echo $this->form->getInput('downloadID'); ?></td> 
                </tr>
				*/ ?>
				
            	<tr class="noborder">
                	<td colspan="3">&nbsp;</td>
                </tr>
            	<tr>
                	<td><?php echo $this->form->getLabel('templateColor'); ?></td>
                	<td><?php echo $this->form->getInput('templateColor'); ?></td>
                	<td></td>
                </tr>
            	<tr>
                	<td>PDF Library</td>
                	<td>
                	<?php 
                	$options = array();
                	$options[] = JHtml::_('select.option','0',JText::_('COM_SECRETARY_SELECT_OPTION'));
                	$options[] = JHtml::_('select.option','mpdf','mPDF');
                	$options[] = JHtml::_('select.option','mpdf7','mPDF 7');
                	$options[] = JHtml::_('select.option','dompdf','Dompdf');
                	
                	$standardPDF = (isset($this->item->params['pdf'])) ? $this->item->params['pdf'] : 0;

                	$item = '<select name="jform[pdf]" id="pdf_select" class="form-control fullwidth">';
                	$item .= JHtml::_('select.options', $options, 'value', 'text', $standardPDF);
                	$item .= '</select>';
                	echo $item;
                	?>
                	</td>
                	<td> 
                	<div class="secretary-desc">  
                	<span id="mpdf" style="display:<?php if(in_array($standardPDF,['mpdf'])) { echo 'block'; } else { echo 'none';}?>">
                		<?php echo JText::_('COM_SECRETARY_PDF_MPDF_DOWNLOADINFO') ?>
                	</span>
                	<span id="mpdf7" style="display:<?php if(in_array($standardPDF,['mpdf7'])) { echo 'block'; } else { echo 'none';}?>">
                		<?php echo JText::_('COM_SECRETARY_PDF_MPDF7_DOWNLOADINFO') ?>
                	</span>
                	<span id="dompdf" style="display:<?php if(in_array($standardPDF,['dompdf'])) { echo 'block'; } else { echo 'none';}?>">
                		<?php echo JText::_('COM_SECRETARY_PDF_DOMPDF_DOWNLOADINFO') ?>
                	</div>
                	</div>
                	</td>
                </tr>
            	<tr>
                	<td><?php echo $this->form->getLabel('entitySelect'); ?></td>
                	<td><?php echo $this->form->getInput('entitySelect'); ?></td>
                	<td><div class="secretary-desc"><?php echo JText::_('COM_SECRETARY_SETTINGS_BUSINESS_ENTITYSELECT_DESC');?></div></td>
                </tr>
            	<tr>
                	<td><?php echo $this->form->getLabel('numberformat'); ?></td>
                	<td><?php echo $this->form->getInput('numberformat'); ?><br><br><?php echo $this->form->getInput('currencyformat'); ?></td>
                	<td><div class="secretary-desc"><?php echo JText::_('COM_SECRETARY_NUMBER_FORMAT_DESC');?></div></td>
                </tr>
            	<tr>
                	<td><?php echo $this->form->getLabel('accessMissingNote'); ?></td>
                	<td><?php echo $this->form->getInput('accessMissingNote'); ?></td>
                	<td><div class="secretary-desc"><?php echo JText::_('COM_SECRETARY_ACCESS_LIMITED_ACCESS');?></div></td>
                </tr>
            	<tr>
                	<td><?php echo $this->form->getLabel('cache'); ?></td>
                	<td><?php echo $this->form->getInput('cache'); ?></td>
                	<td><div class="secretary-desc"><?php echo JText::_('COM_SECRETARY_CACHE_DESC');?></div></td>
                </tr>
                
            	<tr>
                	<td colspan="3">
                    <h3><?php echo JText::_('COM_SECRETARY_SETTINGS_GOOGLEMAPS');?></h3>
                    </td>
                </tr>
            	<tr>
                	<td><?php echo $this->form->getLabel('gMapsAPI'); ?></td>
                	<td><?php echo $this->form->getInput('gMapsAPI'); ?></td>
                	<td><div class="secretary-desc"><?php echo JText::_('COM_SECRETARY_SETTINGS_GOOGLEMAPS_API_KEY_DESC')?></div></td>
                </tr>
            	<tr>
                	<td><?php echo $this->form->getLabel('gMapsContacts'); ?></td>
                	<td><?php echo $this->form->getInput('gMapsContacts'); ?></td>
                	<td><div class="secretary-desc"><?php echo JText::sprintf('COM_SECRETARY_SETTINGS_GOOGLEMAPS_DESC',JText::_('COM_SECRETARY_SUBJECTS'));?></div></td>
                </tr>
            	<tr>
                	<td><?php echo $this->form->getLabel('gMapsLocations'); ?></td>
                	<td><?php echo $this->form->getInput('gMapsLocations'); ?></td>
                	<td><div class="secretary-desc"><?php echo JText::sprintf('COM_SECRETARY_SETTINGS_GOOGLEMAPS_DESC',JText::_('COM_SECRETARY_LOCATIONS'));?></div></td>
                </tr>
            	<tr>
                	<td colspan="3">
                    <h3><?php echo JText::_('COM_SECRETARY_ACTIVITY');?></h3>
                    <div class="secretary-desc"><?php echo JText::_('COM_SECRETARY_SETTINGS_ACTIVITY_DESC');?></div>
                    </td>
                </tr>
            	<tr>
                	<td><?php echo $this->form->getLabel('activityCreated'); ?></td>
                	<td><?php echo $this->form->getInput('activityCreated'); ?></td>
                	<td></td>
                </tr>
            	<tr>
                	<td><?php echo $this->form->getLabel('activityEdited'); ?></td>
                	<td><?php echo $this->form->getInput('activityEdited'); ?></td>
                	<td></td>
                </tr>
            	<tr>
                	<td><?php echo $this->form->getLabel('activityDeleted'); ?></td>
                	<td><?php echo $this->form->getInput('activityDeleted'); ?></td>
                	<td></td>
                </tr>
            	<tr>
                	<td colspan="3"><h3><?php echo JText::_('COM_SECRETARY_UPLOADS');?></h3></td>
                </tr>
            	<tr>
                	<td><?php echo $this->form->getLabel('documentExt'); ?></td>
                	<td><?php echo $this->form->getInput('documentExt'); ?></td>
                	<td><div class="secretary-desc"><?php echo JText::_('COM_SECRETARY_SETTINGS_DOCUMENT_ENDUNG_DESC');?></div></td>
                </tr>
            	<tr>
                	<td><?php echo $this->form->getLabel('documentSize'); ?></td>
                	<td><?php echo $this->form->getInput('documentSize'); ?></td>
                	<td><div class="secretary-desc"><?php echo JText::_('COM_SECRETARY_SETTINGS_DOCUMENT_SIZE_DESC');?></div></td>
                </tr>
            </tbody>
        </table>
        
    </div>
    
    <div class="secretary_tab_pane" style="display:none;" id="settings_areas">
    	<table class="table table-noborder">
			<tbody>
            	<tr class="noborder">
                	<td colspan="3"><h3><?php echo JText::_('COM_SECRETARY_COLUMNS_ADAPT');?></h3></td>
                </tr>
            	<tr>
                	<td><?php echo JText::_('COM_SECRETARY_PRODUCTS'); ?></td>
                	<td colspan="2">  	
            		<div class="chk_items_container">
                        <div class="fullwidth">
                        <?php 
                        $acceptedProductsCols = (array) json_decode($this->item->params['products_columns']);
                        foreach(\Secretary\Helpers\Products::$selectedColumns as $key => $value) {
                            $str = '<div class="chk_item"><input id="chk_'.ucfirst($key).'" type="checkbox" name="jform[products_columns][]" value="'.$key .'" ';
                            if(in_array($key,$acceptedProductsCols)) $str .= " checked";
                            $str .= ' /><label for="chk_'. ucfirst($key) . '">'. JText::_('COM_SECRETARY_PRODUCT_'.ucfirst($key)).'</label></div>';
                            echo $str;
                        }
                        ?></div>
                    </div>
                	</td>
                </tr>
            	<tr>
                	<td><?php echo JText::_('COM_SECRETARY_SUBJECTS'); ?></td>
                	<td colspan="2">  	
            		<div class="chk_items_container">
                        <div class="fullwidth">
                        <?php 
                        $acceptedContactsCols = (array) json_decode($this->item->params['contacts_columns']);
                        foreach(\Secretary\Helpers\Subjects::$selectedColumns as $key => $value) {
                            $str = '<div class="chk_item"><input id="chk_'.ucfirst($key).'" type="checkbox" name="jform[contacts_columns][]" value="'.$key .'" ';
                            if(in_array($key,$acceptedContactsCols)) $str .= " checked";
                            $str .= ' /><label for="chk_'. ucfirst($key) . '">'. JText::_('COM_SECRETARY_'.ucfirst($key)).'</label></div>';
                            echo $str;
                        }
                        ?></div>
                    </div>
                	</td>
                </tr>
            	<tr>
                	<td colspan="3"><h3><?php echo JText::_('COM_SECRETARY_DOCUMENTS');?></h3></td>
                </tr>
            	<tr>
                	<td><?php echo $this->form->getLabel('documents_frontend'); ?></td>
                	<td><?php echo $this->form->getInput('documents_frontend'); ?></td>
                	<td><div class="secretary-desc"><?php echo JText::_('COM_SECRETARY_SETTINGS_DOCUMENTS_FRONTEND_DESC');?></div></td>
                </tr>
            	<tr>
                	<td><?php echo $this->form->getLabel('filterList'); ?></td>
                	<td><?php echo $this->form->getInput('filterList'); ?></td>
                	<td><div class="secretary-desc"><?php echo JText::_('COM_SECRETARY_SETTINGS_BUSINESS_FILTERLIST_DESC');?></div></td>
                </tr>
            	<tr>
                	<td colspan="3"><h3><?php echo JText::_('COM_SECRETARY_MESSAGES');?></h3></td>
                </tr>
            	<tr>
                	<td><?php echo $this->form->getLabel('messages_unread'); ?></td>
                	<td><?php echo $this->form->getInput('messages_unread'); ?></td>
                	<td><div class="secretary-desc"><?php echo JText::_('COM_SECRETARY_SETTINGS_MESSAGES_UNREAD_DESC');?></div></td>
                </tr>
            	<tr>
                	<td colspan="3"><h4><?php echo JText::_('COM_SECRETARY_LIVE_CHAT');?></h4></td>
                </tr>
            	<tr>
                	<td><?php echo $this->form->getLabel('messages_chat'); ?></td>
                	<td><?php echo $this->form->getInput('messages_chat'); ?></td>
                	<td></td>
                </tr>
            	<tr>
                	<td><?php echo $this->form->getLabel('messages_waitMsg'); ?></td>
                	<td><?php echo $this->form->getInput('messages_waitMsg'); ?></td>
                	<td><div class="secretary-desc"><?php echo JText::_('COM_SECRETARY_SETTINGS_MESSAGES_WAITMSG_DESC');?></div></td>
                </tr>
            	<tr>
                	<td><?php echo $this->form->getLabel('messages_waitPing'); ?></td>
                	<td><?php echo $this->form->getInput('messages_waitPing'); ?></td>
                	<td><div class="secretary-desc"><?php echo JText::_('COM_SECRETARY_SETTINGS_MESSAGES_WAITPING_DESC');?></div></td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <?php /* ?>
    <div class="tab-pane " id="settings_access">
    
        <div class="tabbable tabs-left">
        
        	<ul class="nav nav-tabs">
    			<?php foreach($this->rulesList as $title => $rule) { ?>
            	<li class=" <?php if($title == 'component') echo 'active'; ?>"><a data-toggle="tab" href="#permission-<?php echo $title; ?>"><?php echo JText::_('COM_SECRETARY_'. strtoupper($title)); ?></a></li>
    			<?php } ?>
            </ul>
            
            <div class="tab-content">
    			<?php foreach($this->rulesList as $title => $rule) { ?>
            	<div id="permission-<?php echo $title ?>" class="tab-pane <?php if($title == 'component') echo 'active'; ?>"><?php echo $rule; ?>
                </div>
    			<?php } ?>
            </div>
            
        </div>
        
        <div class="alert"><?php echo JText::_('COM_SECRETARY_RULES_SETTING_NOTES_ITEM');?></div>
        
    </div>
    <?php */ ?>

</div>
