<?php
/** @version     3.0.0
 *
 * @copyright    Copyright (C) 2026 Fjodor Schaefer. All rights reserved.
 * @license      GNU General Public License version 2 or later.
 */

defined('_JEXEC') or die;

$user	= \Joomla\CMS\Factory::getUser();

$showDocuments   = (int) $params->get('showDocuments',0);

if ($showDocuments === 1)
{
    $documents = \Secretary\Database::getQuery('folders', 'documents','extension','id,title','loadObjectList');
}

$showSubjects   = (int) $params->get('showSubjects',0);

if ($showSubjects === 1)
{
    $subjects = \Secretary\Database::getQuery('folders', 'subjects','extension','id,title','loadObjectList');
}

$showProducts   = (int) $params->get('showProducts',0);

if ($showProducts === 1)
{
    $products = \Secretary\Database::getQuery('folders', 'products','extension','id,title','loadObjectList');
}

$showTimes   = (int) $params->get('showTimes',0);

if ($showTimes === 1)
{
    $times = \Secretary\Database::getQuery('folders', 'times','extension','id,title','loadObjectList');
}

?>
<div id="secretaryQuickIconsTitle">
	<h1 class="text-center"><?php echo $business['title'];?></h1>
	<?php 
    if (!empty( $business['slogan'] ))
    {
        ?><h3 class="text-center"><?php echo $business['slogan'];?></h3><?php } ?>
</div>

