<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Secretary\Helpers;


defined('_JEXEC') or die;

class Templates
{

	protected static $template = array();
	protected static $templates = array();

	public static $formats = array(
		'210mm;297mm' => 'A4 - Portrait',
		'297mm;210mm' => 'A4 - Landscape',
		'148mm;210mm' => 'A5 - Portrait',
		'210mm;148mm' => 'A5 - Landscape',
		'100%;100%' => '100%'
	);

	public static $templateTagsBusiness = array(
		"user-name" => \Secretary\DataTypeEnum::String,
		"business-title" => \Secretary\DataTypeEnum::Clean,
		"slogan" => \Secretary\DataTypeEnum::Clean,
		"address" => \Secretary\DataTypeEnum::Clean,
		"taxvalue" => \Secretary\DataTypeEnum::Float,
	);

	public static $templateTagsInfo = array(
		"currency" => \Secretary\DataTypeEnum::String,
		"document-title" => \Secretary\DataTypeEnum::String,
		"title" => \Secretary\DataTypeEnum::String,
		"deadline" => \Secretary\DataTypeEnum::String,
		"nr" => \Secretary\DataTypeEnum::String,
		"paid" => \Secretary\DataTypeEnum::Money,
		"total-remaining" => \Secretary\DataTypeEnum::Money,
		"total" => \Secretary\DataTypeEnum::Money,
		"subtotal" => \Secretary\DataTypeEnum::Money,
		"note" => \Secretary\DataTypeEnum::Textarea,
		"created" => \Secretary\DataTypeEnum::Date,
	);

	public static $itemShortCuts = array(
		"{item_start}",
		"{item_quantity}",
		"{item_entity}",
		"{item_title}",
		"{item_desc}",
		"{item_price}",
		"{item_taxrate}",
		"{item_taxamount}",
		"{item_total}",
		"{item_end}",
		"{item_doc_start}",
		"{item_doc_title}",
		"{item_doc_created}",
		"{item_doc_deadline}",
		"{item_doc_subtotal}",
		"{item_doc_tax}",
		"{item_doc_total}",
		"{item_doc_nr}",
		"{item_doc_end}"
	);

	/**
	 * Method to get a single template data by id
	 * 
	 * @param number $id
	 * @return mixed
	 */
	public static function getTemplate($id)
	{
		if (!isset(self::$template[$id]))
		{
			self::$template[$id] = \Secretary\Database::getQuery('templates', $id);
		}
		
        return self::$template[$id];
	}

	/**
	 * Method to replace template tags with data
	 *
	 * @param array $search
	 * @param array $replace
	 * @param array $info
	 */
	protected static function replaceTemplateTags(&$search, &$replace, &$info, $templateTags)
	{
		if (empty($templateTags))
		{
			return;
		}

		foreach ($templateTags as $fieldname => $datatype)
		{
			$search[] = '{' . $fieldname . '}';
			
            if (isset($info[$fieldname]))
			{
				switch ($datatype)
				{
					case \Secretary\DataTypeEnum::String:
					case \Secretary\DataTypeEnum::Date:
						$replace[] = $info[$fieldname];
						break;

					case \Secretary\DataTypeEnum::Clean:
						$replace[] = \Secretary\Utilities::cleaner(nl2br($info[$fieldname]), true);
						break;

					case \Secretary\DataTypeEnum::Textarea:
						$replace[] = nl2br($info[$fieldname]);
						break;

					case \Secretary\DataTypeEnum::Money:
						$replace[] = \Secretary\Utilities\Number::getNumberFormat($info[$fieldname]);
						break;

					case \Secretary\DataTypeEnum::Float:
						$replace[] = floatval($info[$fieldname]);
						break;

					case \Secretary\DataTypeEnum::Date:
						$replace[] = $info['created'];
						break;

					default:
						$replace[] = '';
						break;
                }
			}
            else
			{
				switch ($datatype)
				{
					case \Secretary\DataTypeEnum::Date:
						$replace[] = \Joomla\CMS\HTML\HTMLHelper::_('date', date('Y-m-d'), \Joomla\CMS\Language\Text::_('DATE_FORMAT_LC4'));
						break;

					default:
						$replace[] = '';
						break;
                }
			}
		}
	}

