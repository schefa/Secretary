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
defined('JPATH_BASE') or die;


JFormHelper::loadFieldClass('list');

class JFormFieldSecretarySearch extends JFormFieldList
{
	
	protected $type = 'secretarysearch';
	
	public function getInput( )
	{
		$jinput		= JFactory::getApplication()->input;
		$html		= array();
		$extension	= "";
		$module		= "";
		$value		= "";
	
		if (!empty($this->element['extension'])) {
			$extension = (string) $this->element['extension'];
		}
		
		// Placeholder value instead of id
		if($this->value > 0) {
			$value = $this->getValue($extension, $this->value);
			JFactory::getDocument()->addScriptDeclaration('
				jQuery(document).ready(function(){
					Secretary.Search.drawBlockInput("input.search-'.$extension.'","'.$value.'");
				});
			');
		}
		
		// Extension (important for search in specific databases where needed)
		if (!empty($this->element['module'])) {
			$module = (string) $this->element['module'];
		} elseif ($mod	= $jinput->getCmd('module')) {
			$module =  $mod;
		} 
		if(!empty($module)) $module = 'data-extension="'.$module.'"';
		
		// If value is set, then make a blocked input
		$html[] = '<input type="text" class="search-'.$extension.' search-block-input" value="'. $value .'" '.$module.' />';
		$html[] = '<input id="'.$this->id.'" type="hidden" value="'. $this->value.'" name="'.$this->name.'">';
		
		// Budget for documents connection
		if($extension == 'documents') $html[] = '<div class="secretary-budget"><div class="budget"></div></div>';
	
		return implode('', $html);
		
	}
	
	private function getValue( $extension, $value )
	{
		switch ($extension) {
			
			case 'documents' :
				$db = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->select(" d.id, d.nr, d.title, d.total, d.subtotal, d.taxtotal, d.taxtype, d.currency, d.rabatt, d.subjectid, d.created")
					->from($db->quoteName('#__secretary_documents','d'))
					->select('c.title as category')
					->leftJoin($db->qn('#__secretary_folders','c').' ON c.id = d.catid')
					->where('d.id = '. intval($value));
				$db->setQuery($query);
				$object = $db->loadObject();
				
				$row	= array();
				\Secretary\Helpers\Documents::getDocumentsPrepareRow( $row, $object );
				$result	= $row['value'];
				
				echo '<script>var budget = '.json_encode($row).';</script>';
				
				break;
				
			case 'locations' : $result = Secretary\Database::getQuery($extension, $value,'id','title','loadResult');break;
			default: $result = ""; break;	
			
		}
		
		return $result;
	}
	
}