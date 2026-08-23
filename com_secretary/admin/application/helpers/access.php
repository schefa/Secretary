<?php

namespace Secretary\Helpers;

// No direct access
use Secretary\Joomla;
use Joomla\CMS\Helper\ContentHelper;

defined('_JEXEC') or die;

abstract class Access
{
    private static $storedAssetRules = array();
    private static $actions = array();
    private static $missing = array();

    public static function checkAdmin()
    {
        $result = \Secretary\Joomla::getUser()->get('isRoot');
        
        return (bool) $result;
    }

    public static function getActions($section = 'component', $id = null)
    {
        if (!isset(self::$actions[$section]))
		{
            if (isset($section))
            {
                $section = \Secretary\Application::getSingularSection($section);
            }

            $user = Joomla::getUser();
            $result = new \Joomla\CMS\Object\CMSObject;
            $id = (!empty($id)) ? ('.' . $id) : '';
            $permissions = [
                // Views guard their display with canDo->get('core.show'); without
                // it in this list the value stays null and the view throws.
                'core.show',
                'core.admin',
                'core.manage',
                'core.create',
                'core.delete',
                'core.edit',
                'core.edit.state',
                'core.edit.own',
                'core.upload'
            ];

            foreach ($permissions as $actionName)
			{
                $permission = $user->authorise(
                    $actionName,
                    'com_secretary.' . $section . '.' . $id
                );

                $result->set($actionName, $permission);

                if (!$permission)
				{
                    self::$missing[$section][] = $actionName;
                }
            }

            self::$actions[$section] = $result;
        }

        return self::$actions[$section];
    }

    /**
     * Checks if current user has limited access and displays a message
     * 
     * @param string $section the current section view
     * @return NULL|mixed
     */
    public static function getAccessMissingMsg($section = 'component')
    {
        $params = \Secretary\Application::parameters();
        $accessMissingNote = boolval($params->get('accessMissingNote'));

        if (!$accessMissingNote)
        {
            return NULL;
        }

        $section = \Secretary\Application::getSingularSection($section);

        if (isset(self::$missing[$section]))
		{
            return '<div class="secretary-access-warning">' . \Joomla\CMS\Language\Text::_('COM_SECRETARY_ACCESS_LIMITED_ACCESS') . "</div>";
        }
    }

    public static function show($section, $id = 0, $created_by = '')
    {
        $user = \Secretary\Joomla::getUser();
        $section = (!empty($section)) ? ('.' . $section) : '';

        if (\Secretary\Helpers\Access::checkAdmin())
        {
            return true;
        }


        if (
            $id > 0 && ($created_by > 0)
            && (($created_by === $user->id || $user->authorise('core.show.other', 'com_secretary' . $section . '.' . $id) || $user->authorise('core.show.other', 'com_secretary' . $section)))
        )
		{
            return true;
        }
        elseif ($id < 1 && $user->authorise('core.show', 'com_secretary' . $section))
		{
            return true;
        }
        
        return false;
    }

    public static function edit($section = '', $id = '', $created_by = '')
    {
        $test = false;
        $user = \Joomla\CMS\Factory::getUser();
        $section = (!empty($section)) ? ('.' . $section) : '';

        // Edit
        if (isset($id) && isset($created_by))
		{
            if (
                ($user->id == $created_by && $user->authorise('core.edit.own', 'com_secretary' . $section))
                || $user->authorise('core.edit', 'com_secretary' . $section)
            )
			{
                $test = true;
            }
        }
        // Create
        else
		{
            if ($user->authorise('core.create', 'com_secretary' . $section))
			{
                $test = true;
            }
        }

        if (!$user->authorise('core.show', 'com_secretary' . $section))
        {
            return false;
        }

        return $test;
    }

    /**
     * Whether the current user may view a document's non-HTML output (PDF, raw preview,
     * e-invoice, ...). Shared by views/document/view.pdf.php, view.raw.php and
     * view.xrechnung.php so a future ACL fix only needs to happen in one place.
     *
     * @param object $item document item with id/created_by/subjectid
     * @param string $layout current request layout ('edit' grants access via edit permission)
     * @return bool
     */
    public static function documentExportAllowed($item, $layout)
    {
        $section = 'document';

        if ($layout == 'edit' && true === self::edit($section, $item->id, $item->created_by))
		{
            return true;
        }

        if ($layout != 'edit')
		{
            if (false !== self::show($section, $item->id, $item->created_by))
			{
                return true;
            }
            $subjectUserId = \Secretary\Database::getQuery('subjects', $item->subjectid, 'id', 'created_by', 'loadResult');
            
            if (false !== self::show($section, $item->id, $subjectUserId))
			{
                return true;
            }
        }

        return false;
    }

    public static function allowEdit($section, $data = array(), $key = 'id')
    {

        $recordId = (int) isset($data[$key]) ? $data[$key] : '';
        $user = \Secretary\Joomla::getUser();
        $asset = 'com_secretary.' . $section;

        if ($user->authorise('core.edit', $asset))
		{
            return true;
        }

        if ($user->authorise('core.edit.own', $asset))
		{
            $ownerId = (int) isset($data['created_by']) ? $data['created_by'] : 0;

            if (empty($ownerId) && $recordId)
			{
                $table = \Secretary\Application::$sections[$section];
                $record = \Secretary\Database::getQuery($table, $recordId);

                if (empty($record))
                {
                    return false;
                }

                $ownerId = (int) isset($record->created_by) ? $record->created_by : 0;
            }

            if ($ownerId == $user->id)
            {
                return true;
            }
        }

        return $user->authorise('core.edit', 'com_secretary');
    }

