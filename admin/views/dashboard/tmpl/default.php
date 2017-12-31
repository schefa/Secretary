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

// Get Business Data
$business	= Secretary\Application::company();
$currency	= $business['currency'];

$user		= Secretary\Joomla::getUser();
$canOrder	= $user->authorise('core.edit.state', 'com_secretary');

$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
?>

<div class="secretary-main-container dashboard">
<div class="secretary-main-area">
    
    <div class="dashboard-icons fullwidth">
    
        <div class="fullwidth">
            <?php if ($user->authorise('core.show', 'com_secretary.folder')) { ?>
            <div class="col-md-3">
                <a class="icon" href="index.php?option=com_secretary&view=folders&extension=documents">
                    <span class="fa fa-folder-o"></span>
                    <?php echo JText::_('COM_SECRETARY_FOLDERS'); ?>
                </a>
            </div>
            <?php } ?>
            
            <?php if ($user->authorise('core.show', 'com_secretary.document')) { ?>
            <div class="col-md-3">
                <a class="icon" href="index.php?option=com_secretary&view=documents&catid=0">
                    <span class="fa fa-list"></span>
                    <?php echo JText::_('COM_SECRETARY_DOCUMENTS'); ?>
                </a>
            </div>
            <?php } ?>
            
            <?php if ($user->authorise('core.show', 'com_secretary.subject')) { ?>
            <div class="col-md-3">
                <a class="icon" href="index.php?option=com_secretary&view=subjects">
                    <span class="fa fa-users"></span>
                    <?php echo JText::_('COM_SECRETARY_SUBJECTS'); ?>
                </a>
            </div>
            <?php } ?>
            
            <?php if ($user->authorise('core.show', 'com_secretary.product')) { ?>
            <div class="col-md-3">
                <a class="icon" href="index.php?option=com_secretary&view=products">
                    <span class="fa fa-shopping-cart"></span>
                <?php echo JText::_('COM_SECRETARY_PRODUCTS'); ?>
                </a>
            </div>
            <?php } ?>
            
            <?php if ($user->authorise('core.show', 'com_secretary.time')) { ?>
            <div class="col-md-3">
                <a class="icon" href="index.php?option=com_secretary&view=times&layout=list">
                    <span class="fa fa-calendar"></span>
                	<?php echo JText::_('COM_SECRETARY_TIMES'); ?>
                </a>
            </div>
            <?php } ?>
            
            <?php if ($user->authorise('core.show', 'com_secretary.message')) { ?>
            <div class="col-md-3">
                <a class="icon" href="index.php?option=com_secretary&view=messages">
                    <span class="fa fa-comment"></span>
                <?php echo JText::_('COM_SECRETARY_MESSAGES'); ?>
                </a>
            </div>
            <?php } ?>
            
            <?php if ($user->authorise('core.show', 'com_secretary.accounting')) { ?>
            <div class="col-md-3">
                <a class="icon" href="index.php?option=com_secretary&view=accountings">
                    <span class="fa fa-book"></span>
                <?php echo JText::_('COM_SECRETARY_ACCOUNTINGS'); ?>
                </a>
            </div>
            <?php } ?>
            
            <?php if ($user->authorise('core.show', 'com_secretary.market')) { ?>
            <div class="col-md-3">
                <a class="icon" href="index.php?option=com_secretary&view=markets">
                    <span class="fa fa-certificate"></span>
                <?php echo JText::_('COM_SECRETARY_MARKETS'); ?>
                </a>
            </div>
            <?php } ?>
            
        </div>
        
        <hr>
            
        <div class="fullwidth">
            
            <?php if ($user->authorise('core.show', 'com_secretary.business')) { ?>
            <div class="col-md-3">
            	<a class="icon" href="index.php?option=com_secretary&view=businesses">
                <span class="fa fa-home"></span><?php echo JText::_('COM_SECRETARY_BUSINESS'); ?>
                </a>
            </div>
            <?php } ?>
            
            <?php if ($user->authorise('core.show', 'com_secretary.location')) { ?>
            <div class="col-md-3">
                    <a class="icon" href="<?php echo JRoute::_('index.php?option=com_secretary&view=locations'); ?>">
                        <span class="fa fa-cube"></span>
                        <?php echo JText::_('COM_SECRETARY_LOCATIONS'); ?>
                    </a>
            </div>
            <?php } ?>
            
            <?php if ($user->authorise('core.show', 'com_secretary.reports')) { ?>
            <div class="col-md-3">
                    <a class="icon" href="index.php?option=com_secretary&view=reports">
                        <span class="fa fa-bar-chart"></span>
                    <?php echo JText::_('COM_SECRETARY_REPORTS'); ?>
                    </a>
            </div>
            <?php } ?>
            
            <?php if ($user->authorise('core.show', 'com_secretary.template')) { ?>
            <div class="col-md-3">
                    <a class="icon" href="index.php?option=com_secretary&view=templates">
                        <span class="fa fa-print"></span>
                    <?php echo JText::_('COM_SECRETARY_TEMPLATES'); ?>
                    </a>
            </div>
            <?php } ?>
            
            <?php if ($user->authorise('core.admin', 'com_secretary')) { ?>
            <div class="col-md-3">
                    <a class="icon" href="index.php?option=com_secretary&view=items&extension=status">
                        <span class="fa fa-folder-o"></span>
                    <?php echo JText::_('COM_SECRETARY_STATUS'); ?>
                    </a>
            </div>
            <?php } ?>
            
            <?php if ($user->authorise('core.admin', 'com_secretary')) { ?>
            <div class="col-md-3">
                    <a class="icon" href="index.php?option=com_secretary&view=items&extension=entities">
                        <span class="fa fa-magic"></span>
                    <?php echo JText::_('COM_SECRETARY_ENTITIES'); ?>
                    </a>
            </div>
            <?php } ?>
            
            
            <?php if ($user->authorise('core.admin', 'com_secretary')) { ?>
            <div class="col-md-3">
            	<a class="icon" href="index.php?option=com_secretary&view=items&extension=uploads">
                <span class="fa fa-upload"></span><?php echo JText::_('COM_SECRETARY_FILES'); ?>
                </a>
            </div>
            
            <div class="col-md-3">
                    <a class="icon" href="index.php?option=com_secretary&view=item&id=1&layout=edit&extension=settings">
                        <span class="fa fa-cog"></span>
                        <?php echo JText::_('COM_SECRETARY_SETTINGS'); ?>
                    </a>
            </div>
            <?php } ?>
        </div>
        
    </div>
    
