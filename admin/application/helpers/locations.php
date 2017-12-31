<?php
/**
 * @version     3.0.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
 */

namespace Secretary\Helpers;

// No direct access
defined('_JEXEC') or die;

class Locations
{
	
    /**
     * Global location types
     * 
     * @var array
     */
	public static $options = array(
			//  'businesses' => 'COM_SECRETARY_LOCATIONS_BUSINESSES',
			  'documents' => 'COM_SECRETARY_LOCATIONS_DOCUMENTS',
			  'products' => 'COM_SECRETARY_LOCATIONS_PRODUCTS',
			  'times' => 'COM_SECRETARY_LOCATIONS_TIMES'
			);

    /**
     * Method to get the GPS coordinates
     * 
     * @param string $street
     * @param string $zip
     * @param string $location
     * @return array latitude and longitude
     */
	public static function getCoords( $street = NULL, $zip = NULL, $location = NULL )
	{
		$result = array();
		$searchLocation = $street . ' ' . $zip . ' ' . $location;
		$searchLocation = str_replace(' ', '+', $searchLocation);
				
		$url = "http://maps.google.com/maps/api/geocode/json?address=".html_entity_decode($searchLocation)."&sensor=false";
		$response = file_get_contents($url);
		$response = json_decode($response, true);
		 
		$result['lat'] = $response['results'][0]['geometry']['location']['lat'];
		$result['lng'] = $response['results'][0]['geometry']['location']['lng'];
		return $result;
	}
	
	/**
	 * Method to search after a location title
	 * 
	 * @param string $term 
	 * @param string $extension 
	 * @return string JSON encoded list of locations
	 */
	public static function search($term,$extension)
	{
		
		$i        = 0;
		$json     = array(); 
		
		if (!isset($term) || !isset($extension))
			exit;
		
		$business	= \Secretary\Application::company();
        $user		= \Secretary\Joomla::getUser();
		$db			= \Secretary\Database::getDBO();
		$searchValue= $db->quote('%'. htmlentities($term, ENT_QUOTES) .'%');
		
		$query = $db->getQuery(true);
        $query->select("id,title,zip,street,location")
            ->from($db->qn("#__secretary_locations"))
            ->where($db->qn("business")."=".intval($business['id']))
            ->where($db->qn("extension")."=".$db->quote($extension))
            ->where($db->qn('title').' LIKE '.$searchValue);
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
			if($user->authorise('core.show','com_secretary.location.'.$result->id) 
			|| $user->authorise('core.show.other','com_secretary.location'))
			{
				$json[$i]["id"]			= $result->id;
				$json[$i]["title"]		= \Secretary\Utilities::cleaner($result->title,true);
				$json[$i]["street"]		= $result->street;
				$json[$i]["zip"]		= $result->zip;
				$json[$i]["location"]	= $result->location;
				$i++;
			}
			
			if($i > 9) break;
		}
		
		flush();
		
		return json_encode($json);
		
	}
	
}