	public static function transformText($text, $extra_item = array(), $info = array(), $extension = 'text')
	{
		// $text is null for templates with no header/footer set; preg_replace() etc. below require a string.
		$text = (string) $text;

		// Remove Toolbar
		$toolbarPattern = '/<div class="box-toolbar"><div class="box-toolbar-group"><div.*?>(.*?)<\/div><div.*?>(.*?)<\/div><\/div><div.*?><div.*?>(.*?)<\/div><div.*?>([^<]*?)<\/div><\/div><\/div>/i';
		$text = preg_replace($toolbarPattern, '', $text);

		$text = html_entity_decode($text);

		$search = array();
		$replace = array();

		// Business Informationen
		$business = \Secretary\Application::company();
		$business['user-name'] = \Secretary\Joomla::getUser()->name;
		self::replaceTemplateTags($search, $replace, $business, self::$templateTagsBusiness);

		$search[] = '{logo}';
		
        if (!empty($business['upload']) && $logoImage = \Secretary\Database::getQuery('uploads', $business['upload'], 'id', 'business,title,folder'))
		{
			$replace[] = \Secretary\Helpers\Uploads::getUploadFile($logoImage, '', '', TRUE);
		}
        else
		{
			$replace[] = '';
		}

		// Kontaktinformationen
		if (!empty($extra_item))
		{
			if (isset($extra_item['subject']))
			{
				self::_replaceContact($text, $extra_item['subject'], $info);
			}
			
            if (isset($extra_item['product']))
			{
				self::_replaceProduct($text, $extra_item['product']);
			}
		}

		// Generelle Informationen für das Dokument
		if (!empty($info))
		{
			self::_infoText($text, $info, $business);
		}

		// Secretary Nutzer
		if (isset($info['created_by']))
		{
			$userContact = \Secretary\Database::getQuery('subjects', (int) $info['created_by'], 'created_by');
			
            if (!empty($userContact->id))
			{
				self::_replaceContact($text, $userContact, $info, 'createdby');
			}
		}

		// Replace
		$text = str_replace($search, $replace, $text);

		// Remove old fields
		$text = preg_replace('#({field-(.*?)})#ms', '', $text);
		$text = preg_replace('#({createdby-(.*?)})#ms', '', $text);

		return $text;
	}

	private static function _infoText(&$text, &$info, &$business)
	{
		$search = array();
		$replace = array();

		// Taxes
		self::_replaceTaxtotal($text, $info, $search, $replace);

		// Extra fields
		if (!empty($info['fields']))
		{
			$info['fields'] = str_replace('\\n', '<br>', $info['fields']);
			$extraFields = json_decode($info['fields'], true);
			
            foreach ($extraFields as $rowCount => $value)
			{
				$search[] = '{field-' . \Secretary\Route::safeURL($value[1]) . '}';
				$v = ($value[3] === 'textarea') ? nl2br($value[2]) : $value[2];
				$v = ($value[3] === 'date') ? \Joomla\CMS\HTML\HTMLHelper::_('date', $value[2], \Joomla\CMS\Language\Text::_('DATE_FORMAT_LC4')) : $value[2];

				$replace[] = \Secretary\Utilities::cleaner($v, true);
			}
		}

		self::replaceTemplateTags($search, $replace, $info, self::$templateTagsInfo);

		$search[] = '{discount}';
		$replace[] = isset($info['rabatt']) ? \Secretary\Utilities\Number::getNumberFormat($info['rabatt']) : '';

		// Products and services
		if (isset($info['items']))
		{
			self::_replaceItems($text, $info, $search, $replace);
		}

		$text = str_replace($search, $replace, $text);

		$text = html_entity_decode($text);
	}

