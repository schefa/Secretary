<?php
/**
 * @version     3.0.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      GNU General Public License version 2 or later.
 */

namespace Secretary\Helpers;

use stdClass;

// No direct access
defined('_JEXEC') or die; 

abstract class Products
{
    
    public static $selectedColumns = array(
        'id'            => false,
        'nr'            => false,
        'category'      => false,
        'location'      => false,
        'year'          => false,
        'entity'        => false,
        'taxRate'       => false,
        'quantityMin'   => false,
        'quantityMax'   => false,
        'priceCost'     => true,
        'quantityBought'=> true,
        'totalBought'   => true,
        'priceSale'     => true,
        'quantity'      => true,
        'total'         => true,
    );
    
	/** 
	 * Updates products information
	 * 
	 * @return array list of products that were successfully updated
	 */
    public static function updateProducts($documents_pks)
	{
		$result = array();
		$ids = implode(',', $documents_pks);
		
		$db = \Secretary\Database::getDBO();
        $query = $db->getQuery(true);
        $query->select($db->qn(array("id","createdEntry","catid","items","taxtype","fields")));
        $query->from($db->qn('#__secretary_documents'));
		
		if(!empty($ids))	
			$query->where($db->qn('id').' IN ('. $ids .')');
		
		$db->setQuery($query);
		$documents_items = $db->loadObjectList();
		
		// Loop documents
		foreach($documents_items AS $document)
		{
			if( $parameter = json_decode($document->fields, true) )
			{
				if( !empty($parameter['pUsage']) && ($document->createdEntry > 0) && ($document->catid > 0))
				{	
					$return = false;
					// Clears all document history in existing products
					if($parameter['pUsage'] == 1 || $parameter['pUsage'] == 2)
						self::deleteOldProducts( $document->createdEntry , $parameter['pUsage'] );
				
					if($products = json_decode($document->items,true))
					{
						$position	= 1;
						
						// Loop Products of document
						foreach($products AS $product)
						{
						    // string are escaped in items. Re-escape to add new products correctly
						    $product['title']		= \Secretary\Utilities::cleaner($product['title'],true);
						    $product['description']	= \Secretary\Utilities::cleaner($product['description'],true);
										
							if ( $product['total'] == 0 )
								$product['total'] = $product['quantity'] * $product['price'];
								
							if(empty($product['taxRate']) || $product['taxRate'] < 0 )
								$product['taxRate'] = 0;
							
							if($document->taxtype == 1) {
								// Mwst inklusiv
								$product['subtotal'] = ( $product['total'] / ( 1 + ($product['taxRate'] / 100)) );
								$product['total'] += round($product['subtotal'],4);
							} else {
								$product['subtotal'] = $product['total'];
							}
									
							// Produktupdate
							if($parameter['pUsage'] == 1 or $parameter['pUsage'] == 2) {
								$return = self::addNewProduct( (int) $document->createdEntry , $position, $document->catid , $product, $parameter['pUsage'] );
							}
							
							if($return !== false) {
								$result[] = $product['title'];	
							}
							
							$position++;	
						}
					}
				}
			}
		}
		
		return array_unique($result);	
	}