    /**
     * Test if user can delete a record
     * 
     * @param object $record
     * @param string $view
     * @return boolean
     */
    public static function canDelete($record, $view)
    {
        $user = \Secretary\Joomla::getUser();
        
        if (!empty($record->id))
		{
            return $user->authorise('core.delete', 'com_secretary.' . $view . '.' . (int) $record->id);
        }
        
        return $user->authorise('core.delete', 'com_secretary.' . $view);
    }

    public static function JAccessRulestoArray($jaccessrules)
    {
        $rules = array();
        
        foreach ($jaccessrules as $action => $jaccess)
		{
            $actions = array();
            
            foreach ($jaccess as $group => $allow)
			{
                if (is_string($allow))
				{
                    $allow = intval($allow);

                    if ($allow < 2)
                    {
                        $actions[$group] = ((int) $allow);
                    }
                }
                elseif (is_bool($allow))
				{
                    $actions[$group] = ((int) $allow);
                }
            }
            $rules[$action] = $actions;
        }
        
        return $rules;
    }

    /**
     * Returns rules for an asset item
     * 
     * @param number $assetId
     */
    public static function getAssetRules($assetId)
    {
        $db = \Secretary\Database::getDBO();
        $query = $db->getQuery(true);

        $query->select('rules');
        $query->from($db->qn('#__assets'));
        $query->where($db->qn('id') . '=' . intval($assetId));
        $db->setQuery($query);
        $list = $db->loadResult();

        if (!empty($list) && !isset(self::$storedAssetRules[$assetId]))
		{
            $tmp = (array) json_decode($list);
            
            foreach ($tmp as $key => $arr)
			{
                foreach ($arr as $key2 => $val)
				{
                    self::$storedAssetRules[$assetId][$key][(int) $key2] = $val;
                }
            }
        }
    }

    /**
     * Test if usergroup can perform the action for an asset
     * 
     * @param string $assetId
     * @param string $actionname
     * @param string $group
     * @return boolean|NULL
     */
    public static function checkAllow($assetId, $actionname, $group)
    {
        if (isset(self::$storedAssetRules[$assetId][$actionname][(int) $group]))
		{
            return (bool) self::$storedAssetRules[$assetId][$actionname][(int) $group];
        }
        
        return null;
    }

    /**
     * Method to restore both assets table and secretary rules for missing entries
     */
    public static function restoreDefaultSectionAssets()
    {
        if (!\Secretary\Joomla::getUser()->authorise('core.admin', 'com_secretary'))
        {
            return false;
        }

        $db = \Secretary\Database::getDBO();

        $sections = \Secretary\Application::$sections;
        unset($sections['system']);
        unset($sections['item']);
        
        foreach ($sections as $singular => $plural)
		{
            $test = FALSE;

            if ($singular == 'component')
            {
                $assetName = 'com_secretary';
            }
            else
            {
                $assetName = 'com_secretary.' . $singular;
            }

            // Check if has entry in assets 
            $query = $db->getQuery(true);
            $db->setQuery('SELECT name FROM #__assets WHERE name = ' . $db->quote($assetName));
            $test = $db->loadResult();

            if (!$test)
			{
                // Get Asset if exists
                $asset = \Joomla\CMS\Table\Table::getInstance('Asset');
                $asset->loadByName($assetName);

                $asset_id = $asset->id;
                $asset->name = $assetName;
                $asset->title = \Joomla\CMS\Language\Text::_('COM_SECRETARY_' . strtoupper($plural));
                $asset->rules = '{}';
                $asset->store();

                // parent id
                if (!($asset_id > 0))
				{
                    \Secretary\Helpers\Access::setParentIdAssets($assetName, $singular);
                }
            }
        }

        // Set Rules for secretary_settings
        \Secretary\Helpers\Access::updateSecretaryRules();
    }

    /**
     * Method to store parent_id and level in assets table
     * 
     * @param string $assetName
     * @param string $section
     * @return boolean
     */
    public static function setParentIdAssets($assetName, $section)
    {
        if (!\Secretary\Joomla::getUser()->authorise('core.admin', 'com_secretary'))
        {
            return false;
        }

        $db = \Secretary\Database::getDBO();
        $db->setQuery('SELECT id FROM #__assets WHERE name LIKE "com_secretary"');
        $parentAssetId = $db->loadResult();
        $asset = \Joomla\CMS\Table\Table::getInstance('Asset');
        $asset->loadByName($assetName);
        $asset->parent_id = ($section == 'component') ? 1 : $parentAssetId;
        $asset->level = ($section == 'component') ? 1 : 2;
        
        if (!$asset->store())
		{
            return false;
        }
        $asset->reset();
        
        return true;
    }

    /**
     * Method to set rules in settings by connecting them to assets table
     * 
     * @return boolean
     */
    public static function updateSecretaryRules()
    {
        if (!\Secretary\Joomla::getUser()->authorise('core.admin', 'com_secretary'))
        {
            return false;
        }

        // Set Rules for secretary_settings
        $db = \Secretary\Database::getDBO();
        $sections = \Secretary\Application::$sections;
        $newRules = array();

        foreach ($sections as $section => $plural)
		{
            if (in_array($section, array('system', 'item')))
			{
                continue;
            }
            $assetName = ($section == 'component') ? 'com_secretary' : 'com_secretary.' . $section;
            $db->setQuery('SELECT id FROM #__assets WHERE name LIKE ' . $db->quote($assetName));
            $section_id = (int) $db->loadResult();
            $newRules[$section] = ($section_id > 0) ? $section_id : 0;
        }
        $newRules = json_encode($newRules, JSON_NUMERIC_CHECK);
        $query = 'UPDATE ' . $db->qn('#__secretary_settings') . ' SET ' . $db->qn('rules') . '=' . $db->quote($newRules) . ' WHERE ' . $db->qn('id') . ' = 1';
        $db->setQuery($query);
        $db->execute();
    }
}
