<?php
/**
 * @version     3.0.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      GNU General Public License version 2 or later.
 */
use Secretary\Debug;
 
// No direct access

defined('_JEXEC') or die;

class SecretaryTableDocument extends JTable
{
    
    /**
     * Class constructor
     *
     * @param mixed $db
     */
    public function __construct(&$db) {
        parent::__construct('#__secretary_documents', 'id', $db);
    }
	
    /**
     * {@inheritDoc}
     * @see \Joomla\CMS\Table\Table::bind()
     */
    public function bind($array, $ignore = '')
	{
        if (!JFactory::getUser()->authorise('core.admin', 'com_secretary.document.' . $array['id'])) {
            $actions = JFactory::getACL()->getActions('com_secretary', 'document');
            $default_actions = JFactory::getACL()->getAssetRules('com_secretary.document.' . $array['id'])->getData();
            $array_jaccess = array();
            foreach ($actions as $action) {
				if(isset($default_actions[$action->name]))
                	$array_jaccess[$action->name] = $default_actions[$action->name];
            }
            $array['rules'] = \Secretary\Helpers\Access::JAccessRulestoArray($array_jaccess);
        }
		
        //Bind the rules for ACL where supported.
        if (isset($array['rules']) && is_array($array['rules'])) {
            $array['rules'] = \Secretary\Helpers\Access::JAccessRulestoArray($array['rules']);
            $this->setRules($array['rules']);
        }

        return parent::bind($array, $ignore);
    }
	
