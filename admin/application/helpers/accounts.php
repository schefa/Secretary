<?php
/**
 * @version     3.2.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
 */

namespace Secretary\Helpers;

use JTable;
use JText;

// No direct access
defined('_JEXEC') or die; 

class Accounts
{
	
	protected static $stateOffen = 26;
	protected static $stateGebucht = 27;
	protected static $stateStorniert = 29;
	
	
	public static function prepareAccounting ( $entry_id, $currency, $accounting, $total, $title = "")
	{
		$business = \Secretary\Application::company();
		$userID = \Secretary\Joomla::getUser()->id;
		$soll = array();
		$haben = array();
		
		if($entry_id < 1)
			return false;
		
		$total_soll = 0;
		foreach($accounting['s'] as $val) {
			$account = \Secretary\Database::getQuery('accounts_system',$val['id']);
			if(!empty($account)) {
				$soll[] = array( $val['id'], $val['sum'] );
				$total_soll += $val['sum'];
		      } else {
				return JText::_('COM_SECRETARY_NOT_FOUND').': '.JText::_('COM_SECRETARY_ACCOUNT');	
			}
		}
		
		$total_haben = 0;
		foreach($accounting['h'] as $val) {
			$account = \Secretary\Database::getQuery('accounts_system',$val['id']);
			if(!empty($account)) {
				$haben[] = array( $val['id'], $val['sum'] );
				$total_haben += $val['sum'];
			} else {
				return JText::_('COM_SECRETARY_NOT_FOUND').': '.JText::_('COM_SECRETARY_ACCOUNT');	
			}
		}
		
		if($total_soll != $total_haben)
		    return JText::_('Summen stimmen nicht überein');
		
		$soll = json_encode($soll);
		$haben = json_encode($haben);
		
		$db   = \Secretary\Database::getDBO();
		$query	= $db->getQuery(true);
		
		$columns = array('created_by','currency','created','title','entry_id','business','soll','haben','total');
		$values = array(intval($userID),$db->quote($currency),$db->quote(date('Y-m-d H:i:s')),$db->quote($title),intval($entry_id),intval($business['id']),$db->quote($soll),$db->quote($haben),$db->escape($total));
			
		try {
			$query->insert($db->quoteName('#__secretary_accounting'))
				->columns($db->quoteName($columns))
				->values(implode(',', $values));
			
			$db->setQuery($query);
			$db->execute();
    		$idded = $db->insertid();
			return $idded;
		} catch(\Exception $e) {
			throw new \Exception( $e->getMessage(), 500);
		}
		return false;
	}
	
	public static function storno ( $ids )
	{
	    $db   = \Secretary\Database::getDBO();
		$done = 0;
		if(!empty($ids)) {
			$table = JTable::getInstance('Accounting','SecretaryTable');
			// loop through entries
			foreach($ids as $id) {
				
				$accounting_entry = \Secretary\Database::getQuery('accounting',$id,'id','*','loadAssoc');	
				if($accounting_entry['state'] == self::$stateGebucht) {
					
					// Storniert
					self::_update('accounting',array('state='. self::$stateStorniert ),array('id='.(int) $id));
					
					// Kopieren & Gegenbuchung erstellen
					unset($accounting_entry['id']);
					$soll = $accounting_entry['soll'];
					$accounting_entry['soll'] = $accounting_entry['haben'];
					$accounting_entry['haben'] = $soll;
					$accounting_entry['state'] = self::$stateStorniert;
					$accounting_entry['created'] = date('Y-m-d H:i:s');
					
					// Bind & Store 
					if (!$table->bind($accounting_entry)) { $this->setError($table->getError()); return false; }
					if (!$table->store()) { $this->setError($table->getError()); return false; }
					$table->reset();
					
					// Auf Konto buchen
					self::bookAccount($accounting_entry);
					
					$done++;
				}
				unset($accounting_entry);
			}
		}
		
		return JText::_('Gemacht: '.$done);
	}
	
	protected static function getOrCreateAccount ( $account_id )
	{
	    $db   = \Secretary\Database::getDBO();
		$db->setQuery('SELECT id,soll,haben,history FROM #__secretary_accounts WHERE kid = '. (int) $account_id .' AND year = '. (int) date('Y') );
		$tkonto = $db->loadObject();
		
		if(empty($tkonto->id)) {
			$business = \Secretary\Application::company();
			$db->setQuery("INSERT INTO #__secretary_accounts (business,year,kid) VALUES (".$business['id'].",".(int) date('Y').",". (int) $account_id .");");
			$db->query();
			$tkonto = self::getOrCreateAccount ( $account_id );
		}
		
		return $tkonto;
	}
	