	private static function _replaceContact(&$text, $contactID, $info, $tagName = "contact")
	{

		$contact = is_numeric($contactID) ? \Secretary\Database::getQuery('subjects', $contactID) : $contactID;
		$search = array();
		$replace = array();

		if (isset($contact->gender))
		{
			$search[] = '{' . $tagName . '-gender}';
			
            if ($gender = \Secretary\Utilities::getGender($contact->gender))
			{
				$replace[] = $gender;
			}
            else
			{
				$replace[] = '';
			}
		}

		if (isset($contact->catid))
		{
			$contactCategory = \Secretary\Database::getQuery('folders', $contact->catid, 'id', 'title', 'loadResult');
			$search[] = '{' . $tagName . '-category-title}';
			$replace[] = isset($contactCategory) ? $contactCategory : '';
		}

		$columns = array('firstname', 'lastname', 'street', 'zip', 'location', 'country', 'phone', 'email', 'number');
		
        foreach ($columns as $value)
		{
			$search[] = '{' . $tagName . '-' . $value . '}';
			$replace[] = isset($contact->$value) ? \Secretary\Utilities::cleaner($contact->$value, true) : '';
		}

		$search[] = '{' . $tagName . '-upload}';
		$file = "";
		
        if (isset($contact->upload) && $contact->upload > 0)
		{
			$item = \Secretary\Database::getQuery('uploads', $contact->upload);
			$file = \Secretary\Helpers\Uploads::getUploadFile($item, '', '', TRUE);
		}
		$replace[] = $file;

		// Contact connection
		$search[] = '{' . $tagName . '-connection}';
		$connection = array();
		
        if (isset($info['connection']) && $info['connection'] > 0)
		{
			$connection = \Secretary\Database::getQuery('subjects', (int) $info['connection']);
		}
		$replace[] = !empty($connection->id) ? \Secretary\Utilities::cleaner($connection->firstname, true) . ' ' . \Secretary\Utilities::cleaner($connection->lastname, true) : '';

		$text = str_replace($search, $replace, $text);
	}

	private static function _replaceProduct(&$text, $productID)
	{

		$productID = \Secretary\Database::getQuery('products', $productID);
		$suchen = array();
		$ersetzen = array();

		$columns = array('nr' => 'nr', 'title' => 'title', 'description' => 'description', 'price-input' => 'priceCost', 'price-output' => 'priceSale', 'quantity-input' => 'quantityBought', 'quantity-output' => 'quantity', 'total-input' => 'totalBought', 'total-output' => 'total');

		foreach ($columns as $key => $value)
		{
			$suchen[] = '{product-' . $key . '}';
			$ersetzen[] = isset($productID->$value) ? \Secretary\Utilities::cleaner($productID->$value, true) : '';
		}

		$suchen[] = '{product-upload}';
		$file = "";
		
        if ($productID->upload > 0)
		{
			$item = \Secretary\Database::getQuery('uploads', $productID->upload);
			$file = \Secretary\Helpers\Uploads::getUploadFile($item, '', '', TRUE);
		}
		$ersetzen[] = $file;

		$suchen[] = '{product-entity}';
		
        if (is_numeric($productID->entity))
		{
			$entity = \Secretary\Database::getQuery('entities', $productID->entity, 'id', 'title', 'loadResult');
			$ersetzen[] = \Joomla\CMS\Language\Text::_($entity);
		}
        else
		{
			$ersetzen[] = $productID->entity;
		}

		$text = str_replace($suchen, $ersetzen, $text);
	}

	private static function _replaceTaxtotal(&$text, &$info, &$search, &$replace)
	{
		$item_list = array();
		$taxShortcuts = \Secretary\Utilities\Text\Search::between('{taxtotal_start}', '{taxtotal_end}', $text);
		$line = $taxShortcuts;
		$line_search = array();
		$line_replace = array();

		if (isset($info['taxtotal']) && ($taxtotal = json_decode($info['taxtotal'], true)) && is_array($taxtotal))
		{
			foreach ($taxtotal as $tax => $sum)
			{
				$line = $taxShortcuts;
				$line_search = array();
				$line_replace = array();

				if (strpos($text, '{taxtotal_percent}') !== false)
				{
					$line_replace[] = $tax;
					$search[] = $line_search[] = '{taxtotal_percent}';
					$replace[] = '';
				}

				if (strpos($text, '{taxtotal_value}') !== false)
				{
					$line_replace[] = \Secretary\Utilities\Number::getNumberFormat($sum, $info['currency']);
					$search[] = $line_search[] = '{taxtotal_value}';
					$replace[] = '';
				}

				$item_list[] = str_replace($line_search, $line_replace, $line);
			}
		}
        elseif (isset($info['taxtotal']) && $info['taxtotal'] > 0)
		{
			$item_list[] = \Secretary\Utilities\Number::getNumberFormat($info['taxtotal'], $info['currency']);
		}
        else
		{
			$item_list[] = '';
		}

		$search[] = '{taxtotal_end}';
		$replace[] = '';

		$text = preg_replace('#(?<={taxtotal_start})(.*?)(?={taxtotal_end})#ms', '', $text);
		$search[] = '{taxtotal_start}';
		$replace[] = implode('', $item_list);
	}

