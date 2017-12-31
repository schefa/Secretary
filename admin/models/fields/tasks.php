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
defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

class JFormFieldTasks extends JFormFieldList
{
	
	protected $type = 'tasks';
	
	protected function getOptions()
	{
		$app = JFactory::getApplication();
		$db	 = \Secretary\Database::getDBO();
		$pid = $app->input->getInt('pid','');
		
		if(empty($pid)) {
			$id = $app->input->getInt('id','');
			$pid = Secretary\Database::getQuery('tasks', (int) $id,'id',$db->qn('projectID'),'loadResult');
		}
		
        $user	= JFactory::getUser(); 
		$tasks	= array();
	 	$business	= Secretary\Application::company();
		
		$query = $db->getQuery(true)
				->select($db->qn(array("id","title","parentID","level","state")))
				->select("id AS value,title AS text")
				->from($db->quoteName("#__secretary_tasks"))
				->where($db->qn('business').'='. intval($business['id']));
		
		if(!empty($pid))
			$query->where($db->quoteName("projectID")." =".intval($pid));
		
		$db->setQuery($query);
		$tasks = $db->loadObjectList();
		
		$tasks = \Secretary\Helpers\Times::reorderTasks( $tasks ); 
		
		for ($i = 0, $n = count($tasks); $i < $n; $i++) {
			if($user->authorise('core.show','com_secretary.task.'.$tasks[$i]->id) 
			|| $user->authorise('core.show.other','com_secretary.task.'.$tasks[$i]->id))
			{
				$tasks[$i]->text = str_repeat('- ', $tasks[$i]->level ) . JText::_($tasks[$i]->text);
			} else {
				unset($tasks[$i]);	
			}
		} 
		
		array_unshift(	$tasks , JText::_('COM_SECRETARY_SELECT_OPTION') );
    	return $tasks;
	}
}