	protected static function bookAccount ( $accounting_entry )
	{
	    $db   = \Secretary\Database::getDBO();
		if( $soll = json_decode($accounting_entry['soll'],true) ) {
			foreach($soll as $row) {
				
				// Get account
				$tkonto = self::getOrCreateAccount($row[0]);
				
				if(!empty($tkonto->soll)) {
					// History
					$history = (!empty($tkonto->history)) ? json_decode( $tkonto->history, true) : array();
					$history['soll'][] = array( $accounting_entry['id'] => $row[1]);
					
					// Total
					$total = $tkonto->soll + $row[1];
					
					$fields = array( $db->quoteName('soll').'='.$db->escape($total),$db->quoteName('history').'='.$db->quote(json_encode($history)));
					$conditions = array( $db->quoteName('id').'='.((int) $tkonto->id ) );
					
					self::_update('accounts', $fields, $conditions );
				}
			}
		}
		
		if( $haben = json_decode($accounting_entry['haben'],true) ) {
			foreach($haben as $row) {
				
				// Get account
				$tkonto = self::getOrCreateAccount($row[0]);
				
				if(!empty($tkonto->haben)) {
					// History
					$history = (!empty($tkonto->history)) ? json_decode( $tkonto->history, true) : array();
					$history['haben'][] = array( $accounting_entry['id'] => $row[1]);
					
					// Total
					$total = $tkonto->haben + $row[1];
					
					$fields = array( $db->quoteName('haben').'='.$db->escape($total),$db->quoteName('history').'='.$db->quote(json_encode($history)));
					$conditions = array( $db->quoteName('id').'='.((int) $tkonto->id ) );
					
					self::_update('accounts', $fields, $conditions );
				}
			}
		}	
	}
	
	public static function book ( $ids )
	{
		$done = 0;
		if(!empty($ids)) {
			// loop through entries
			foreach($ids as $id) {
				// Get Data
				$accounting_entry = \Secretary\Database::getQuery('accounting',$id,'id','*','loadAssoc');
				if($accounting_entry['state'] == self::$stateOffen) {
					self::bookAccount($accounting_entry);
					self::_update('accounting',array('state = '.(int) self::$stateGebucht), array('id = '.(int) $id));
					$done++;
				}
			}
				
		}
		
		return JText::_('Gemacht: '. $done);
	}
	
	private static function _update($section , $fields, $conditions )
	{
	    $db   = \Secretary\Database::getDBO();
		$query	= $db->getQuery(true);
		$query->update($db->quoteName('#__secretary_'.$db->escape($section)))->set($fields)->where($conditions);
		$db->setQuery($query);
		$result = $db->execute();
	}
	
	public static function getAccounts ( $term )
	{
	    $db   = \Secretary\Database::getDBO();
		$term = $db->quote('%'.str_replace(" ","",htmlentities($term, ENT_QUOTES)).'%');
		
		$a_json		= array();
		$a_json_row	= array();
		
		if ( !isset($term) || strlen($term) <= 2) exit;
		
		$query = $db->getQuery(true);
		$query->select('a.*')
				->from('#__secretary_accounts_system AS a')
				->where(' ( a.nr ='. $term  . ') OR (a.title LIKE '. $term .')  OR (concat(a.nr,a.title) LIKE '. $term .') ')
				// ->where('year='.intval(date('Y')))
				->order('nr');
		$db->setQuery($query);
		$results = $db->loadObjectList();
		
		foreach($results AS $i => $result) {
			
			$a_json_row["id"]		= $result->id;
			$a_json_row["nr"]		= $result->nr;
			$a_json_row["title"]	= $result->title;
			
			array_push($a_json, $a_json_row); 
			
			if($i > 9) { break; }
		}
		
		return json_encode($a_json);
		flush();
		
	}
	