	private static function _replaceItems(&$text, &$info, &$search, &$replace)
	{
		$items = array_values($info['items']);
		$item_list = array();
		$documents_list = array();

		// Reihenfolge der Shortcurts im Template bestimmt wie diese ausgegeben werden
		$documentListShortCuts = \Secretary\Utilities\Text\Search::between('{item_doc_start}', '{item_doc_end}', $text);
		$doc_ordered = self::getItemShortCutsOrder($documentListShortCuts);

		$productListShortCuts = \Secretary\Utilities\Text\Search::between('{item_start}', '{item_end}', $text);
		$prod_ordered = self::getItemShortCutsOrder($productListShortCuts);

		// Positionen durchlaufen und ersetzen
		for ($i = 0; $i < count($items ?? []); $i++)
		{
			if (!isset($items[$i]))
			{
				continue;
			}

			if (!isset($items[$i]['id']) && strpos($text, '{item_start}') !== false)
			{
				$line = $productListShortCuts;
				$line_search = array();
				$line_replace = array();

				if (strpos($text, '{item_quantity}') !== false)
				{
					$line_replace[] = (strlen($items[$i]['title']) > 0) ? str_replace('.', ',', $items[$i]['quantity']) : '';
					$search[] = $line_search[] = '{item_quantity}';
					$replace[] = '';
				}

				if (strpos($text, '{item_entity}') !== false)
				{
					if (is_numeric($items[$i]['entity']))
					{
						$entity = \Secretary\Database::getQuery('entities', $items[$i]['entity'], 'id', 'title', 'loadResult');
						$line_replace[] = \Joomla\CMS\Language\Text::_($entity);
					}
                    else
					{
						$line_replace[] = $items[$i]['entity'];
					}
					$search[] = $line_search[] = '{item_entity}';
					$replace[] = '';
				}

				if (strpos($text, '{item_title}') !== false)
				{
					$line_replace[] = $items[$i]['title'];
					$search[] = $line_search[] = '{item_title}';
					$replace[] = '';
				}

				if (strpos($text, '{item_desc}') !== false)
				{
					$line_replace[] = $items[$i]['description'];
					$search[] = $line_search[] = '{item_desc}';
					$replace[] = '';
				}

				if (strpos($text, '{item_nr}') !== false)
				{
					$line_replace[] = $items[$i]['pno'];
					$search[] = $line_search[] = '{item_nr}';
					$replace[] = '';
				}

				if (strpos($text, '{item_price}') !== false)
				{
					$line_replace[] = (strlen($items[$i]['title']) > 0 && isset($items[$i]['price']) && $items[$i]['quantity'] != 0) ? \Secretary\Utilities\Number::getNumberFormat($items[$i]['price'], $info['currency']) : '';
					$search[] = $line_search[] = '{item_price}';
					$replace[] = '';
				}

				if (strpos($text, '{item_taxrate}') !== false)
				{
					$line_replace[] = (isset($items[$i]['taxRate'])) ? $items[$i]['taxRate'] : '';
					$search[] = $line_search[] = '{item_taxrate}';
					$replace[] = '';
				}

				if (strpos($text, '{item_taxamount}') !== false)
				{
					if ($info['taxtype'] == 1)
					{
						$taxamount = $items[$i]['total'] - ($items[$i]['total'] / ($items[$i]['taxRate'] / 100 + 1));
					}
					elseif ($info['taxtype'] == 2)
					{
						$taxamount = $items[$i]['price'] * $items[$i]['quantity'] * $items[$i]['taxRate'] / 100;
					}
					$line_replace[] = (strlen($items[$i]['title']) > 0 && $info['taxtype'] > 0 && $items[$i]['quantity'] != 0) ? \Secretary\Utilities\Number::getNumberFormat($taxamount, $info['currency']) : '';
					$search[] = $line_search[] = '{item_taxamount}';
					$replace[] = '';
				}

				if (strpos($text, '{item_total}') !== false)
				{
					$line_replace[] = (strlen($items[$i]['title']) > 0) ? \Secretary\Utilities\Number::getNumberFormat($items[$i]['total'], $info['currency']) : '';
					$search[] = $line_search[] = '{item_total}';
					$replace[] = '';
				}

				// Merge reordered columns
				$item_list[] = str_replace($line_search, $line_replace, $line);
			}
            elseif (isset($items[$i]['id']) && strpos($text, '{item_doc_start}') !== false)
			{
				$line = $documentListShortCuts;
				$line_search = array();
				$line_replace = array();

				if (strpos($text, '{item_doc_nr}') !== false)
				{
					$line_replace[] = $items[$i]['nr'];
					$search[] = $line_search[] = '{item_doc_nr}';
					$replace[] = '';
                }

				if (strpos($text, '{item_doc_title}') !== false)
				{
					$line_replace[] = $items[$i]['title'];
					$search[] = $line_search[] = '{item_doc_title}';
					$replace[] = '';
                }

				if (strpos($text, '{item_doc_subtotal}') !== false)
				{
					$line_replace[] = (!isset($items[$i]['subtotal'])) ? '' : \Secretary\Utilities\Number::getNumberFormat($items[$i]['subtotal'], $info['currency']);
					$search[] = $line_search[] = '{item_doc_subtotal}';
					$replace[] = '';
                }

				if (strpos($text, '{item_doc_tax}') !== false)
				{
					$line_replace[] = (!isset($items[$i]['tax'])) ? '' : $items[$i]['tax'];
					$search[] = $line_search[] = '{item_doc_tax}';
					$replace[] = '';
                }

				if (strpos($text, '{item_doc_total}') !== false)
				{
					$line_replace[] = (!isset($items[$i]['total'])) ? '' : \Secretary\Utilities\Number::getNumberFormat($items[$i]['total'], $info['currency']);
					$search[] = $line_search[] = '{item_doc_total}';
					$replace[] = '';
                }

				if (strpos($text, '{item_doc_created}') !== false)
				{
					if (!isset($items[$i]['created']) || ($items[$i]['created'] == '0000-00-00'))
					{
						$line_replace[] = '';
					}
                    else
					{
						$cDate = new \Joomla\CMS\Date\Date($items[$i]['created']);
						$line_replace[] = $cDate->format("d.m.Y");
					}
					$search[] = $line_search[] = '{item_doc_created}';
					$replace[] = '';
                }

				if (strpos($text, '{item_doc_deadline}') !== false)
				{
					if (!isset($items[$i]['deadline']) || ($items[$i]['deadline'] == '0000-00-00'))
					{
						$line_replace[] = '';
					}
                    else
					{
						$cDate = new \Joomla\CMS\Date\Date($items[$i]['deadline']);
						$line_replace[] = $cDate->format("d.m.Y");
					}
					$search[] = $line_search[] = '{item_doc_deadline}';
					$replace[] = '';
                }

				// Merge reordered columns
				$documents_list[] = str_replace($line_search, $line_replace, $line);
			}
		}

		$search[] = '{item_end}';
		$replace[] = '';
		$search[] = '{item_doc_end}';
		$replace[] = '';

		$text = preg_replace('#(?<={item_start})(.*?)(?={item_end})#ms', '', $text);
		$search[] = '{item_start}';
		$replace[] = implode('', $item_list);

		$text = preg_replace('#(?<={item_doc_start})(.*?)(?={item_doc_end})#ms', '', $text);
		$search[] = '{item_doc_start}';
		$replace[] = implode('', $documents_list);
	}