    /**
     * Prepare data before saving
     * 
     * @param array $data
     */
    public function prepareStore(&$data)
    {
        
		$business = Secretary\Application::company();
		$data['business']	= $business['id'];
		$data['created_by']	= (!empty($this->created_by)) ? $this->created_by : JFactory::getUser()->id;
		// Override items in JSON
		$itemsLoop	= count($data['items']);
		$itemsRow	= array();
		$subtotalRow= array(0 => 0);
		$totalRow	= array(0 => 0);
		$position	= 0;
		
		// bei anfänglich leerer catid kann es sein, dass der Produktverbrauch vergessen wurde (aber: was ist, wenn absichtlich?), dennoch:
		if(empty($data['id']) && !empty($data['catid']) && ($data['fields']['pUsage'] == 0)) {
			$categoryP = Secretary\Database::getQuery('folders', $data['catid'], 'id', 'fields', 'loadResult');
			$categoryP	= json_decode($categoryP, true);
			$data['fields']['pUsage'] = $categoryP['pUsage'];
		}
		
		// Bereinigen aller alten Produktinformationen anhand des Entry
		if( isset($data['fields']['pUsage']) && ( $data['fields']['pUsage'] == 1 || $data['fields']['pUsage'] == 2 ) )
			\Secretary\Helpers\Products::deleteOldProducts( $data['createdEntry'] , $data['fields']['pUsage'] );
		
		if(!empty($data['items']) && is_array($data['items'])) {
    		
    		foreach($data['items'] AS $product)
    		{
			if(isset($product)) {
			    
			    if(isset($product['id']) && $product['id'] > 0) {

			        $itemsRow[$position]['id']		= intval($product['id']);
			        $itemsRow[$position]['subjectid'] = intval($product['subjectid']);
			        $itemsRow[$position]['title']	= htmlentities(trim($product['title']));
			        $itemsRow[$position]['created']	= htmlentities($product['created']);
			        $itemsRow[$position]['deadline']= htmlentities($product['deadline']);
			         
			        $result = Secretary\Database::getQuery('documents', $itemsRow[$position]['id']);
			        $document	= array();
			        \Secretary\Helpers\Documents::getDocumentsPrepareRow( $document, $result );
			         
			        $itemsRow[$position]['nr']      = htmlentities($document['nr']);
			        $itemsRow[$position]['subtotal'] = floatval( $document['subtotal'] );
			        $itemsRow[$position]['total'] 	= floatval( $document['total'] ) ;
			        $itemsRow[$position]['tax'] 	= floatval( $document['tax'] ) ;

			        $subtotalRow[$position]	+= $itemsRow[$position]['subtotal'];
			        $totalRow[$position]	+= $itemsRow[$position]['total'];
			        
			    } else {
    			        
    				$itemsRow[$position]['title']		= htmlentities(trim($product['title']));
    				$itemsRow[$position]['description']	= htmlentities($product['description']);
    				$itemsRow[$position]['pno']			= htmlentities($product['pno']);
    				$itemsRow[$position]['quantity'] 	= ($product['quantity'] > 0) ? floatval( $product['quantity'] ) : "";			
    				
    				$itemsRow[$position]['entity'] 		= htmlentities($product['entity']);
    				$itemsRow[$position]['price'] 		= ($product['price'] > 0) ? floatval( $product['price'] ) : "";
    				$itemsRow[$position]['total'] 		= ($product['total'] != 0) ? floatval( $product['total'] ) : "";
    				$itemsRow[$position]['taxRate'] 	= ($product['total'] != 0) ? floatval( $product['taxRate'] ) : "";
    				
    				if ( $product['quantity'] > 0 && $product['total'] == 0 )
    					$itemsRow[$position]['total'] = $itemsRow[$position]['quantity'] * $itemsRow[$position]['price'];
    					
    				if(empty($itemsRow[$position]['taxRate']) || $itemsRow[$position]['taxRate'] < 0 ) $itemsRow[$position]['taxRate'] = 0;
    				
    				$total = $itemsRow[$position]['total'];
				
    				if($data['taxtype'] == 1) {
    					// Mwst inklusiv
    					if($total != 0) $totalRow[]	+= $total;
    					$product['subtotal'] = ( $total / ( 1 + ($itemsRow[$position]['taxRate'] / 100)) );
    					$subtotalRow[] += round($product['subtotal'],4);
    				} elseif($data['taxtype'] == 2) {
    					// Mwst exklusiv
    					$product['subtotal']	= $total;
    					if($total != 0) $subtotalRow[] += $product['subtotal'];
    					$totalRow[]	+= ( $itemsRow[$position]['total'] * ( 1 + ($itemsRow[$position]['taxRate'] / 100)) );
    				} else {
    					$product['subtotal']	= $total;
    					if($total != 0) $subtotalRow[]	+= $product['subtotal'];
    					$totalRow[]	+= $product['subtotal'];
    				}
    				
    				// Update product
    				if((!empty($itemsRow[$position]['title'])) && (( $data['fields']['pUsage'] == 1) || ( $data['fields']['pUsage'] == 2 )) ) {
    					$ok = \Secretary\Helpers\Products::addNewProduct($data['createdEntry'],$position,$data['catid'],$product,$data['fields']['pUsage']);
    				}
			    }
				$position++;
			}
    		}
    		
    		// Make sure no redudant data documents if($data['fields']['pUsage'] == 0) unset($data['fields']['pUsage']);
    		
    		// Combination der Produkte
    		$data['items'] = json_encode($itemsRow);
    		
    		$taxTotal = 0;
    		if(!empty($data['taxtotal'])) {
    			$taxTotal = array_sum($data['taxtotal']);
    			$data['taxtotal']	= json_encode( $data['taxtotal'], true );
    		} else {
    			$data['taxtotal']	= FALSE;	
    		}
    		
    		switch ($data['taxtype']) {
    			case 0 : break;
    			case 1 : // Mwst inklusiv
    					$data['total']	= array_sum($totalRow);
    					$data['subtotal']	= array_sum($subtotalRow);
    					
    					if(!empty($data['rabatt'])) {
    						$data['rabatt'] = $data['rabatt'];
    						$data['total'] = $data['total'] - $data['rabatt'];
    						$data['subtotal'] = $data['total'] - $taxTotal;
    					}
    					break;
    			case 2 : // Mwst exklusiv
    					$data['subtotal']	= array_sum($subtotalRow);
    					
    					if(!empty($data['rabatt'])) {
    						$data['rabatt'] = $data['rabatt'];
    						$data['subtotal'] = $data['subtotal'] - $data['rabatt'];
    					}
    					
    					$data['total']	= $data['subtotal'] + $taxTotal;
    					break;
    		}
    	
		}
		
		// Zeilenumbrüche zuerst entfernen, dann als neue speichern
		$data['title']	= Secretary\Utilities\Text::ripTags( $data['title'] );
		$data['text']	= Secretary\Utilities\Text::ripTags( $data['text'] );
		
		// Verbinden für Druckausgaben etc.
		ksort($data['subject']);
		if(!empty($data['subject']) && is_array($data['subject'])) {
    		$data['subject'] = \Secretary\Helpers\Subjects::cleanSubject($data['subject']);
    		if(empty($data['subjectid']) && !empty($data['subject'][1]) ) $data['subjectid'] = \Secretary\Helpers\Subjects::addNewSubject( $data['subject'] , $data['created']);
    		$data['subject'] = json_encode($data['subject']);
		}
		
		// Fields
		if(is_array($data['fields'])) {
		    
    		// Wiederkehrende Einträge
    		if(isset($data['repetition'])) {
    			\Secretary\Helpers\Times::cleanRepetitions($data['id'], $data['created']);
    			if($data['repetition']['check'] == 1 && $data['repetition']['zyklus'] > 0) {
    				\Secretary\Helpers\Times::saveRepetition("documents", $data['id'], $data['created'], $data['repetition'] );
    				$data['fields']['repetition'] = $data['repetition'];
    			}
    		}	
    		
    		if(isset($data['fields']['message']['template']) && $data['fields']['message']['template'] > 0 && empty($data['fields']['message']['text'])) {
    		
    			$templateInfoFields = \Secretary\Helpers\Templates::getTemplateInfoFields($data);
    			$emailTemplate	= \Secretary\Helpers\Templates::getTemplate($data['fields']['message']['template']);
    			$data['fields']['message']['subject'] = \Secretary\Helpers\Templates::transformText( $emailTemplate->title, array('subject'=>$data['subjectid']), $templateInfoFields ); 
    			$data['fields']['message']['text'] = \Secretary\Helpers\Templates::transformText( $emailTemplate->text,array('subject'=>$data['subjectid']), $templateInfoFields ); 
    		
    		}
    		
    		// Data Fields
    		if(empty($data['fields']['message']) && isset($this->fields)) {
    			if($fields = json_decode($this->fields,true)) {
    				$data['fields']['message'] = $fields['message'];
    			}
    		}
    		$data['fields']	= \Secretary\Helpers\Items::saveFields($data['fields']);
    		
		    
		}
		
    }
	
