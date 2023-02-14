<?php

/**
 * @version     3.2.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * 
 */

namespace Secretary\Helpers;

use JError;
use JText;
use stdClass;

// No direct access
defined('_JEXEC') or die;

class Subjects
{
	public static $selectedColumns = array(
		'category' => true,
		'street' => false,
		'zip' => true,
		'location' => true,
		'country' => false,
		'phone' => false,
		'email' => false,
		'number' => false,
		'id' => false,
	);

	public static function cleanSubject($array)
	{
		foreach ($array as $i => $a) {
			$array[$i] = \Secretary\Utilities::cleaner(trim($a));
		}
		return $array;
	}

	public static function getSubjectByName($gender, $lastname, $firstname = false, $street = false, $zip = false, $location = false)
	{
		// Naive Lösung für das Holen der gespeicherten ID
		// Idee war: Wir speichern die Werte zuerst und suchen danach nach eben diesen Werten und der zugehörigen ID
		// Neu: $subjectID	= $db->insertid();

		// sollte im constructor stehen funx aber nicht
		$business = \Secretary\Application::company();

		// test if Exists
		$db = \Secretary\Database::getDBO();
		$query = $db->getQuery(true);
		$query->select("id")
			->from($db->qn('#__secretary_subjects'))
			->where($db->qn('business') . '=' . $db->escape($business['id']))
			->where($db->qn('gender') . '=' . $db->escape($gender));

		if (!empty($firstname))
			$query->where($db->qn('firstname') . '=' . $db->quote($firstname));
		if (!empty($lastname))
			$query->where($db->qn('lastname') . '=' . $db->quote($lastname));
		if (!empty($street))
			$query->where($db->qn('street') . '=' . $db->quote($street));
		if (!empty($zip))
			$query->where($db->qn('zip') . '=' . $db->quote($zip));
		if (!empty($location))
			$query->where($db->qn('location') . '=' . $db->quote($location));

		$db->setQuery($query);
		$subjectID = $db->loadResult();

		return $subjectID;
	}

	public static function addNewSubject($subject, $timestamp = false, $created_by = false)
	{
		$zip = '';

		$gender = (int) $subject[0];
		$name = $subject[1];
		$street = $subject[2];
		$zip = $subject[3];
		$location = $subject[4];
		$phone = $subject[5];
		$email = $subject[6];

		if ($gender == 2) {
			$lastname = trim($name);
			$firstname = '';
		} else {
			$cleanname = explode(" ", trim($name));
			$lastname = trim(array_pop($cleanname));
			$firstname = trim(str_replace($lastname, '', $name));
		}

		$subjectID = self::getSubjectByName($gender, $lastname, $firstname, $street, $zip, $location);

		// Insert the object into the user profile table.
		if (empty($subjectID) && !empty($subject[1])) {

			// Create and populate an object.
			$db = \Secretary\Database::getDbo();
			$profile = new stdClass();

			// Google Maps 
			if (!empty($subject[3])) {
				$coords = \Secretary\Helpers\Locations::getCoords($street, $zip, $location);
				$profile->lat = $coords['lat'];
				$profile->lng = $coords['lng'];
			} else {
				$profile->lat = 0.0;
				$profile->lng = 0.0;
			}

			// sollte im constructor stehen funx aber nicht
			$business = \Secretary\Application::company();

			// Prepare Object
			$profile->business = $business['id'];
			$profile->gender = $gender;
			$profile->firstname = $firstname;
			$profile->lastname = $lastname;
			$profile->street = $street;
			$profile->zip = $zip;
			$profile->location = $location;
			$profile->phone = $phone;
			$profile->email = $email;
			$profile->created_by = (int) ((empty($created_by)) ? 0 : $created_by);
			$profile->created = $timestamp;

			$result = $db->insertObject('#__secretary_subjects', $profile);
			$subjectID = $db->insertid();

			// Activity
			\Secretary\Helpers\Activity::set('subjects', 'created', 0, $subjectID, \Secretary\Joomla::getUser()->id);
		}

		return $subjectID;
	}