	public static function getItemShortCutsOrder($found)
	{
		$result = array();

		// Reihenfolge ermitteln
		if (!empty($found))
		{
			$cleanedWS = preg_replace('/\s+/', '', $found);
			$parts = preg_split('/([}])/', $cleanedWS, -1, PREG_SPLIT_DELIM_CAPTURE);
			
            for ($i = 0, $n = count($parts ?? []) - 1; $i < $n; $i += 2)
			{
				if (!empty($parts[$i]))
				{
					$result[] = $parts[$i] . $parts[$i + 1];
                }
			}
			
            if ($parts[$n] != '')
			{
				$result[] = $parts[$n];
			}
		}

		return $result;
	}

	public static function getTemplateInfoFields(array $data)
	{
		$currencySymbol = !empty($data['currency']) ? \Secretary\Database::getQuery('currencies', $data['currency'], 'currency', 'symbol', 'loadResult') : '';

		$categoryP = (isset($data['category'])) ? $data['category'] : \Secretary\Database::getQuery('folders', (int) $data['catid'], 'id', 'fields,title,alias');
		$kontotitle = (empty($categoryP->alias)) ? \Joomla\CMS\Language\Text::_('COM_SECRETARY_DOCUMENT') : \Joomla\CMS\Language\Text::_($categoryP->alias);

		// A document that has not been saved yet carries neither date. Passing
		// null to DateTime used to mean "now"; since PHP 8.1 it is deprecated,
		// so spell the fallback out and keep the previous result.
		$date = new \Joomla\CMS\Date\Date(($data['created'] ?? '') ?: 'now');
		$deadlineDate = new \Joomla\CMS\Date\Date(($data['deadline'] ?? '') ?: 'now');

		$taxtotal = (is_array($data['taxtotal'])) ? json_encode($data['taxtotal']) : $data['taxtotal'];
		$templateInfoFields = array(
			'document-title' => $kontotitle,
			'nr' => $data['nr'],
			'note' => $data['text'],
			'title' => $data['title'],
			'created_by' => $data['created_by'],
			'subtotal' => $data['subtotal'],
			'taxtype' => $data['taxtype'],
			'taxtotal' => $taxtotal,
			'total' => $data['total'],
			'paid' => floatval($data['paid']),
			'total-remaining' => $data['total'] - floatval($data['paid']),
			'currency' => $currencySymbol,
			'rabatt' => $data['rabatt'],
			'deadline' => $deadlineDate->format("d.m.Y"),
			'created' => $date->format("d.m.Y"),
		);

		$subject = (is_array($data['subject'])) ? $data['subject'] : json_decode($data['subject'] ?? '', true);
		$templateInfoFields['connection'] = (isset($subject[7]) && $subject[7] > 0) ? $subject[7] : false;

		if (!empty($data['items']) && ($items = json_decode($data['items'], true)))
		{
			$templateInfoFields['items'] = $items;
		}

		return $templateInfoFields;
	}

