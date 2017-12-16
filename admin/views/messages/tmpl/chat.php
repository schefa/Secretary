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

$enableChat = (bool) $this->params->get('messages_chat',false);
$waitMsg = $this->params->get('messages_waitMsg',3000);
$waitPing = $this->params->get('messages_waitPing',10000);
  
if(false === $enableChat)
    return;

$user		= JFactory::getUser();
$userId		= (int) Secretary\Database::getQuery('subjects', (int) $user->id,'created_by','id','loadResult');

$messages_unread = Secretary\Application::parameters()->get('messages_unread', 9);

$canDelete	= $user->authorise('core.delete', 'com_secretary.message');
$canCreate	= $user->authorise('core.create', 'com_secretary.message');
$canEditState	= $user->authorise('core.edit.state', 'com_secretary.message');

if($this->isChatFrontend === true)
    $canCreate = true;

$statesSelected = array();
foreach($this->talks as $k => $talk) {
    $statesSelected[$talk->id] = ($talk->state < 1) ? $messages_unread : $talk->state;
    $talk->statustitle = (empty($talk->statustitle)) ? JText::_('COM_SECRETARY_MESSAGES_OPTION_UNREAD') : JText::_($talk->statustitle);
    $talk->message =  Secretary\Utilities::cleaner($talk->message,true);
} 

$this->document->addScript(JURI::root() .'media/secretary/assets/angular/angular.min.js');
$this->document->addScript(JURI::root() .'media/secretary/assets/angular/angular-sanitize.min.js');
$this->document->addScript(JURI::root() .'media/secretary/assets/angular/angular-route.min.js');
$this->document->addScript(JURI::root() .'media/secretary/js/secretary.messages.js?v='.SECRETARY_VERSION);

$this->online = \Secretary\Helpers\Messages::getChatOnlineUsers($this->referTo) ;

$firstMsg = ' $scope.firstMsg = ';
$firstMsg .= (isset($this->talks[0])) ? json_encode($this->talks[0],JSON_NUMERIC_CHECK).';' : '{};';