	/**
	 * Deletes document in product history
	 * 
	 * @param int $document_timestamp
	 * @param int $product_usage
	 */
    public static function deleteOldProducts( $document_timestamp, $product_usage )
	{
		$business	= \Secretary\Application::company();
		// Plan: Testen ob fuer den Entry noch Produkte gibt, dann alle Produkte loeschen, zwischendurch Daten fuer die Produkte aktualisieren
		// Neue Strategie: Nicht nach Titel suchen, sondern nach Timestamp ID, da bei Änderungen des Titels die alten Produktinformationen bleiben wuerden
		$db = \Secretary\Database::getDBO();
		$query = $db->getQuery(true);
		$query->select("id,items,history");
		$query->from($db->qn("#__secretary_products"));
		// no better solution as over an extra field
		$query->where("business=". intval($business['id']));
		$query->where("year=".date('Y', $document_timestamp));
		$query->where("items LIKE ".$db->quote('%'.$document_timestamp.'%') );
		$db->setQuery($query);
		$products = $db->loadObjectList();
		
		// If exists, then delete all
		if(!empty($products))
		{
			foreach($products AS $product)
			{
				// alte Strings mit veralteten Daten
				$oldHistory		= json_decode($product->history, true);
				$oldItems		= json_decode($product->items,true);
				$oldQuantity	= 0.0;
				$oldTotal		= 0.0;
				
				/*  [ 0 = [ laufender Index [0], [1], ...
						"einmalige timestamp für eindeutige entry-Zuordnung" = {
							"einmalige Zeilennummer innerhalb des Entrys" = [
								0 = "zeile",
								1 = "quantity",
								2 = "price",
								3 = "total" ,
								4 = "catid",
								5 = "type" ] } ], ] */
			
				// Verlauf durchgehen, Bestehende Läschen und vorhandenen Gesamtverbrauch zurücksetzen
				for($x = 0; $x < count($oldHistory); $x++) {
					if( !empty($oldHistory[$x][$document_timestamp]) ) {
						
						$oldQuantity	+= floatval($oldHistory[$x][$document_timestamp][1]);
						$oldTotal		+= floatval($oldHistory[$x][$document_timestamp][3]);
						
						unset($oldHistory[$x][$document_timestamp]); 
					} 
				}
				
				// unset lässt indezes übrig, daher
				$oldHistory = array_filter($oldHistory);
				
				// ID aus dem Produkt entfernen
				if(($key = array_search($document_timestamp, $oldItems)) !== false) {
					unset($oldItems[$key]);
				}
				
				// Produkt holen, updaten anhand der Produkt ID
				$dQuery = $db->getQuery(true)
						->update("#__secretary_products")
						->set("items=".$db->quote(json_encode($oldItems)))
						->set("history=".$db->quote(json_encode($oldHistory)))
						->where("id=".$product->id);
				
				if($product_usage == 1)
				{
					if(!empty($oldQuantity) or ($oldQuantity != 0))
						$dQuery->set($db->qn("quantity").'='.$db->qn("quantity")." - ". $oldQuantity );
						
					if(!empty($oldTotal) or ($oldTotal != 0))
						$dQuery->set($db->qn("total")." = ".$db->qn("total")." - ". $oldTotal );
				}
				elseif($product_usage == 2)
				{
					if(!empty($oldQuantity) or ($oldQuantity != 0))
						$dQuery->set($db->qn("quantityBought")." = ".$db->qn("quantityBought")." - ". $oldQuantity );
						
					if(!empty($oldTotal) or ($oldTotal != 0))
						$dQuery->set($db->qn("totalBought").'='.$db->qn("totalBought")." - ". $oldTotal );
				}
						
				$dQuery->where("id=". $db->escape($product->id));
				$db->setQuery($dQuery);
				$db->execute();
			}
		}
	}
	
	/**
	 * Creates new product in table
	 * 
	 * @param int $ptimestamp
	 * @param int $position
	 * @param int $pcatid
	 * @param array $product
	 * @param int $product_usage
	 * @return number|number|mixed
	 */
	public static function addNewProduct( $ptimestamp, $position, $pcatid, $product, $product_usage)
	{
		$return = false;
		
		if (empty($product['title'])) {
		    return $return;
		}
		
		$business	= \Secretary\Application::company();
		$user		= \Secretary\Joomla::getUser();
		
		$input = array(	$ptimestamp => array( $position,$product['quantity'],$product['price'],$product['subtotal'], $pcatid,(int) $product_usage) );
		$items = array( $ptimestamp );
		$ptotal = $product['subtotal'];
		
		// test if Exists
		$db = \Secretary\Database::getDBO();
        $query = $db->getQuery(true);
		$query->select("*")
				->from($db->qn("#__secretary_products"))
				->where($db->qn("title").' LIKE "'. \Secretary\Utilities::cleaner($product['title']) .'"' )
				->where($db->qn("year")." = ".date('Y', $ptimestamp))
				->where($db->qn("business").'='. intval($business['id']));
		$db->setQuery($query);
		$item = $db->loadObject(); 
		
		$product['title']		= \Secretary\Utilities::cleaner($product['title']);
		$product['description']	= \Secretary\Utilities::cleaner($product['description']);
		$product['pno']			= htmlentities($product['pno']);
		$product['entity']		= htmlentities($product['entity']);
		$product['quantity'] 	= floatval( $product['quantity'] );		
		$product['price'] 		= floatval( $product['price'] );
		$product['taxRate'] 	= floatval( $product['taxRate'] );
		$product['total'] 		= floatval( $product['total'] );
		 
		// Update
		if(!empty($item))
		{
			// alter Strings mit veralteten Daten
			$oldhistory	= json_decode($item->history,true);
			array_unshift($oldhistory, $input);
			
			$oldItems = json_decode($item->items,true);
			$oldItems = array_merge($oldItems, $items);
			$oldItems = array_unique($oldItems);

			$total = 0;
			$totalBought = 0;
			foreach($oldhistory as $i) {
			    foreach($i as $v) {
    			    if(!isset($v[5])) continue;
    			    if(intval($v[5]) === 2) { $totalBought += $v[3]; }
    			    else { $total += $v[3]; } 
			    } 
			}
			
			$query = $db->getQuery(true);
			$query->update("#__secretary_products");
			$query->set($db->qn("business").'='. intval($business['id']));
			$query->set($db->qn("entity").'='. $db->quote($product['entity']));
			$query->set($db->qn("nr").'='. $db->quote($product['pno']));
			$query->set($db->qn("taxRate").'='. $db->escape($product['taxRate']));
			$query->set($db->qn("history").'='. $db->quote(json_encode($oldhistory)));
			$query->set($db->qn("items").'='. $db->quote(json_encode($oldItems)));
			$query->set($db->qn("total").'='. $db->escape($total));
			$query->set($db->qn("totalBought").'='. $db->escape($totalBought));
			
			if($product_usage == 1)
			{
				if(($item->priceSale == 0) && ($product['price'] > 0))
					$query->set($db->qn("priceSale").'='. floatval($product['price']));
					
				if(!empty($product['quantity']) or ($product['quantity'] != 0))
					$query->set($db->qn("quantity").'='.$db->qn("quantity")." + ". $db->escape($product['quantity']));
			}
			elseif($product_usage == 2)
			{
				if(($item->priceCost == 0) && ($product['price'] > 0))
					$query->set($db->qn("priceCost").'='. floatval($product['price']));
					
				if(!empty($product['quantity']) or ($product['quantity'] != 0))
					$query->set($db->qn("quantityBought").'='.$db->qn("quantityBought")." + ". $db->escape($product['quantity']));
			}
			
			$query->where("id=". $db->escape($item->id));
			$db->setQuery($query);
			$return = $db->execute();
			
			// Activity
			\Secretary\Helpers\Activity::set('products','edited',$item->catid,$item->id);
		}
		else
		{
		    // Insert
			$object = new stdClass();
			
			// Must be a valid primary key value.
			$object->business    = $db->escape($business['id']);
			$object->title       = $product['title'];
			$object->description = $product['description'];
			$object->created_by  = $user->id;
			$object->entity      = \Secretary\Utilities::cleaner($product['entity']);
			$object->nr          = \Secretary\Utilities::cleaner($product['pno']);
			$object->taxRate     = $db->escape($product['taxRate']);
			$object->year        = date('Y', $ptimestamp);
			$object->items       = json_encode($items);
			$object->history     = json_encode(array($input));
			
			if($product_usage == 1)
			{		
				if(!empty($product['price']) or ($product['price'] != 0)) {
					$object->priceSale= $db->escape($ptotal / $product['quantity']);
				}
				if(!empty($product['quantity']) or ($product['quantity'] != 0)) {
					$object->quantity= $db->escape($product['quantity']);
				}
				if(!empty($ptotal) or ($ptotal != 0)) {
					$object->total= $db->escape($ptotal);
				}
			}
			elseif($product_usage == 2)
			{
				if(!empty($product['price']) or ($product['price'] != 0)) {
					$object->priceCost= $db->escape($ptotal / $product['quantity']);
				}
				if(!empty($product['quantity']) or ($product['quantity'] != 0)) {
					$object->quantityBought= $db->escape($product['quantity']);
				}
				if(!empty($ptotal) or ($ptotal != 0)) {
					$object->totalBought= $db->escape($ptotal);
				}
			}
			
			$return = $db->insertObject('#__secretary_products', $object);
			 
			// Activity
			if($return) {
                \Secretary\Helpers\Activity::set('products', 'created', 0, $db->insertid());
			}
		}
		
		return $return;
	}