	public static function getExtraFields($oldFields, $params = array())
	{

		$extraFields = array();

		// Category fields
		if (!empty($params))
		{
			if (isset($params['fields']) && $catFields = json_decode($params['fields']))
			{
				foreach ($catFields as $field)
				{
					$key = \Secretary\Route::safeURL($field[1]);
					$extraFields[$key] = $field;
				}
			}
            elseif (isset($params['id']) && $params['id'] > 0)
			{
				$folders = \Secretary\Helpers\Connections::getFolderToTemplate($params['id']);
				
                foreach ($folders as $f)
				{
					$fields = \Secretary\Database::getQuery('folders', $f->one, 'id', 'fields', 'loadResult');
					
                    if ($fa = json_decode($fields))
					{
						foreach ($fa as $field)
						{
							$key = \Secretary\Route::safeURL($field[1]);
							$extraFields[$key] = $field;
                        }
                    }
					unset($fields);
                }
			}
		}

		// Business fields
		$business = \Secretary\Application::company();
		
        if (!empty($business['fields']) && $businessFields = json_decode($business['fields']))
		{
			foreach ($businessFields as $field)
			{
				if (is_array($field))
				{
					$key = \Secretary\Route::safeURL($field[1]);
					$extraFields[$key] = $field;
				}
            }
		}

		// Item fields
		if (!empty($oldFields) && $of = json_decode($oldFields))
		{
			foreach ($of as $field)
			{
				if (is_array($field))
				{
					$key = \Secretary\Route::safeURL($field[1]);
					$extraFields[$key] = $field;
				}
			}
		}

		return json_encode($extraFields);
	}

	public static function getPaperTitleFromFormat($format, $withPaper = false)
	{

		$tmp = explode(";", $format);
		$result = "";

		$result = array(210, 297);
		
        if (!empty($tmp))
		{
			$result[0] = intval($tmp[0]);
			$result[1] = intval($tmp[1]);
		}

		if ($withPaper === true)
		{
			$result['p'] = ($tmp[0] > $tmp[1]) ? 'landscape' : 'portrait';
			$result['t'] = (isset(self::$formats[$format])) ? substr(self::$formats[$format], 0, 2) : 'A4';
		}
		
        return $result;
	}
}