	public static function getBuchungssatz( $item ) {
						
		$total = round($item->subtotal,2) + $item->taxtotal_sum;
		$bookings = array();
		
		if($item->accounting_id > 0) {
			
			$booking = \Secretary\Database::getQuery('accounting', $item->accounting_id);
			
		//	$bookings = array( "s" => $booking->soll
			
		} else {
		    
		    if(!empty($item->accounting)) {
		        
    		    foreach($item->accounting as $account_type => $accounts){
    		       
    		        if(
    		            ($item->productUsage == 2 && $account_type == 'docsHabenTax') 
    		            or 
    		            ($item->productUsage == 1 && $account_type == 'docsSollTax')
    		           ) {
    		            // Unnötige Steuerkonten überspringen
    		            continue;
    		        }
    		        
    		        foreach ($accounts as $account_title => $account) {
    		            
        		        $acc = \Secretary\Database::getQuery('accounts_system', $account,'id','nr,title');
        		        if(empty($acc))
        		            continue;
        		        
        		        if($account_type == 'docsSoll') {
        		            $sum = ($item->productUsage == 2) ? round($item->subtotal,2) : $total;
        		            $bookings["s"][] = array(  $acc->nr .' '. $acc->title, $account, $sum);
        		        } elseif($account_type == 'docsHaben') {
        		            $sum = ($item->productUsage == 1) ? round($item->subtotal,2) : $total;
        		            $bookings["h"][] = array(  $acc->nr .' '. $acc->title, $account, $sum);
        		        }
        		        
        		        // Steuer
        		        if($item->taxtype > 0 && $item->productUsage > 0) {

        		            $missing_accounts = array();
        		            
                            if(($account_type == 'docsHabenTax' || $account_type == 'docsSollTax') 
                                && $taxes = json_decode( $item->taxtotal, true)) {
                                    
                                $acc_type = ($item->productUsage == 2) ? "s" : "h";
            		                    
                                foreach($taxes as $tax => $value) {
            		                if($account_title == $tax) {
            		                    
            		                    $bookings[$acc_type][] = array( $acc->nr .' '. $acc->title ,$account, round($value,2) );
            		                } else {
            		                    $missing_accounts[$tax] = $value;
            		                }
            		                unset($taxes[$tax]);
            		            }
            		            $item->taxtotal = json_encode($taxes);
            		        }
            		        
        		        }
        		        
    		        }
    		        
    		        if(!empty($missing_accounts)) {
                        
    		            foreach($missing_accounts as $tax => $value) {
        		                
        		            $acc_type = ($item->productUsage == 2) ? "s" : "h";
        		            $tax_title = $tax .'% '. JText::_('COM_SECRETARY_TAX').' '. JText::_('COM_SECRETARY_ACCOUNT');
        		            $tax_acc_title = JText::sprintf('COM_SECRETARY_SEARCH_THIS', $tax_title);
        		            $bookings[$acc_type][] = array( $tax_acc_title ,0, round($value,2) );
        		            
    		            }
    		        }
    		        
    		    }
                
    		    
		    } 
		    
		}
		
		return $bookings;
	}
	
	
		
	public static function reorder( $items )
	{
		$length = count($items) - 1;
		
		$konten = array();
		foreach($items as $item) {
			$konten[$item->type][$item->parent_id][] = $item;
			if(!isset($konten[$item->type][$item->parent_id]['soll']) && !isset($konten[$item->type][$item->parent_id]['haben'])) {
				$konten[$item->type][$item->parent_id]['soll'] = $item->soll;
				$konten[$item->type][$item->parent_id]['haben'] = $item->haben;
			} else {
				$konten[$item->type][$item->parent_id]['soll'] += $item->soll;
				$konten[$item->type][$item->parent_id]['haben'] += $item->haben;
			}
		}
		ksort($konten);
		/* 
		
		for($parent_idx  = $length; $parent_idx >= 0; $parent_idx-- ) { 
			for($child_idx = $length; $child_idx >= 0; $child_idx-- ) {
				if($items[$child_idx]->kid === $items[$parent_idx]->id) {
					if(!isset($items[$child_idx]->step)) {
						$items[$child_idx]->step = 1;
					} else 
						$items[$child_idx]->step++;
					// new position for insert
					$newPosition = ($child_idx < $parent_idx) ? $parent_idx : $parent_idx + 1 ;
					// Move element in Array
					$out = array_splice($items, $child_idx, 1);
					array_splice($items, $newPosition, 0, $out);
				}
			}
		}
		 
		*/
		return $konten;
	}
}
