<?php

defined('_JEXEC') or die;

$user = Secretary\Joomla::getUser();

$features = array(
    'documents' => array(true, \Joomla\CMS\Language\Text::_('COM_SECRETARY_DOCUMENTS')),
    'subjects' => array(true, \Joomla\CMS\Language\Text::_('COM_SECRETARY_SUBJECTS')),
    'products' => array(true, \Joomla\CMS\Language\Text::_('COM_SECRETARY_PRODUCTS')),
    'times' => array(true, \Joomla\CMS\Language\Text::_('COM_SECRETARY_TIMES')),
);

?>


<table class="table table-hover" id="documentsList">
    <thead>
        <tr>

            <th width="1%" class="nowrap center hidden-phone">
                <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', 'JGRID_HEADING_ID', 'a.extension_id', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
            </th>
            <th class='left'>
                <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', 'COM_SECRETARY_NAME', 'a.name', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
            </th>
            <th class='left'>
                <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', 'COM_SECRETARY_VERSION', 'a.version', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
            </th>
            <th class='left'>
                <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', 'COM_SECRETARY_AUTHOR', 'a.author', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
            </th>
            <th class='left'>
                <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', 'COM_SECRETARY_STATUS', 'a.enabled', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
            </th>

        </tr>
    </thead>
    <tbody>
        <?php foreach ($this->items as $i => $item)
        {
            ?>

            <?php $plugin = $this->checkPlugin($item->name); ?>
            <?php $id = (!empty($plugin->extension_id)) ? $plugin->extension_id : 0; ?>
            <tr class="row<?php echo $i % 2; ?>">

                <td class="center hidden-phone">
                    <?php echo (int) $id; ?>
                </td>

                <td>

                    <a class="hasTooltip" data-original-title="<?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_CLICK_TO_EDIT'); ?>"
                        href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_plugins&task=plugin.edit&extension_id=' . (int) $id); ?>">
                        <?php echo $item->name; ?>
                    </a>

                </td>

                <td class="left">
                    <?php echo $item->version; ?>
                </td>
                <td class="left">
                    <?php echo $item->author; ?>
                </td>
                <td class="left">
                    <?php if (empty($id))
                    {
                        ?>
                        <span class="btn btn-danger">
                            <?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_NOT_INSTALLED'); ?>
                        </span>
                    <?php }
                    else
                    {
                        ?>
                        <?php echo \Joomla\CMS\HTML\HTMLHelper::_('jgrid.published', $plugin->enabled, $i, 'plugins.'); ?>
                    <?php } ?>
                </td>

            </tr>
        <?php } ?>
    </tbody>

</table>


<hr />

<h3 class="documents-title">
    <?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_FEATURES'); ?>
    <?php echo '<a class="btn btn-install-features" href="' . \Joomla\CMS\Router\Route::_("index.php?option=com_secretary&task=item.add&extension=plugins") . '">' . \Joomla\CMS\Language\Text::sprintf('COM_SECRETARY_INSTALL_THIS', \Joomla\CMS\Language\Text::_('COM_SECRETARY_FEATURES')) . '</a>';
    ?>
</h3>

<ul class="secretary-features-list">
    <?php foreach ($features as $i => $item)
    {
        ?>
        <li
            class="<?php if ($item[0] === true)
            {
                echo 'yes';
                $icon = '<i class="fa fa-check-circle-o"></i>';
                   }
                   else
                   {
                                   echo 'no';
                                   $icon = '<i class="fa fa-times-circle-o"></i>';
                   } ?>">

            <?php echo $icon; ?>
            <?php echo $item[1]; ?>
        </li>
    <?php } ?>
</ul>