</div>

<?php if(!$user->guest) { ?>
<div class="secretary-main-area"> 
    <div class="secretary-activities">
        
        <form action="<?php echo JRoute::_('index.php?option=com_secretary&view=dashboard'); ?>" method="post" name="adminForm" id="adminForm">
    
            <h2 class="documents-title">
			<?php
			echo JText::_('COM_SECRETARY_ACTIVITY'); 
            if ($this->canDo->get('core.delete')) {
                echo Secretary\Navigation::ToolbarItem('dashboard.delete', 'COM_SECRETARY_TOOLBAR_DELETE', true, 'default hidden-toolbar-btn', 'fa-trash');
            }
            ?>
            </h2>
            <hr />
            
            <?php if(!empty($this->activities)) { ?>
            
                <table class="table table-hover" id="documentsList">
                    <thead>
                        <tr>
                        	<?php if($this->canDo->get('core.delete')) { ?>
                            <th width="1%" class="hidden-phone"><?php echo Secretary\HTML::_('status.checkall'); ?><span class="lbl"></span></th>
                            <?php } ?>
                            <th><?php echo JHtml::_('grid.sort',  'COM_SECRETARY_DATE', 'a.created', $listDirn, $listOrder); ?></th> 
                            <th><?php echo JHtml::_('grid.sort',  'COM_SECRETARY_ACTIVITY', 'a.id', $listDirn, $listOrder); ?></th>
                            <th><?php echo JHtml::_('grid.sort',  'COM_SECRETARY_SECTION', 'a.extension', $listDirn, $listOrder); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    	<?php foreach ($this->activities as $i => $activity) : ?>
                        <tr class="row<?php echo $i % 2 ; ?>">
                            
                        	<?php if($this->canDo->get('core.delete')) { ?>
                            <td class="center hidden-phone">
                                <?php echo JHtml::_('grid.id', $i, $activity->id); ?>
                                <span class="lbl"></span>
                            </td>
                            <?php } ?>
                            
                            <td class="left"> <?php echo JHtml::_('date', $activity->created, JText::_('DATE_FORMAT_LC2'));?> </td>
                            
                            <td class="left"> <?php echo \Secretary\Helpers\Activity::get($activity->id); ?> </td>
                            
                            <td><?php echo JText::_('COM_SECRETARY_'.strtoupper($activity->extension)); ?></td>
                            
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <?php if(count($this->activities) > $this->state->get('list.limit')) { ?>
                    <tfoot class="table-list-pagination">
                    <tr>
                        <td colspan="4"><div class="pull-left"><?php echo $this->pagination->getListFooter(); ?></div></td>
                    </tr>
                    </tfoot>
                    <?php } ?>
                </table>
        
                
            <?php } else { echo '<div class="">'.JText::_('COM_SECRETARY_NONE').'</div>'; } ?>
                
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="boxchecked" value="0" />
            <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
            <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
            <?php echo JHtml::_('form.token'); ?>
        </form>
            
    </div>
    
</div>
<?php } ?>



</div> 
