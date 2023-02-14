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

$extension = 'times';

$user = \Secretary\Joomla::getUser();

$status = Secretary\Database::getQuery('status',$this->item->state,'id','title','loadResult');
$category = Secretary\Database::getQuery('folders',$this->item->catid,'id','title','loadResult');

// Document
if($this->item->document_id > 0) {
	$document = array();
	$object = Secretary\Database::getQuery('documents',$this->item->document_id);
	if(isset($object->catid) && $object->catid > 0) {
    	$object->category = Secretary\Database::getQuery('folders',$object->catid,'id','title','loadResult');
	   \Secretary\Helpers\Documents::getDocumentsPrepareRow($document,$object);
	}
}
?>
         
<ul class="nav nav-tabs fullwidth" id="myTab" role="tablist">
    <li class="active"><a href="#home" role="tab" data-toggle="tab"><?php echo JText::_('JDETAILS', true); ?></a></li>
    <li><a href="#fields" role="tab" data-toggle="tab"><?php echo JText::_('COM_SECRETARY_FIELDS', true); ?></a></li>
</ul>

<div class="tab-content">

    <div class="tab-pane active" id="home">
    
        <div class="fullwidth">
        
            <div class="col-md-9">
            
                <div class="row">
                
                    <div class="col-md-6">
                        <div class="control-label"><?php echo $this->form->getLabel('title'); ?></div>
                        <div class="controls"><?php echo $this->item->title; ?></div>
                    </div>
                    <div class="col-md-3">
                        <div class="control-label"><?php echo $this->form->getLabel('state'); ?></div>
                        <div class="controls"><?php echo JText::_($status); ?></div>
                    </div>
                    <div class="col-md-3">
                        <div class="control-label"><?php echo $this->form->getLabel('catid'); ?></div>
                        <div class="controls"><?php echo JText::_($category); ?></div>
                    </div>
                    
                    <div class="col-md-12"><hr /></div>
                    
                    <div class="col-md-6">
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('startDate'); ?></div>
                            <div class="controls"><?php echo $this->item->startDate; ?></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('endDate'); ?></div>
                            <div class="controls"><?php echo $this->item->endDate; ?></div>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                    <hr />
                        <div class="control-label"><?php echo $this->form->getLabel('text'); ?></div>
                        <div class="controls"><?php echo $this->item->text; ?></div>
                    </div>
                    
                </div>
                
            </div>
            <div class="col-md-3">
                <div class="fullwidth">
                
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('upload'); ?></div>
                        <div class="controls">
						<?php if(!empty($this->item->upload)) {
                            $logoImage = Secretary\Database::getQuery('uploads', $this->item->upload,'id','business,title,folder');?>
                            <div class="upload-file">
                            <?php \Secretary\Helpers\Uploads::getUploadFile( $logoImage, NULL, 200); ?>
                            </div>
                        <?php } ?>
						</div>
                    </div>
                    
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('contacts'); ?></div>
                        <div class="controls">
						<?php if(!empty( $this->contacts)) { ?>
                        	<ul>
                            <?php foreach( $this->contacts AS $idx => $contact) {
								$subject = Secretary\Database::getQuery('subjects',$contact->id,'id','firstname,lastname'); ?>
								<li>
									<a href="index.php?option=com_secretary&view=subject&id=<?php echo $contact->id; ?>"><?php echo $subject->firstname.' '.$subject->lastname; ?></a>
                                    <a target="_blank" href="index.php?option=com_secretary&view=message&layout=edit&contact=<?php echo $contact->id;?>"><i class="fa fa-envelope-o"></i></a>
                                </li>
							<?php } ?>
                            </ul>
						<?php } ?>
                        </div>
                    </div>
                    
                    <hr />
                    <h4><?php echo JText::_('COM_SECRETARY_BUDGET'); ?></h4>
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('document_id'); ?></div>
                        <div class="controls">
						<?php
                        	if($this->item->document_id > 0 && !empty($document)) {
								echo '<a href="index.php?option=com_secretary&view=document&id='.$this->item->document_id .'">'
									. $document['value'] .'</a>'
									.  Secretary\Utilities\Number::getNumberFormat($document['total']) .' '.$document['currency'];
							}
						?>
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('location_id'); ?></div>
                        <div class="controls"><?php 
                        	if($this->item->location_id > 0) {
								echo '<a href="index.php?option=com_secretary&view=location&id='.$this->item->location_id .'">'
									. Secretary\Database::getQuery('locations',$this->item->location_id,'id','title','loadResult') .'</a>';
							}
							?>
						</div>
                    </div>
                    
                </div>
            </div>
        </div>
    
    </div>
    
    <div class="tab-pane" id="fields">

        <div class="fields-items form-horizontal">
            <?php if(!empty($this->item->fields) && ($fields = json_decode($this->item->fields, true))) { ?>
                <?php foreach($fields as $field) { ?>
                    
                <div class="control-group">
                    <div class="control-label"><label><?php echo $field[1]; ?></label></div>
                    <div class="controls"><?php echo $field[2]; ?></div>
                </div>

                <?php } ?>
            <?php } ?>
        </div>
                        
    </div>
    
</div>
<input type="hidden" name="extension" value="time" />
