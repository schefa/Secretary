<?php
/**
 * @version     3.0.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      GNU General Public License version 2 or later.
 */

namespace Secretary\HTML;

require_once SECRETARY_ADMIN_PATH .'/application/HTML.php';

use JText; 

// No direct access
defined('_JEXEC') or die;
 
class Configuration
{
    
    public static function getRulesField( $section , $assetId = NULL )
    {
        // Initialise some field attributes.
        $name		= 'rules_'.$section;
        $component	= 'com_secretary';
        
        // Get the actions for the asset.
        $actions	= \JAccess::getActions($component, $section);
        $assetRules = \Secretary\Helpers\Access::getAssetRules($assetId);
        
        // Get the available user groups.
        $groups = self::_getUserGroups();
        
        // Prepare output
        $html = array();
        $html[] = '<div class="secretary-desc margin-bottom">' . JText::_('JLIB_RULES_SETTINGS_DESC') . '</div>';
        
        // Begin tabs
        $html[] = '<div class="tabbable tabs-left">';
        $html[] =	'<ul class="nav nav-tabs">';
        
        foreach ($groups as $group)
        {
            // Initial Active Tab
            $active = "";
            
            if ($group->value == 1)
            {
                $active = "active";
            }
            
            $html[] = '<li class="' . $active . '">';
            $html[] = '<a href="#permission-'. $section . $group->value . '" data-toggle="tab">';
            $html[] = str_repeat('<span class="level">&ndash;</span> ', $curLevel = $group->level) . $group->text;
            $html[] = '</a>';
            $html[] = '</li>';
        }
        
        $html[] = '</ul>';
        
        $html[] = '<div class="tab-content">';
        
        // Start a row for each user group.
        foreach ($groups as $group)
        {
            // Initial Active Pane
            $active = "";
            
            if ($group->value == 1) $active = " active";
            
            $html[] = '<div class="tab-pane' . $active . '" id="permission-' . $section . $group->value . '">';
            $html[] = '<table class="table table-striped">';
            $html[] = '<thead>';
            $html[] = '<tr>';
            
            $html[] = '<th class="actions" id="actions-th' . $section . $group->value . '">';
            $html[] = '<span class="acl-action">' . JText::_('JLIB_RULES_ACTION') . '</span>';
            $html[] = '</th>';
            
            $html[] = '<th class="settings" id="settings-th' . $section . $group->value . '">';
            $html[] = '<span class="acl-action">' . JText::_('JLIB_RULES_SELECT_SETTING') . '</span>';
            $html[] = '</th>';
            
            // The calculated setting is not shown for the root group of global configuration.
            $canCalculateSettings = ($group->parent_id || !empty($component));
            
            if ($canCalculateSettings)
            {
                $html[] = '<th id="aclactionth' . $group->value . '">';
                $html[] = '<span class="acl-action">' . JText::_('JLIB_RULES_CALCULATED_SETTING') . '</span>';
                $html[] = '</th>';
            }
            
            $html[] = '</tr>';
            $html[] = '</thead>';
            $html[] = '<tbody>';
            
            if(!empty($actions)) :
            // Loop Actions
            foreach ($actions as $action) :
            
            $idName =  $name .'_' . $action->name . '_' . $group->value ;
            $assetRule = \Secretary\Helpers\Access::checkAllow($assetId, $action->name, $group->value);
            $inheritedRule = \JAccess::checkGroup($group->value, $action->name, $assetId);
            
            $html[] = '<tr>';
            $html[] = '<td headers="actions-th' . $section . $group->value . '">';
            $html[] = '<label for="jform_'. $idName . '" class="hasTooltip" title="'
            . htmlspecialchars(JText::_($action->title) . ' ' . JText::_($action->description), ENT_COMPAT, 'UTF-8') . '">';
            $html[] = JText::_($action->title);
            $html[] = '</label>';
            $html[] = '</td>';
            
            
            $html[] = '<td headers="settings-th' . $group->value . '">';
            
            $html[] = '<select class="input-small"'
                . ' data-section="'.$section.'"'
                    . ' data-group="'. $group->value .'"'
                        . ' data-action="'. $action->name .'"'
                            // . ' name="jform[rules]['. $section .'][' . $action->name . '][' . $group->value . ']"'
            . ' id="jform_'. $idName . '" >';
            $html[] =	'<option value="2"' . ($assetRule === null ? ' selected="selected"' : '') . '>'
                . JText::_(empty($group->parent_id) && empty($component) ? 'JLIB_RULES_NOT_SET' : 'JLIB_RULES_INHERITED') . '</option>';
            $html[] =	'<option value="0"' . ($assetRule === false ? ' selected="selected"' : '') . '>' . JText::_('JLIB_RULES_DENIED') . '</option>';
            $html[] =	'<option value="1"' . ($assetRule === true ? ' selected="selected"' : '') . '>' . JText::_('JLIB_RULES_ALLOWED') . '</option>';
            $html[] = '</select>&#160;';
            
            $html[] = '<span id="'.$idName.'"></span>';
            
            // If this asset's rule is allowed, but the inherited rule is deny, we have a conflict.
            if (($assetRule === true) && ($inheritedRule === false)) $html[] = JText::_('JLIB_RULES_CONFLICT');
            
            $html[] = '</td>';
            
            // Build the Calculated Settings column.
            // The inherited settings column is not displayed for the root group in global configuration.
            if ($canCalculateSettings)
            {
                $html[] = '<td headers="aclactionth' . $group->value . '">';
                
                // This is where we show the current effective settings considering currrent group, path and cascade.
                // Check whether this is a component or global. Change the text slightly.
                
                if (\JAccess::checkGroup($group->value, 'core.admin', $assetId) !== true)
                {
                    if ($inheritedRule === null)
                    {
                        $html[] = '<span class="label label-important">' . JText::_('JLIB_RULES_NOT_ALLOWED') . '</span>';
                    }
                    elseif ($inheritedRule === true)
                    {
                        $html[] = '<span class="label label-success">' . JText::_('JLIB_RULES_ALLOWED') . '</span>';
                    }
                    elseif ($inheritedRule === false)
                    {
                        if ($assetRule === false)
                        {
                            $html[] = '<span class="label label-important">' . JText::_('JLIB_RULES_NOT_ALLOWED') . '</span>';
                        }
                        else
                        {
                            $html[] = '<span class="label"><i class="icon-lock icon-white"></i> ' . JText::_('JLIB_RULES_NOT_ALLOWED_LOCKED')
                            . '</span>';
                        }
                    }
                }
                elseif (!empty($component))
                {
                    $html[] = '<span class="label label-success"><i class="icon-lock icon-white"></i> ' . JText::_('JLIB_RULES_ALLOWED_ADMIN')
                    . '</span>';
                }
                else
                {
                    // Special handling for  groups that have global admin because they can't  be denied.
                    // The admin rights can be changed.
                    if ($action->name === 'core.admin')
                    {
                        $html[] = '<span class="label label-success">' . JText::_('JLIB_RULES_ALLOWED') . '</span>';
                    }
                    elseif ($inheritedRule === false)
                    {
                        // Other actions cannot be changed.
                        $html[] = '<span class="label label-important"><i class="icon-lock icon-white"></i> '
                            . JText::_('JLIB_RULES_NOT_ALLOWED_ADMIN_CONFLICT') . '</span>';
                    }
                    else
                    {
                        $html[] = '<span class="label label-success"><i class="icon-lock icon-white"></i> ' . JText::_('JLIB_RULES_ALLOWED_ADMIN')
                        . '</span>';
                    }
                }
                
                $html[] = '</td>';
            }
            
            $html[] = '</tr>';
            endforeach;
            endif;
            
            $html[] = '</tbody>';
            $html[] = '</table></div>';
        }
        
        $html[] = '<input type="hidden" name="jform[rules]['. $section .'][assetid]" value="'. $assetId .'" />';
        $html[] = '</div></div>';
        
        return implode("\n", $html);
    }
    
    protected static function _getUserGroups()
    {
        $db = \Secretary\Database::getDBO();
        $query = $db->getQuery(true);
        $query->select('a.id AS value, a.title AS text, COUNT(DISTINCT b.id) AS level, a.parent_id');
        $query->from($db->qn('#__usergroups','a'));
        $query->join('LEFT', $db->qn('#__usergroups') . ' AS b ON a.lft > b.lft AND a.rgt < b.rgt');
        $query->group('a.id, a.title, a.lft, a.rgt, a.parent_id');
        $query->order('a.lft ASC');
        $db->setQuery($query);
        $options = $db->loadObjectList();
        return $options;
    }
    
    
}