	public static function getSubjects($search, $source = 'subjects')
	{
		$i = 0;
		$json = array();

		if (!isset($search))
			exit;

		$business = \Secretary\Application::company();
		$user = \Secretary\Joomla::getUser();
		$db = \Secretary\Database::getDBO();

		$searchValue = $db->quote('%' . htmlentities(strtolower($search), ENT_QUOTES) . '%');

		switch ($source) {
			default:
			case 'subjects':

				$query = $db->getQuery(true);
				$query->select($db->qn(array('s.id', 'gender', 's.firstname', 's.lastname', 's.street', 's.zip', 's.location', 's.phone', 's.email')))
					->from($db->qn('#__secretary_subjects', 's'))
					->where('s.business = ' . (int) $business['id'])
					->where("(lower(s.lastname) LIKE " . $searchValue . ") OR (lower(s.firstname) LIKE " . $searchValue . ") 
							OR (concat(s.firstname,' ',s.lastname) LIKE " . $searchValue . ')')
					->order('s.lastname ASC');
				$db->setQuery($query, 0, 50);
				$results = $db->loadObjectList();

				foreach ($results as $result) {

					if (
						$user->authorise('core.show', 'com_secretary.subject.' . $result->id)
						|| $user->authorise('core.show.other', 'com_secretary.subject')
					) {

						$json[$i]["connections"] = \Secretary\Helpers\Connections::getConnectionsSubjectData($result->id);

						$json[$i]["id"] = (int) $result->id;
						$json[$i]["value"] = \Secretary\Utilities::cleaner($result->firstname, true) . ' ' . \Secretary\Utilities::cleaner($result->lastname, true);
						$json[$i]["street"] = \Secretary\Utilities::cleaner($result->street, true);
						$json[$i]["zip"] = $result->zip;
						$json[$i]["location"] = \Secretary\Utilities::cleaner($result->location, true);
						$json[$i]["phone"] = $result->phone;
						$json[$i]["email"] = $result->email;
						$json[$i]["gender"] = $result->gender;
						$i++;
					}

					if ($i > 9) {
						break;
					}
				}

				break;

			case 'users':
				$query = $db->getQuery(true);
				$query->select('id,name,username')
					->from('#__users')
					->where(' ( name LIKE ' . $searchValue . ') OR (username LIKE ' . $searchValue . ')')
					->order('name ASC');
				$db->setQuery($query, 0, 50);
				$results = $db->loadObjectList();

				foreach ($results as $result) {

					if (
						$user->authorise('core.show', 'com_secretary.subject.' . $result->id)
						|| $user->authorise('core.show.other', 'com_secretary.subject')
					) {
						$json[$i]["id"] = $result->id;
						$json[$i]["value"] = $result->username;
						$json[$i]["street"] = $result->name;
						$json[$i]["zip"] = NULL;
						$json[$i]["location"] = NULL;
						$json[$i]["phone"] = NULL;
						$json[$i]["email"] = NULL;
						$json[$i]["gender"] = NULL;
						$i++;
					}

					if ($i > 9) {
						break;
					}
				}

				break;
		}

		flush();

		return json_encode($json);
	}

	public static function importUsers()
	{
		$db = \Secretary\Database::getDBO();
		$users = $db->getQuery(true);
		$users->select($db->qn(array("id", "name", "email", "registerDate")))
			->from($db->qn('#__users'));

		try {
			$db->setQuery($users);
			$allUsers = $db->loadObjectList();
		} catch (\RuntimeException $e) {
			JError::raiseWarning(500, $e->getMessage);
			return false;
		}

		$result = array();
		foreach ($allUsers as $u) {
			$subject = array();
			$subject[0] = 0;
			$subject[1] = $u->name;
			$subject[2] = "";
			$subject[3] = "";
			$subject[4] = "";
			$subject[5] = "";
			$subject[6] = $u->email;

			$cleanname = explode(" ", trim($u->name));
			$lastname = trim(array_pop($cleanname));
			$firstname = trim(str_replace($lastname, '', $u->name));

			$subjectID = self::getSubjectByName(3, $lastname, $firstname, false, false, false, $u->id);
			if (empty($subjectID) && !empty($subject[1])) {
				self::addNewSubject($subject, $u->registerDate, $u->id);
				$result[] = $u->name;
			}
		}

		if (empty($result)) {
			$msg = JText::_("COM_SECRETARY_SUBJECTS_IMPORT_ADDED_NAMES") . '<br>' . JText::_("COM_SECRETARY_SUBJECTS_IMPORT_NOONE");
		} else {
			$msg = JText::sprintf("COM_SECRETARY_SUBJECTS_IMPORT_ADDED_NAMES", count($result ?? [])) . '<br>' . implode(", ", $result);
		}
		return $msg;
	}

	public static function convertIDtoJSON($id)
	{
		$id = (int) $id;
		$subject = array("", "", "", "", "", "");
		if (!empty($id)) {
			$subAssoc = \Secretary\Database::getQuery('subjects', $id, 'id', 'gender,firstname,lastname,street,zip,location,phone,email', 'loadAssoc');
			$subject[0] = $subAssoc['gender'];
			$subject[1] = $subAssoc['firstname'] . ' ' . $subAssoc['lastname'];
			$subject[2] = $subAssoc['street'];
			$subject[3] = $subAssoc['zip'];
			$subject[4] = $subAssoc['location'];
			$subject[5] = $subAssoc['phone'];
			$subject[6] = $subAssoc['email'];
		}
		return json_encode($subject);
	}


	public static function searchLocations($term, $field)
	{

		$i = 0;
		$json = array();

		if (!isset($term) || !isset($field))
			exit;

		$business = \Secretary\Application::company();
		$user = \Secretary\Joomla::getUser();
		$db = \Secretary\Database::getDBO();
		$searchValue = $db->quote('%' . htmlentities($term, ENT_QUOTES) . '%');

		$query = $db->getQuery(true);
		$query->select("zip,location")
			->from($db->qn("#__secretary_subjects"))
			->group($db->qn($db->escape($field)))
			->where($db->qn($db->escape($field)) . ' LIKE ' . $searchValue);
		$db->setQuery($query, 0, 50);
		try {
			$results = $db->loadObjectList();
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage());
			exit;
		}

		foreach ($results as $result) {
			$json[$i]["value"] = \Secretary\Utilities::cleaner($result->$field, true);
			$json[$i]["zip"] = \Secretary\Utilities::cleaner($result->zip, true);
			$json[$i]["location"] = \Secretary\Utilities::cleaner($result->location, true);
			if ($i > 9)
				break;
			$i++;
		}

		flush();

		return json_encode($json);
	}
}