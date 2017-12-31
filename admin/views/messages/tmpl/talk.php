<?php
/**
 * @version     3.0.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
 */
 
// No direct access
defined('_JEXEC') or die;

$user		= JFactory::getUser();
$userId		= (int) Secretary\Database::getQuery('subjects', (int) $user->id,'created_by','id','loadResult');

$canDelete	= $user->authorise('core.delete', 'com_secretary');
$canUpload	= $user->authorise('core.upload', 'com_secretary');
$documentSize	= Secretary\Application::parameters()->get('documentSize');
$enableChat = (bool) $this->params->get('messages_chat',false);

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

$category = (!empty($this->categoryId)) ? '&catid='.$this->categoryId :  '&catid=0';
$contact_to = (!empty($this->contact_to)) ? '&contact_id='.$this->contact_to : '';

$first_key = 0;
//$talks = Secretary\Utilities::reorderTree($this->talks,'refer_to' );
$talks =  $this->talks ; 


$col = empty( $this->otherTalks) ? '12' : '9';
?>

<div class="secretary-main-container message-talk" ng-app="SecretaryChat" ng-controller="SecretaryChatCtrl">

	<div class="row-fluid">
    
        <div id="secretary-message-area" class="secretary-messages-talk col-md-<?php echo $col ?>">
            
            <form action="<?php echo JRoute::_('index.php?option=com_secretary&view=messages&layout=talk'.$category . $contact_to.'&rid='.$talks[0]->id); ?>" method="post" name="adminForm" id="adminForm">
            
                <div class="row-fluid clearfix">
                
                    <div class="fullwidth margin-bottom">
                    
                    	<?php if($user->authorise('core.show','com_secretary.message')) {?>
                        <a class="pull-left btn btn-default margin-right" href="<?php echo Secretary\Route::create('messages',array('catid'=>0)); ?>">
                            <i class="fa fa-angle-double-up"></i> <?php echo JText::_('COM_SECRETARY_MESSAGES'); ?>
                        </a>
                		<?php } ?>
                		
                		<?php if(true === $enableChat){ ?>
                        <a class="pull-left btn btn-default margin-right" href="<?php echo Secretary\Route::create('messages',array('layout'=>'chat','rid'=>$this->referTo,'catid'=>$this->categoryId,"k"=>md5($this->talks[0]->id.$this->talks[0]->created))); ?>">
                        <?php $t = JText::_('COM_SECRETARY_LIVE_CHAT');
                        echo JText::sprintf('COM_SECRETARY_SWITCH_TO',$t) ?>
                        </a>
                		<?php } ?>
                            
                		<?php if(JFactory::getApplication()->isAdmin()) { ?> 
                        <a class="pull-left btn btn-default" target="_blank" href="<?php echo JUri::root(true) ."/index.php?option=com_secretary&view=messages&layout=talk&rid=".$this->referTo."&catid=".$this->categoryId."&k=".md5($this->talks[0]->id.$this->talks[0]->created); ?>"><i class="fa fa-link"></i>&nbsp;Public Link</a>
                        <?php } ?>
                    
                    </div>
                    
                    <?php if(!empty($this->categoryId) || !empty($this->contact_to)) { ?>
                    
                        <div class="fullwidth margin-bottom">
                        <div class="secretary-title">
                             
                            <span class="margin-right">
                            <?php 
                            if(!empty($this->contact_to) && $this->contact_to > 0) {
                                $contact = Secretary\Database::getJDataResult('users',(int) $this->contact_to, 'name');
                                echo JText::sprintf('COM_SECRETARY_MESSAGES_CONTACTTOCONTACT', $contact);
                            } elseif(!empty($this->categoryId)) {
                                $categoryT = Secretary\Database::getQuery('folders',$this->categoryId,'id','title','loadResult');
                                echo JText::sprintf('COM_SECRETARY_MESSAGES_CONTACTTOCATEGORY', $categoryT);
                            }
                            ?>
                            </span>

                        </div>
                        </div>

                    <?php } ?>

                </div>
                
                <div class="secretary-toolbar">
                
                	<?php $this->addToolbar(); ?>
                    

                    <div class="pull-right margin-right secretary-table-pagination"><?php echo $this->pagination->getListFooter(); ?></div>
                    
                </div> 
                
                <hr />
                
                <?php if (empty($talks)) : ?>
                
                    <div class="alert alert-no-items">
                        <?php echo JText::_('COM_SECRETARY_NO_MATCHING_RESULTS'); ?>
                    </div>
                    
                <?php else : ?>
                
                    <div class="secretary-chat-messages secretary-table">
                    
                        <div class="secretary-table-body fullwidth">
                            <?php
                            foreach ($talks as $i => $item) : 
                            
                                if(is_numeric($item->created_by)) {
                                    $fromUser = Secretary\Database::getQuery('subjects', $item->created_by);
                                    $from = Secretary\Database::getJDataResult('users',(int) $fromUser->created_by,'name');
                                } else {
                                    $from = $item->created_by;
                                }
                                if(!empty($item->created_by_alias)) $from .= " (". $item->created_by_alias .")";
                                
                            ?>
                            <div class="secretary-table-row message-talk-item  <?php if(intval($item->created_by) === $userId) { echo 'mine'; } ?>">
                                <?php if(intval($item->created_by) === $userId) { 
                                    $email_to = ($item->refer_to > 0) ? Secretary\Database::getQuery('messages', (int) $item->refer_to, 'id', 'created_by_alias', 'loadResult') : '?';
                                    if(strlen($email_to) > 0) {
                                ?>
                                    <a class="message-talk-item-email" href="index.php?option=com_secretary&task=message.email&id=<?php echo $item->id; ?>&rid=<?php echo $item->refer_to; ?>"><?php echo JText::sprintf('COM_SECRETARY_EMAIL_SEND_TO', $email_to);?></a>
                                <?php }} ?>
                                
                                <div class="message-talk-item-top clearfix">
                                
                                    <?php if($canDelete) { ?>
                                    <div class="pull-right">
                                        <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                                    </div>
                                    <?php } ?>
                                    
                                    <?php if(intval($item->created_by) !== $userId) { ?>
                                    <div class="pull-right select-arrow-small select-small select-arrow select-arrow-white margin-right">
                                        <select data-id="<?php echo (int) $item->id; ?>">
                                            <?php echo JHtml::_('select.options', $this->states, 'value', 'text', $item->state, true);?>
                                        </select>
                                    </div>
                                    <?php } ?>
                                    
                                    <a class="pull-left message-talk-item-title hasTooltip" href="<?php echo Secretary\Route::create('message', array('id' => (int) $item->id)); ?>" data-original-title="<?php echo JText::_('COM_SECRETARY_SHOW'); ?>" title="<?php echo JText::_('COM_SECRETARY_SHOW'); ?>">
                                        <?php echo Secretary\Utilities::cleaner(($item->subject),true); ?>&nbsp;<i class="fa fa-newspaper-o"></i></a>
                                </div>
                                
                                <?php echo Secretary\Utilities::cleaner(($item->message),true); ?>
                            
                                <div class="message-talk-item-bottom"><?php echo $this->escape($from) .' '. JText::_('COM_SECRETARY_MESSAGES_AT') .' '. JHtml::_('date', $item->created, JText::_('DATE_FORMAT_LC2')); ?> 
                                
                                <?php
                                if($item->created_by != $userId) {
                                    $item->statustitle = (empty($item->statustitle)) ? JText::_('COM_SECRETARY_MESSAGES_OPTION_UNREAD') : JText::_($item->statustitle);
                    	        echo JText::_('COM_SECRETARY_STATUS'). ' : '.  $item->statustitle;
                    	        }
                    	        ?>
								
                				</div>
                                
                                <?php if(!empty($item->upload)) { ?>
                                    <a class="message-talk-item-attachment modal show" rel="{size: {x: 900, y: 500}, handler:'iframe'}" href="<?php echo "index.php?option=com_secretary&task=item.openFile&id=".$item->upload; ?>"><i class="fa fa-paperclip"></i> <?php echo JText::_('COM_SECRETARY_DOCUMENT_DESC'); ?></a>
                                <?php } ?>
                                
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                    </div>
                <?php endif; ?>
                
                <?php include_once(JPATH_ADMINISTRATOR.'/components/com_secretary/views/messages/tmpl/default_batch.php'); ?>
        
                <input type="hidden" name="task" value="" />
                <input type="hidden" name="boxchecked" value="0" />
                <?php if( !empty($this->layout) ) { ?>
                <input type="hidden" name="layout" value="<?php echo $this->layout; ?>" />
                <?php } ?>
                <?php if( !empty($this->contact_to) ) { ?>
                <input type="hidden" name="contact_to" value="<?php echo $this->contact_to; ?>" />
                <?php } ?>
                <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
                <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
                <?php echo JHtml::_('form.token'); ?>
                
            </form>
            
            <div class="message-talk-item-form">
                <form action="<?php echo JRoute::_('index.php?option=com_secretary&view=messages&layout=talk'.$category . $contact_to.'&rid='.$talks[0]->id); ?>" method="post" enctype="multipart/form-data">
                    <input id="secretary-form-subject" name="jform[subject]" class="form-control fullwidth" type="text" placeholder="<?php echo JText::_('COM_SECRETARY_TITLE');?>" />
                    <textarea id="secretary-form-message" name="jform[message]" class="form-control fullwidth" placeholder="<?php echo JText::_('COM_SECRETARY_MESSAGE');?>" rows="20" cols="10"></textarea>
                    
                    
                    <?php if($canUpload) { ?>
                    <div class="fullwidth">
                        <div class="pull-left upload-file">
                            <div class="secretary-desc"><?php echo JText::_('COM_SECRETARY_DOCUMENT_SIZE_ALLOWED') .' '. \Secretary\Utilities\Number::human_filesize($documentSize) ; ?>B</div>
                            <input type="file" name="jform[upload]" id="jform_upload">
                        </div>
                    </div>
                    <?php } ?>
             
                    <div class="message-talk-item-form-bottom">
                        <button type="submit" class="btn btn-success disabled"><i class="fa fa-email"></i>&nbsp;<?php echo JText::_('COM_SECRETARY_SAVE');?></button>
                        <div class="btn counter">0</div>
                    </div>
                    
                    <input type="hidden" name="jform[refer_to]" value="<?php echo $talks[$first_key]->id; ?>" />
                    <input type="hidden" name="jform[contact_to]" value="<?php echo $this->escape($talks[$first_key]->created_by); ?>" />
                    <input type="hidden" name="jform[contact_to_alias]" value="<?php echo $this->escape($talks[$first_key]->created_by_alias); ?>" />
                    <input type="hidden" name="jform[catid]" value="<?php echo ($talks[$first_key]->catid) ? $talks[$first_key]->catid: $this->categoryId; ?>" />
                    <input type="hidden" name="task" value="message.save" />
                    <?php echo JHtml::_('form.token'); ?>
                </form>
            </div>
            
        </div>

		<?php if( !empty( $this->otherTalks) ) { ?>
        <div class="col-md-3">
			<?php echo $this->loadTemplate('sidebar'); ?>
        </div>
        <?php } ?>
    
	</div>
</div>
