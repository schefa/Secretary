<?php

defined('_JEXEC') or die;

//JHtml::_('bootstrap.tooltip');

$user = \Secretary\Joomla::getUser();
?>

<ul class="nav nav-tabs fullwidth margin-bottom" id="myTab" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" href="#settings_general" role="tab" data-bs-toggle="tab"><?php echo \Joomla\CMS\Language\Text::_('JDETAILS', true); ?></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#settings_areas" role="tab" data-bs-toggle="tab"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_SECTIONS', true); ?></a>
    </li>
    <?php if ($user->authorise('core.admin', 'com_secretary'))
    :
        ?>
    <li class="nav-item">
        <a class="nav-link" href="#settings_access" role="tab" data-bs-toggle="tab"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_PERMISSIONS', true); ?></a>
    </li>
    <?php endif; ?>
</ul>

<div class="tab-content">

    <div class="tab-pane active" id="settings_general">

    	<table class="table">
        	<tbody>
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
                	$options[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option','0',\Joomla\CMS\Language\Text::_('COM_SECRETARY_SELECT_OPTION'));
                	$options[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option','mpdf','mPDF');
                	$options[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option','dompdf','Dompdf');

                	$standardPDF = (isset($this->item->params['pdf'])) ? $this->item->params['pdf'] : 0;

                	$item = '<select name="jform[pdf]" id="pdf_select" class="form-control fullwidth">';
                	$item .= \Joomla\CMS\HTML\HTMLHelper::_('select.options', $options, 'value', 'text', $standardPDF);
                	$item .= '</select>';
                	echo $item;
                	?>
                	</td>
                	<td>
                	<div class="secretary-desc">
                	<span id="mpdf" style="display:<?php if (in_array($standardPDF,['mpdf']))
                	{
                        echo 'block'; }
                                                   else
                                                   {
                                                       echo 'none';}?>">
                		<?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_PDF_MPDF_DOWNLOADINFO') ?>
                	</span>
                	<span id="dompdf" style="display:<?php if (in_array($standardPDF,['dompdf']))
                	{
                        echo 'block'; }
                                                     else
                                                     {
                                                         echo 'none';}?>">
                		<?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_PDF_DOMPDF_DOWNLOADINFO') ?>
                	</span>
                	</div>
                	</td>
                </tr>
            	<tr>
                	<td><?php echo $this->form->getLabel('entitySelect'); ?></td>
                	<td><?php echo $this->form->getInput('entitySelect'); ?></td>
                	<td><div class="secretary-desc"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_SETTINGS_BUSINESS_ENTITYSELECT_DESC');?></div></td>
                </tr>
            	<tr>
                	<td><?php echo $this->form->getLabel('numberformat'); ?></td>
                	<td><?php echo $this->form->getInput('numberformat'); ?><br><br><?php echo $this->form->getInput('currencyformat'); ?></td>
                	<td><div class="secretary-desc"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_NUMBER_FORMAT_DESC');?></div></td>
                </tr>
            	<tr>
                	<td><?php echo $this->form->getLabel('accessMissingNote'); ?></td>
                	<td><?php echo $this->form->getInput('accessMissingNote'); ?></td>
                	<td><div class="secretary-desc"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_ACCESS_LIMITED_ACCESS');?></div></td>
                </tr>
            	<tr>
                	<td><?php echo $this->form->getLabel('cache'); ?></td>
                	<td><?php echo $this->form->getInput('cache'); ?></td>
                	<td><div class="secretary-desc"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_CACHE_DESC');?></div></td>
                </tr>
                
            	<tr>
                	<td colspan="3">
                    <h3><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_SETTINGS_GOOGLEMAPS');?></h3>
                    </td>
                </tr>
            	<tr>
                	<td><?php echo $this->form->getLabel('gMapsAPI'); ?></td>
                	<td><?php echo $this->form->getInput('gMapsAPI'); ?></td>
                	<td><div class="secretary-desc"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_SETTINGS_GOOGLEMAPS_API_KEY_DESC')?></div></td>
                </tr>
            	<tr>
                	<td><?php echo $this->form->getLabel('gMapsContacts'); ?></td>
                	<td><?php echo $this->form->getInput('gMapsContacts'); ?></td>
                	<td><div class="secretary-desc"><?php echo \Joomla\CMS\Language\Text::sprintf('COM_SECRETARY_SETTINGS_GOOGLEMAPS_DESC',\Joomla\CMS\Language\Text::_('COM_SECRETARY_SUBJECTS'));?></div></td>
                </tr>
            	<tr>
                	<td><?php echo $this->form->getLabel('gMapsLocations'); ?></td>
                	<td><?php echo $this->form->getInput('gMapsLocations'); ?></td>
                	<td><div class="secretary-desc"><?php echo \Joomla\CMS\Language\Text::sprintf('COM_SECRETARY_SETTINGS_GOOGLEMAPS_DESC',\Joomla\CMS\Language\Text::_('COM_SECRETARY_LOCATIONS'));?></div></td>
                </tr>
            	<tr>
                	<td colspan="3">
                    <h3><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_ACTIVITY');?></h3>
                    <div class="secretary-desc"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_SETTINGS_ACTIVITY_DESC');?></div>
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
                	<td colspan="3"><h3><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_UPLOADS');?></h3></td>
                </tr>
            	<tr>
                	<td><?php echo $this->form->getLabel('documentExt'); ?></td>
                	<td><?php echo $this->form->getInput('documentExt'); ?></td>
                	<td><div class="secretary-desc"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_SETTINGS_DOCUMENT_ENDUNG_DESC');?></div></td>
                </tr>
            	<tr>
                	<td><?php echo $this->form->getLabel('documentSize'); ?></td>
                	<td><?php echo $this->form->getInput('documentSize'); ?></td>
                	<td><div class="secretary-desc"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_SETTINGS_DOCUMENT_SIZE_DESC');?></div></td>
                </tr>
            </tbody>
        </table>
        
    </div>
    
    <div class="tab-pane" id="settings_areas">
    	<table class="table table-noborder">
			<tbody>
            	<tr class="noborder">
                	<td colspan="3"><h3><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_COLUMNS_ADAPT');?></h3></td>
                </tr>
            	<tr>
                	<td><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_PRODUCTS'); ?></td>
                	<td colspan="2">  	
            		<div class="chk_items_container">
                        <div class="fullwidth">
                        <?php 
                        $acceptedProductsCols = (array) json_decode($this->item->params['products_columns']);
                        
                        foreach (\Secretary\Helpers\Products::$selectedColumns as $key => $value)
                        {
                            $str = '<div class="chk_item"><input id="chk_'.ucfirst($key).'" type="checkbox" name="jform[products_columns][]" value="'.$key .'" ';
                            
                            if (in_array($key,$acceptedProductsCols))
                            {
                            	$str .= " checked";
                            }
                            $str .= ' /><label for="chk_'. ucfirst($key) . '">'. \Joomla\CMS\Language\Text::_('COM_SECRETARY_PRODUCT_'.ucfirst($key)).'</label></div>';
                            echo $str;
                        }
                        ?></div>
                    </div>
                	</td>
                </tr>
            	<tr>
                	<td><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_SUBJECTS'); ?></td>
                	<td colspan="2">  	
            		<div class="chk_items_container">
                        <div class="fullwidth">
                        <?php 
                        $acceptedContactsCols = (array) json_decode($this->item->params['contacts_columns']);
                        
                        foreach (\Secretary\Helpers\Subjects::$selectedColumns as $key => $value)
                        {
                            $str = '<div class="chk_item"><input id="chk_'.ucfirst($key).'" type="checkbox" name="jform[contacts_columns][]" value="'.$key .'" ';
                            
                            if (in_array($key,$acceptedContactsCols))
                            {
                            	$str .= " checked";
                            }
                            $str .= ' /><label for="chk_'. ucfirst($key) . '">'. \Joomla\CMS\Language\Text::_('COM_SECRETARY_'.ucfirst($key)).'</label></div>';
                            echo $str;
                        }
                        ?></div>
                    </div>
                	</td>
                </tr>
            	<tr>
                	<td colspan="3"><h3><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_DOCUMENTS');?></h3></td>
                </tr>
            	<tr>
                	<td><?php echo $this->form->getLabel('documents_frontend'); ?></td>
                	<td><?php echo $this->form->getInput('documents_frontend'); ?></td>
                	<td><div class="secretary-desc"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_SETTINGS_DOCUMENTS_FRONTEND_DESC');?></div></td>
                </tr>
            	<tr>
                	<td><?php echo $this->form->getLabel('filterList'); ?></td>
                	<td><?php echo $this->form->getInput('filterList'); ?></td>
                	<td><div class="secretary-desc"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_SETTINGS_BUSINESS_FILTERLIST_DESC');?></div></td>
                </tr>
            </tbody>
        </table>
    </div>

</div>
