<?php

defined('_JEXEC') or die;

$user = \Secretary\Joomla::getUser();

$app = \Secretary\Joomla::getApplication();
$toggleTaxRateColumn = (int) $app->getUserState('filter.toggleTaxRateColumn', 1);
$taxSelection = ($toggleTaxRateColumn == 0) ? '' : ' taxSelection';
$hasSubject = (!empty($this->item->subject)) ? true : false;
?>

<div class="secretary-main-container">
    <?php echo Secretary\HTML::_('datafields.item'); ?>
    <?php echo $this->loadTemplate('item'); ?>
    <form action="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_secretary&view=document&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="adminForm" class="form-validate">
        <div class="secretary-main-area secretary-document">
            <div class="secretary-document-padding">
                <div class="secretary-toolbar fullwidth">
                    <div class="select-arrow-toolbar "><?php echo $this->form->getInput('catid'); ?></div>
                    <div class="select-arrow-toolbar-next">&#10095;</div>
                    <?php $this->addToolbar(); ?>
                </div>
                <ul class="nav nav-tabs fullwidth margin-bottom" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" href="#home" role="tab" data-bs-toggle="tab"><?php echo \Joomla\CMS\Language\Text::_('JDETAILS', true); ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#more" role="tab" data-bs-toggle="tab"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_TAB_ERWEITERT', true); ?></a>
                    </li>
                    <?php if ($user->authorise('core.admin', 'com_secretary'))
                    {
                        ?>
                        <li class="nav-item"><a class="nav-link" href="#permission" role="tab" data-bs-toggle="tab"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_PERMISSIONS', true); ?></a></li>
                    <?php } ?>
                    <?php 
                    if ($this->item->id > 0)
                    {
                        ?>
                        <?php if (COM_SECRETARY_PDF && !empty($this->defaultTemplate))
                        {
                            ?>
                            <li class="nav-item pull-right secretary-document-pdf-print">
                                <a class="nav-link btn-pdf" data-joomla-dialog='{"popupType": "iframe", "src": "<?php echo Secretary\Route::create('document', array('format' => 'pdf', 'id' => $this->item->id)); ?>"}'><img src="<?php echo SECRETARY_MEDIA_PATH; ?>/images/pdf-20.png" />&nbsp;<?php echo \Joomla\CMS\Language\Text::_('PDF'); ?></a>
                            </li>
                        <?php }  ?>
                        <?php 
                        if (\Secretary\Helpers\ERechnung::isAvailable($this->item))
                        {
                            ?>
                            <li class="nav-item pull-right secretary-document-pdf-print">
                                <a class="nav-link btn-erechnung" href="<?php echo Secretary\Route::create('document', array('format' => 'xrechnung', 'id' => $this->item->id)); ?>" target="_blank"><i class="fa fa-file-code-o"></i>&nbsp;<?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_ERECHNUNG'); ?></a>
                            </li>
                        <?php } ?>
                        <?php 
                        if (\Secretary\Helpers\Access::checkAdmin() && !empty($this->item->subject[6]))
                        {
                            ?>
                            <li class="pull-right">
                                <a class="nav-link" data-joomla-dialog='{"popupType": "iframe", "src": "<?php echo Secretary\Route::create('document', array('layout' => 'email', 'tmpl' => 'component', 'id' => $this->item->id)); ?>"}'><img src="<?php echo SECRETARY_MEDIA_PATH; ?>/images/email-25.png" />&nbsp;<?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_EMAIL'); ?></a>
                            </li>
                        <?php } ?>
                        <li class="pull-right">
                            <a class="nav-link" data-joomla-dialog='{"popupType": "iframe", "src": "<?php echo Secretary\Route::create('document', array('layout' => 'preview', 'tmpl' => 'component', 'id' => $this->item->id)); ?>"}'><img src="<?php echo SECRETARY_MEDIA_PATH; ?>/images/document_print_preview-20.png" />&nbsp;<?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_PREVIEW'); ?></a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane active form" id="home">
                    <div class="row-fluid">
                        <div class="col-lg-9">
                            <div class="secretary-document-main">
                                <div class="table-item-header fullwidth">
                                    <div class="pull-right secretary-documents-datetitle">
                                        <div class="row-fluid">
                                            <h4 class="title"><?php echo \Joomla\CMS\Language\Text::_('&nbsp;'); ?></h4>
                                            <div class="secretary-control-group row">
                                                <div class="pull-left col-md-6">
                                                    <?php echo $this->form->getLabel('created'); ?>
                                                    <div class="controls"><?php echo $this->form->getInput('created'); ?></div>
                                                </div>
                                                <div id="ajaxNumber" class="col-md-6" <?php if ($this->item->id)
                                                {
                                                                                            echo 'data-id="' . $this->item->id . '"';
                                                                                      } ?> <?php if ($this->item->catid)
                                                {
                                                                                                    echo 'data-catid="' . $this->item->catid . '"';
                                                                                      } ?>>
                                                    <?php echo $this->form->getLabel('nr'); ?>
                                                    <div class="controls">
                                                        <?php echo $this->form->getInput('nr'); ?>
                                                        <div id="ajaxNumberResult"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="secretary-control-group">
                                                <div class="control-label"><?php echo $this->form->getLabel('title'); ?></div>
                                                <div class="controls"><?php echo $this->form->getInput('title'); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="pull-left secretary-documents-contact">
                                        <?php if (!$this->multiple_subjects || count($this->jsonSubjects ?? []) <= 1)
                                        {
                                            ?>
                                            <h4 class="title"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_SUBJECT') . Secretary\HTML::_('search.contacts'); ?></h4>
                                            <div class="fullwidth secretary-control-group">
                                                <div class="secretary-control-group-gender">
                                                    <label class="control-label"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_ANREDE'); ?></label>
                                                    <?php echo $this->genderoptions; ?>
                                                </div>
                                                <div class="secretary-control-group-name ui-widget">
                                                    <label class="control-label"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_NAME'); ?></label>
                                                    <div id="display_contact_name" <?php 
                                                    if (!$hasSubject && empty($this->item->subject[1]))
                                                    {
                                                                                        echo 'style="display:none"';
                                                    } ?>><span id="contact_name"><?php echo ($hasSubject ? $this->item->subject[1] : ""); ?></span><span class="clean-contact">x</span></div>
                                                    <input id="jform_subject_name" <?php 
                                                    if ($hasSubject && !empty($this->item->subject[1]))
                                                    {
                                                                                        echo 'style="display:none"';
                                                    } ?> type="text" name="jform[subject][1]" value="<?php echo ($hasSubject ? $this->item->subject[1] : ""); ?>" placeholder="<?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_NAME'); ?>" class="hasTooltip" data-original-title="<?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_NAME'); ?>" />
                                                </div>
                                                <?php echo $this->relatedContacts; ?>
                                            </div>
                                            <div class="secretary-control-group">
                                                <div class="secretary-input-prepend clearfix">
                                                    <div class="secretary-add-on"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_STREET'); ?></div>
                                                    <input id="jform_subject_street" type="text" name="jform[subject][2]" value="<?php echo ($hasSubject ? $this->item->subject[2] : ""); ?>" placeholder="<?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_STREET'); ?>" class="hasTooltip" data-original-title="<?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_STREET'); ?>" />
                                                </div>
                                                <table border="0">
                                                    <tr>
                                                        <td width="36%">
                                                            <div class="secretary-input-prepend clearfix">
                                                                <div class="secretary-add-on"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_POSTCODE'); ?></div>
                                                                <input id="jform_subject_zip" type="text" name="jform[subject][3]" value="<?php echo ($hasSubject ? $this->item->subject[3] : ""); ?>" placeholder="<?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_POSTCODE'); ?>" class="search-subject-zip hasTooltip input-border-radius-left" data-original-title="<?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_POSTCODE'); ?>" />
                                                            </div>
                                                        </td>
                                                        <td width="4%">
                                                        </td>
                                                        <td width="60%">
                                                            <div class="secretary-input-prepend clearfix">
                                                                <div class="secretary-add-on"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_LOCATION'); ?></div>
                                                                <input id="jform_subject_location" type="text" name="jform[subject][4]" value="<?php echo ($hasSubject ? $this->item->subject[4] : ""); ?>" placeholder="<?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_LOCATION'); ?>" class="search-subject-location hasTooltip input-border-radius-left" data-original-title="<?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_LOCATION'); ?>" />
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </table>
                                                <div class="clearfix">
                                                    <div class="secretary-control-group-left">
                                                        <div class="secretary-input-prepend clearfix">
                                                            <div class="secretary-add-on"><span class="fa fa-phone"></span></div>
                                                            <input class="hasTooltip" id="jform_subject_phone" type="text" name="jform[subject][5]" value="<?php echo ($hasSubject ? $this->item->subject[5] : ""); ?>" placeholder="<?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_PHONE'); ?>" data-original-title="<?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_PHONE'); ?>">
                                                        </div>
                                                    </div>
                                                    <div class="secretary-control-group-right">
                                                        <div class="secretary-input-prepend clearfix">
                                                            <div class="secretary-add-on"><span class="fa fa-envelope-o"></span></div>
                                                            <input class="hasTooltip" id="jform_subject_email" type="text" name="jform[subject][6]" value="<?php if (isset($this->item->subject[6]))
                                                            {
                                                                echo $this->item->subject[6];
                                                                                                                                                           } ?>" placeholder="<?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_EMAIL'); ?>" data-original-title="<?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_EMAIL'); ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php echo $this->form->getInput('subjectid'); ?>
                                        <?php }
                                        else
                                        {
                                            ?>
                                            <h4 class="title"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_SUBJECTS'); ?></h4>
                                            <?php
                                            foreach ($this->jsonSubjects as $x => $id)
                                            {
                                                $contact = Secretary\Database::getQuery('subjects', $id, 'id', 'firstname,lastname', 'loadAssoc');
                                                
                                                if ($x > 0)
                                                {
                                                    echo ', ';
                                                }
                                                echo $contact['firstname'] . ' ' . $contact['lastname'];
                                            }
                                            ?>
                                            <input name="subject" type="hidden" value="<?php echo $this->subjects; ?>" />
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="secretary-documents-table-items <?php echo $taxSelection ?>">
                                    <h4 class="title"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_PRODUCTS'); ?></h4>
                                    <div class="table-item-th">
                                        <div class="row-fluid table-items clearfix">
                                            <div class="table-item-col-0">&nbsp;</div>
                                            <div class="table-item-col-1">
                                                <div class="row">
                                                    <div class="col-md-6 text-center"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_QUANTITY'); ?></div>
                                                    <div class="col-md-6 text-center"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_ENTITY'); ?></div>
                                                </div>
                                            </div>
                                            <div class="table-item-col-pno"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_PRODUCT_NO'); ?></div>
                                            <div class="table-item-col-2"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_PRODUCT'); ?></div>
                                            <div class="table-item-col-3 text-center"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_EINZELPREIS'); ?></div>
                                            <div class="table-item-col-4 text-center" style="display: <?php echo (!empty($taxSelection) ? 'block' : 'none') ?>"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_STEUERSATZ'); ?></div>
                                            <div class="table-item-col-5 text-center"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_GESAMTPREIS'); ?></div>
                                        </div>
                                    </div>
                                    <div class="row-fluid table-items-list dd">
                                        <div class="dd-list"></div>
                                    </div>
                                    <div class="secretary-documents-table-bottom">
                                        <span class="btn btn-default item-counter" id="item-add" counter="<?php echo $this->countParameters; ?>">
                                            <i class="fa fa-plus"></i>&nbsp;<?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_ENTRY_ADD_NEW_ROW'); ?>
                                        </span>
                                        <span class="btn btn-no-bg <?php echo (!empty($taxSelection) ? 'active' : ' ') ?>" id="item-toggle-tax"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_TAXRATE_EDIT'); ?></span>
                                        <span class="btn btn-no-bg" id="item-toggle-pno"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_PRODUCT_NO_TOGGLE'); ?></span>
                                        <span class="btn btn-no-bg item-counter" id="item-add-document" counter="<?php echo $this->countParameters; ?>"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_ENTRY_ADD_NEW_ROW_DOCUMENT'); ?></span>
                                    </div>
                                </div>
                                <?php echo $this->loadTemplate('details_footer'); ?>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="secretary-document-bottom">
                                <div class="secretary-document-template">
                                    <?php if ($this->state->params->get('documents_frontend') == 1 && !empty($this->item->id) && $this->item->template > 0)
                                    {
                                        $key = md5($this->item->id . $this->item->createdEntry . $this->item->subjectid . $this->item->total);
                                        $fontendlink = \Joomla\CMS\Uri\Uri::root() . 'index.php?option=com_secretary&view=document&id=' . $this->item->id . '&key=' . $key;
                                        ?>
                                        <div class="control-group">
                                            <p class="secretary-desc"><a href="<?php echo $fontendlink ?>" target="_blank"><?php echo $fontendlink; ?></a></p>
                                        </div>
                                        <hr />
                                    <?php } ?>
                                    <div class="control-group">
                                        <h4 class="title"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_TEMPLATE'); ?>&nbsp;<a href="index.php?option=com_secretary&view=templates&extension=documents" target="_blank"><i class="fa fa-external-link"></i></a></h4>
                                        <?php echo $this->itemtemplates; ?>
                                    </div>
                                    <?php if (isset($this->item->message['template']))
                                    {
                                        ?>
                                        <div class="control-group">
                                            <h4 class="title"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_EMAIL'); ?>&nbsp;<?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_TEMPLATE'); ?></h4>
                                            <?php echo $this->emailtemplates; ?>
                                            <?php 
                                            if (isset($this->item->message['subject']))
                                            {
                                                ?>
                                                <input type="hidden" name="jform[fields][message][subject]" value="<?php echo $this->escape($this->item->message['subject']); ?>" />
                                            <?php } ?>
                                            <?php 
                                            if (isset($this->item->message['id']))
                                            {
                                                ?>
                                                <input type="hidden" name="jform[fields][message][id]" value="<?php echo $this->item->message['id']; ?>" />
                                            <?php } ?>
                                            <?php 
                                            if (isset($this->item->message['text']))
                                            {
                                                ?>
                                                <input type="hidden" name="jform[fields][message][text]" value="<?php echo $this->escape($this->item->message['text']); ?>" />
                                            <?php } ?>
                                            <?php 
                                            if (isset($this->item->message['emailed']))
                                            {
                                                ?>
                                                <input type="hidden" name="jform[fields][message][emailed]" value="<?php echo $this->item->message['emailed']; ?>" />
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                    <hr />
                                    <div class="control-group">
                                        <h4 class="title"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_LOCATION_DOCUMENTS'); ?>&nbsp;<a href="index.php?option=com_secretary&view=locations&extension=documents" target="_blank"><i class="fa fa-external-link"></i></a></h4>
                                        <div class="controls"><?php echo $this->form->getInput('office'); ?></div>
                                    </div>
                                </div>
                                <div class="secretary-document-upload">
                                    <h4 class="title"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_DOCUMENT_DESC'); ?>&nbsp;<a href="index.php?option=com_secretary&view=items&extension=uploads" target="_blank"><i class="fa fa-external-link"></i></a></h4>
                                    <div class="controls"><?php echo $this->form->getInput('upload'); ?></div>
                                </div>
                                <div class="secretary-document-zahlung">
                                    <h4 class="title"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_ENTRY_PAYMENTINFORMATIONEN'); ?></h4>
                                    <div class="control-group">
                                        <?php echo $this->form->getLabel('state'); ?>
                                        <div class="controls"><?php echo $this->form->getInput('state'); ?></div>
                                    </div>
                                    <div class="control-group">
                                        <?php echo $this->form->getLabel('deadline'); ?>
                                        <div class="controls"><?php echo $this->form->getInput('deadline'); ?></div>
                                    </div>
                                    <div class="control-group">
                                        <?php echo $this->form->getLabel('paid'); ?>
                                        <div class="controls">
                                            <div class="secretary-input-group clearfix pull-left">
                                                <div class="secretary-input-group-left"><?php echo $this->form->getInput('paid'); ?></div>
                                                <div class="secretary-input-group-right currency-control"><?php echo $this->item->currencySymbol; ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane secretary-document-main" id="more">
                    <?php echo $this->loadTemplate('extended'); ?>
                </div>
                <?php if ($user->authorise('core.admin', 'com_secretary'))
                {
                    ?>
                    <div class="tab-pane secretary-document-main" id="permission">
                        <?php echo $this->form->getInput('rules'); ?>
                    </div>
                <?php } ?>
            </div>
            <?php echo $this->form->getInput('id'); ?>
            <input type="hidden" name="jform[createdEntry]" value="<?php echo $this->item->createdEntry; ?>" />
            <input type="hidden" name="catid" value="<?php echo $this->item->catid; ?>" id="catid" />
            <input type="hidden" name="task" value="" id="formtask" />
            <?php echo \Joomla\CMS\HTML\HTMLHelper::_('form.token'); ?>
        </div>
    </form>
</div>