<div id="secretaryQuickIcons">

	<div class="grid fullwidth">
    	
        <?php if ($user->authorise('core.show', 'com_secretary.folder'))
        {
            ?>
        <div class="grid-item">
            <div class="qicon">
                <a href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_secretary&view=folders&extension=documents'); ?>">
                    <span class="fa fa-folder-o"></span>
                    <?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_CATEGORIES'); ?>
                </a>
            </div>
        </div>
        <?php } ?>
        
        <?php if ($user->authorise('core.show', 'com_secretary.document'))
        {
            ?>
        <div class="grid-item">
            <div class="qicon">
                <a href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_secretary&view=documents&catid=0'); ?>">
                    <span class="fa fa-file"></span>
                    <?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_DOCUMENTS'); ?>
                </a>
                <?php if (!empty($documents))
                {
                    ?>
                <div class="qicon-wrapper-list">
                    <?php foreach ($documents as $document)
                    {
                        ?>
                	<div><a href="index.php?option=com_secretary&view=documents&catid=<?php echo $document->id ?>"><?php echo \Joomla\CMS\Language\Text::_( $document->title ) ?></a></div>
                <?php } ?>
                </div>
                <?php } ?>
            </div>
        </div>
        <?php } ?>
        
        <?php if ($user->authorise('core.show', 'com_secretary.subject'))
        {
            ?>
        <div class="grid-item">
            <div class="qicon">
                <a href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_secretary&view=subjects'); ?>">
                    <span class="fa fa-users"></span>
                    <?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_SUBJECTS'); ?>
                </a>
                <?php if (!empty($subjects))
                {
                    ?>
                <div class="qicon-wrapper-list">
                    <?php foreach ($subjects as $subject)
                    {
                        ?>
                	<div><a href="index.php?option=com_secretary&view=subjects&catid=<?php echo $subject->id ?>"><?php echo \Joomla\CMS\Language\Text::_( $subject->title ) ?></a></div>
                <?php } ?>
                </div>
                <?php } ?>
            </div>
        </div>
        <?php } ?>
        
        <?php if ($user->authorise('core.show', 'com_secretary.product'))
        {
            ?>
        <div class="grid-item">
            <div class="qicon">
                <a href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_secretary&view=products'); ?>">
                    <span class="fa fa-shopping-cart"></span>
                <?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_PRODUCTS'); ?>
                </a>
                <?php if (!empty($products))
                {
                    ?>
                <div class="qicon-wrapper-list">
                    <?php foreach ($products as $product)
                    {
                        ?>
                	<div><a href="index.php?option=com_secretary&view=products&catid=<?php echo $product->id ?>"><?php echo \Joomla\CMS\Language\Text::_( $product->title ) ?></a></div>
                <?php } ?>
                </div>
                <?php } ?>
            </div>
        </div>
        <?php } ?>

            <?php if ($user->authorise('core.show', 'com_secretary.time'))
            {
                ?>
            <div class="grid-item">
                <div class="qicon">
                <a href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_secretary&view=times&layout=list'); ?>">
                    <span class="fa fa-calendar"></span>
                <?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_TIMES'); ?>
                </a>
                    <?php if (!empty($times))
                    {
                        ?>
                    <div class="qicon-wrapper-list">
                        <?php foreach ($times as $time)
                        {
                            ?>
                    	<div><a href="index.php?option=com_secretary&view=times&catid=<?php echo $time->id ?>"><?php echo \Joomla\CMS\Language\Text::_( $time->title ) ?></a></div>
                    <?php } ?>
                    </div>
                    <?php } ?>
                </div>
            </div>
            <?php } ?>
            
            <?php if ((file_exists(JPATH_ADMINISTRATOR.'/components/com_secretary/views/markets/view.html.php')) && $user->authorise('core.show', 'com_secretary.market'))
            {
                ?>
            <div class="grid-item">
                <div class="qicon">
                    <a href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_secretary&view=markets'); ?>">
                        <span class="fa fa-certificate"></span>
                    <?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_MARKETS'); ?>
                    </a>
                </div>
            </div>
            <?php } ?>
        
    </div>
    
	<hr />
	
	<div class="fullwidth">
        
        <?php if ($user->authorise('core.show', 'com_secretary.business'))
        {
            ?>
        <div class="grid-item">
            <div class="qicon">
                <a href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_secretary&view=businesses'); ?>">
                    <span class="fa fa-home"></span>
                <?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_BUSINESS'); ?>
                </a>
            </div>
        </div>
        <?php } ?>
        
        <?php if ($user->authorise('core.show', 'com_secretary.location'))
        {
            ?>
        <div class="grid-item">
            <div class="qicon">
                <a href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_secretary&view=locations'); ?>">
                    <span class="fa fa-cube"></span>
                    <?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_LOCATIONS'); ?>
                </a>
            </div>
        </div>
        <?php } ?>
        
        <?php if ($user->authorise('core.show', 'com_secretary.reports'))
        {
            ?>
        <div class="grid-item">
            <div class="qicon">
                <a href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_secretary&view=reports'); ?>">
                    <span class="fa fa-bar-chart"></span>
                <?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_REPORTS'); ?>
                </a>
            </div>
        </div>
        <?php } ?>
        
        <?php if ($user->authorise('core.show', 'com_secretary.template'))
        {
            ?>
        <div class="grid-item">
            <div class="qicon">
                <a href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_secretary&view=templates'); ?>">
                    <span class="fa fa-print"></span>
                <?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_TEMPLATES'); ?>
                </a>
            </div>
        </div>
        <?php } ?>
        
        <?php if ($user->authorise('core.admin', 'com_secretary'))
        {
            ?>
        <div class="grid-item">
            <div class="qicon">
                <a href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_secretary&view=items&extension=status'); ?>">
                    <span class="fa fa-paperclip"></span>
                <?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_STATUS'); ?>
                </a>
            </div>
        </div>
        
        <div class="grid-item">
            <div class="qicon">
                <a href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_secretary&view=items&extension=fields'); ?>">
                    <span class="fa fa-th-large"></span>
                <?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_FIELDS'); ?>
                </a>
            </div>
        </div>
        
        <div class="grid-item">
            <div class="qicon">
                <a href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_secretary&view=items&extension=entities'); ?>">
                    <span class="fa fa-text-height"></span>
                <?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_ENTITIES'); ?>
                </a>
            </div>
        </div>
        <?php } ?>
        
        <?php if ($user->authorise('core.admin', 'com_secretary'))
        {
            ?>
        <div class="grid-item">
            <div class="qicon">
                <a href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_secretary&view=items&extension=uploads'); ?>">
                    <span class="fa fa-upload"></span>
                	<?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_FILES'); ?>
                </a>
            </div>
        </div>
        
        <div class="grid-item">
            <div class="qicon">
                <a href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_secretary&view=item&id=1&layout=edit&extension=settings'); ?>">
                    <span class="fa fa-cog"></span>
                    <?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_SETTINGS'); ?>
                </a>
            </div>
        </div>
        <?php } ?>
    </div>
    
	<div style="clear: both;"></div>
  
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
	var grid = document.querySelector('#secretaryQuickIcons .grid');
	if (grid) {
		new Masonry(grid, { itemSelector: '.grid-item', columnWidth: 150, fitWidth: true });
	}
});
</script>