	/**
	 * Products search
	 * 
	 * @param string $search
	 * @param number $pUsage
	 * @return string JSON result
	 */
	public static function search( $search , $pUsage = 1 )
	{
		$i		= 0;
		$json	= array();
		
		if ( !isset($search) )
			exit;
		 
		$business	= \Secretary\Application::company();
        $user		= \Secretary\Joomla::getUser();
        $db			= \Secretary\Database::getDBO(); 
        $searchValue= $db->quote('%'.str_replace("%","", \Secretary\Utilities::cleaner($search)).'%');
		
        $query = $db->getQuery(true);
        $query->select($db->qn(array("id","nr","title","description","taxRate","priceSale","priceCost","total","quantity")));
        $query->from($db->qn("#__secretary_products"));
        $query->where($db->qn("business").'='.intval($business['id']));
        $query->where('( title LIKE '.$searchValue .') OR ( description LIKE '.$searchValue.')');
        $query->order('CHAR_LENGTH(history) DESC');
        $db->setQuery($query,0,50);
		try
		{
        	$results = $db->loadObjectList();
		}
		catch(\Exception $e)
		{
			throw new \Exception( $e->getMessage() );
			exit;
		}
	
		foreach($results AS $result)
		{
			if($user->authorise('core.show','com_secretary.product.'.$result->id) 
			|| $user->authorise('core.show.other','com_secretary.product'))
			{
				$json[$i]["id"]				= $result->id;
				$json[$i]["value"]			= \Secretary\Utilities::cleaner($result->title,true);
				$json[$i]["description"]	= \Secretary\Utilities::cleaner($result->description,true);
				$json[$i]["pno"]			= $result->nr;
				$json[$i]["taxRate"]		= $result->taxRate;
				
				$price = ($pUsage == 2) ? $result->priceCost : $result->priceSale;
				$json[$i]["price"]= round(floatval($price),2);
				$i++;
			}
			
			if($i > 9) { break;	}
		}
		
		flush();
		
		return json_encode($json);
    }
}