$this->document->addScriptDeclaration('
    const REFERTO = '. $this->referTo.';
    const WAITMSG = '. (int) $waitMsg .';
    const WAITPING = '. (int) $waitPing .';
    document.addEventListener("DOMContentLoaded", function(event) { 
        var appElement = document.querySelector("[ng-app=SecretaryChat]");
        var $scope = angular.element(appElement).scope();
        $scope.$apply(function() { '. $firstMsg .'
            $scope.user = {}; 
            $scope.messages = '.json_encode($this->talks,JSON_NUMERIC_CHECK).';
            $scope.selectedState = '.json_encode($statesSelected,JSON_NUMERIC_CHECK).';
            $scope.statesOptions = '.json_encode($this->states,JSON_NUMERIC_CHECK) .';
            $scope.onlineUsers = '. json_encode(array('list'=>$this->online,'total'=>count($this->online))) .';
        }); 
    });
');

?>
 

<div class="secretary-main-container message-talk" ng-app="SecretaryChat" ng-controller="SecretaryChatCtrl">

	<div class="row-fluid">
    
        <div id="secretary-message-area" class="secretary-messages-talk col-md-12">
         
            <div class="row-fluid clearfix">
            
                <div class="fullwidth margin-bottom">
                     
                    <?php if($user->authorise('core.show','com_secretary.message')) {?>
                    <a class="pull-left btn btn-default margin-right" href="<?php echo Secretary\Route::create('messages',array('catid'=>0)); ?>">
                        <i class="fa fa-angle-double-up"></i> <?php echo JText::_('COM_SECRETARY_MESSAGES'); ?>
                    </a> 
                	<?php } ?>
                	
                    <a class="pull-left btn btn-default margin-right" href="<?php echo Secretary\Route::create('messages',array('layout'=>'talk','rid'=>$this->referTo,'catid'=>$this->categoryId,"k"=>md5($this->talks[0]->id.$this->talks[0]->created))); ?>">
                    <?php $t = JText::_('COM_SECRETARY_STANDARD');
                    echo JText::sprintf('COM_SECRETARY_SWITCH_TO',$t) ?>
                    </a>
                    
            		<?php if(JFactory::getApplication()->isAdmin()) { ?> 
                    <a class="pull-left btn btn-default margin-right" target="_blank" href="<?php echo JUri::root(true) ."/index.php?option=com_secretary&view=messages&layout=chat&rid=".$this->referTo."&catid=".$this->categoryId."&k=".md5($this->talks[0]->id.$this->talks[0]->created); ?>"><i class="fa fa-link"></i>&nbsp;Public Link</a>
                    <?php } ?>
                        
        			<div class="pull-left btn-group btn-default secretary-online-users">
        				<div class="btn online">Online: <strong>{{ online.total || '<?php echo count($this->online); ?>' }}</strong></div>
        				<div class="btn secretary-online-users-list">
            			<div class="pull-left" ng-repeat="user in onlineUsers.list">{{ user.name }}<i ng-show="!$last">,&nbsp;</i></div>
            			</div>
        			</div>
            		
                </div>
            
                <div class="pull-left">
                
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
                
            </div>
            
            <hr />

    <div ng-app="SecretaryChat" ng-controller="SecretaryChatCtrl">

        <div class="secretary-chat-messages">
        
            <div class="secretary-table-row message-talk-item" ng-repeat="message in messages" ng-class="{'mine':message.created_by == '<?php echo $userId ?>' }">
            
            	<div ng-if="message.subject.length > 0" class="message-talk-item-top clearfix">
                    {{ message.subject }} 
            	</div>
            
                <div class="secretary-chat-text right">
                    <span ng-bind-html="message.message"></span>
                </div>
                
                <div class="message-talk-item-bottom">
                	{{ message.created_by_user ||  ( message.created_by+' ('+ message.created_by_alias+')' ) }} <?php echo JText::_('COM_SECRETARY_MESSAGES_AT'); ?> {{ message.created | date }} 
                	<span ng-if="message.created_by == '<?php echo $userId ?>'">- <?php echo JText::_('COM_SECRETARY_STATUS')?> : {{ message.statustitle }}</span>
                </div>
                
                <div class="message-toolbar">
                    <?php if($canDelete === true) { ?>
                    <div class="message-delete hasTooltip" ng-click="deleteMsg( message.id )" data-original-title="<?php echo JText::_('COM_SECRETARY_DELETE')?>"><i class="fa fa-remove"></i></div> 
                    <?php } ?>
                    
                    <?php if($canEditState === true) { ?>
                    <div class="message-state select-arrow select-arrow-white" ng-if="message.created_by != <?php echo $userId ?> ">       
                        <select ng-change="changeMsgState( message.id )" ng-model="selectedState[message.id]" ng-options="o.value as o.text for o in statesOptions">
                        </select>
    
                    </div>
                    <?php } ?>
                </div>
                
            </div>
            
        </div>
        
        <?php if(true === $canCreate) { ?>
        <div class="message-talk-item-form">
            <form name="form" ng-submit="saveMessage()" novalidate>

				<?php if($user->id < 1) { ?>
				<div class="fullwidth margin-bottom">
                	<input class="fullwidth" name="uName" type="text" placeholder="<?php echo JText::_('COM_SECRETARY_NAME') ?>" ng-model="user.name" required />
                	<input class="fullwidth" name="uEmail" type="email" placeholder="<?php echo JText::_('COM_SECRETARY_EMAIL') ?>" ng-model="user.email" required />
                    <div ng-show="form.$submitted || form.uEmail.$touched">
                      <span ng-show="form.uEmail.$error.email"><?php echo JText::_('COM_SECRETARY_EMAIL_INVALID')?></span>
                    </div>
                	<input name="uKat" type="hidden" ng-model="user.kat" value="<?php echo $this->key ?>" ng-init="user.kat = '<?php echo $this->key ?>'" />
				</div>
				<?php } ?>

            	<input id="secretary-form-subject" class="fullwidth" type="text" placeholder="<?php echo JText::_('COM_SECRETARY_TITLE') ?>: {{ firstMsg.subject }}" ng-model="user.subject" />
                <textarea id="secretary-form-message" name="uMessage" placeholder="Type message..." autofocus="autofocus" class="fullwidth" ng-model="user.message" required></textarea> 

                <div class="message-talk-item-form-bottom">
                    <button class="btn btn-success" ng-disabled="!user.message.length || form.$invalid" type="submit"><?php echo JText::_('COM_SECRETARY_SEND')?></button>
                    <div class="btn">{{ user.message.length || '0' }}</div> 
                </div>
                
            </form>
        </div>
        <?php } ?>

    </div>
        

        </div> 
    
	</div>
</div>