    /**
     * {@inheritDoc}
     * @see \Joomla\CMS\Table\Table::check()
     */
    public function check()
	{	
				
        if ($this->id > 0) {
			$buchung = Secretary\Database::getQuery('accounting',$this->id,'entry_id');	
			if(!empty($buchung->id)) {
				$this->setError(JText::sprintf('COM_SECRETARY_ACCOUNTS_SAVE_FAILED_RECORD_EXISTS', $buchung->id));
				return false;
			}
        }
		
		// No Contact
		$subject = json_decode($this->subject);
		if (strlen($subject[1]) < 1) {
			$errTitle = JText::_('COM_SECRETARY_SUBJECT');
			$this->setError(JText::sprintf('COM_SECRETARY_ERROR_CHECK_THIS', $errTitle));
			return false;
		}
		
		// Test Double Documents
        if (property_exists($this, 'nr')) {
			$NrExists = \Secretary\Helpers\Documents::getDoubleCategoryNumber($this->nr, $this->catid, $this->id);
			if (!empty($NrExists)) {
				$this->setError(JText::_('COM_SECRETARY_ERROR_DOUBLE_NO'));
				return false;
			}
		}
		
		// Negative totals
		if($this->rabatt < 0) { 
			$this->setError(JText::sprintf('COM_SECRETARY_ERROR_CHECK_THIS', JText::_('COM_SECRETARY_RABATT')));
			return false;
		}
		
		// Paid is higher than total
		if(!empty($this->paid) && ($this->paid > $this->total)) {
			$errTitle = JText::_('COM_SECRETARY_PAIDAMOUNT');
			$this->setError(JText::sprintf('COM_SECRETARY_ERROR_CHECK_THIS', $errTitle));
			return false;
		} elseif( $this->paid < 0) {
			$this->paid = 0;
		}
		
        return true;
    }
    
    /**
     * Delete and save activity
     *
     * {@inheritDoc}
     * @see \Joomla\CMS\Table\Table::delete()
     */
    public function delete($pk = null)
	{
		$datas = Secretary\Database::getQuery('documents',$pk);
		
        $this->load($pk);
		
		if(isset($this->accounting_id) && $this->accounting_id > 0) {
        	$this->setError(JText::sprintf('Delete not possible. Accounting entry exists: %s.', $this->id));
			return false;
		}
		
        $result = parent::delete($pk);
        if ($result) {
			
			JFactory::getApplication()->enqueueMessage(JText::_('COM_SECRETARY_DOCUMENT_DELETED_STATUS_UPLOADS'), 'warning');
				
			// Activity
			\Secretary\Helpers\Activity::set('documents', 'deleted', $datas->catid, $pk, JFactory::getUser()->id);
			
			// Delete Products
			$datas->fields = json_decode($datas->fields, true);
			if($datas->createdEntry && isset($datas->fields['pUsage'])) {
				\Secretary\Helpers\Products::DeleteOldProducts( $datas->createdEntry, $datas->fields['pUsage']);
			}
			
			$repetition = JTable::getInstance('Repetition', 'SecretaryTable');
			$repetition->deleteRepetition($pk);
		
        }
        return $result;
    }
	
    /**
     * {@inheritDoc}
     * @see \Joomla\CMS\Table\Table::_getAssetName()
     */
    protected function _getAssetName() {
        $k = $this->_tbl_key;
        return 'com_secretary.document.' . (int) $this->$k;
    }
	
    /**
     * {@inheritDoc}
     * @see \Joomla\CMS\Table\Table::_getAssetParentId()
     */
	protected function _getAssetParentId(JTable $table = null, $id = null)
	{
		$asset = self::getInstance('Asset');
		$asset->loadByName('com_secretary.document');
		return $asset->id;
	}
	
}
