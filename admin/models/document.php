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

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

class SecretaryModelDocument extends JModelAdmin
{
	protected static $_item = array();

	protected $business;
	protected $catid;
	protected $locationId;
	protected $productId;
	protected $productUsage;
	protected $subject;
	protected $subjectIds;
	protected $fileId;
	protected $time_id;
	protected $text_prefix = 'com_secretary';

	private $_uploadFile = array();

	/**
	 * Class constructor
	 * 
	 * @param array $config
	 */
	public function __construct($config = array())
	{
		$jinput = \Secretary\Joomla::getApplication()->input;
		$this->catid = $jinput->getInt('catid');
		$this->locationId = $jinput->getInt('location');

		$this->fileId = $jinput->getInt('secf');
		$this->time_id = $jinput->getInt('tid');
		$this->productUsage = $jinput->getInt('pusage');

		$this->subject = $jinput->getVar('subject');
		$this->subjectIds = (!empty($this->subject)) ? json_decode($this->subject) : null;

		$pIds = $jinput->getVar('pid');
		$this->productIds = (!empty($pIds)) ? (array_map('intval', explode(',', $pIds))) : null;

		$this->business = Secretary\Application::company();
		parent::__construct($config);
	}

	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\AdminModel::populateState()
	 */
	protected function populateState()
	{
		$pk = \Secretary\Joomla::getApplication()->input->getInt('id');
		$this->setState($this->getName() . '.id', $pk);

		$params = Secretary\Application::parameters();
		$this->setState('params', $params);
	}

	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\AdminModel::canDelete()
	 */
	protected function canDelete($record)
	{
		return \Secretary\Helpers\Access::canDelete($record, 'document');
	}

	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\BaseDatabaseModel::getTable()
	 */
	public function getTable($type = 'Document', $prefix = 'SecretaryTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\FormModel::getForm()
	 */
	public function getForm($data = array(), $loadData = true)
	{
		$form = $this->loadForm('com_secretary.document', 'document', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
			return false;
		return $form;
	}

	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\FormModel::loadFormData()
	 */
	protected function loadFormData()
	{
		$data = \Secretary\Joomla::getApplication()->getUserState('com_secretary.edit.document.data', array());
		if (empty($data)) {
			$data = $this->getItem();

			if (!isset($data->currency))
				$data->currency = $this->business['currency'];
			$data->title = Secretary\Utilities\Text::prepareTextarea($data->title);
			$data->text = Secretary\Utilities::cleaner($data->text, true);
			$data->text = Secretary\Utilities\Text::prepareTextarea($data->text);

			// Nr
			$data->nr = Secretary\Utilities::cleaner($data->nr, true);
			if (empty($data->nr) && !empty($data->category->number)) {
				$cntDocs = \Secretary\Helpers\Folders::countCategoryEntries('documents', $data->catid) + 1;
				$startCnt = 0;
				$match = array();
				preg_match('#\{CNT([^}]*)\}#siU', $data->category->number, $match);
				if (!empty($match)) {
					if (!empty($match[1]) && strpos($match[1], 'start=') !== false) {
						$startCnt = substr($match[1], 7);
					}
				}
				$cntDocs += $startCnt;

				$data->nr = preg_replace('#\{CNT([^}]*)\}#siU', $cntDocs, $data->category->number);

				$NrExists = 1;
				while (!empty($NrExists)) {
					$NrExists = \Secretary\Helpers\Documents::getDoubleCategoryNumber($data->nr, $data->catid);
					if (empty($NrExists)) {
						break;
					} else {
						$data->nr += 1;
					}
				}
			}

			// Subject
			if (!empty($data->subject) && !is_array($data->subject)) {
				$data->subject = (array) json_decode($data->subject);
				foreach ($data->subject as $x => $row)
					$data->subject[$x] = Secretary\Utilities::cleaner($row, true);
			}

			// Produkte
			if (!empty($data->items) && ($products = json_decode($data->items, true))) {

				foreach ($products as $no => $product) {
					foreach ($product as $key => $val) {
						$products[$no][$key] = Secretary\Utilities::cleaner($val, true);
					}
				}
				$data->items = json_encode($products, true);
			}
		}
		return $data;
	}

	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\AdminModel::getItem()
	 */
	public function getItem($pk = null)
	{
		if (empty(self::$_item[$pk]) && ($item = parent::getItem($pk))) {

			// Create document from an existing upload
			if (empty($item->upload) && !empty($this->fileId))
				$item->upload = \Secretary\Helpers\Uploads::checkEmptyFileId($this->fileId);

			if (!isset($item->taxtype))
				$item->taxtype = $this->business['taxPrepo'];

			if (empty($item->tax) || !is_float($item->tax))
				$item->tax = $this->business['taxvalue'];

			// Taxes
			$item->taxtotal_sum = 0;
			if ($taxtotals = json_decode($item->taxtotal)) {
				$taxtotal = array();
				if (is_numeric($taxtotals))
					$taxtotals = array($taxtotals);
				foreach ($taxtotals as $tax)
					$taxtotal[] = round($tax, 2);
				$item->taxtotal_sum = array_sum($taxtotal);
				if ((round($item->subtotal, 2) + array_sum($taxtotal)) < $item->total)
					$item->subtotal = round($item->total, 2) - array_sum($taxtotal);
			}

			$item->rabatt = (empty($item->rabatt)) ? 0 : $item->rabatt;
			$item->catid = (empty($item->catid)) ? $this->catid : $item->catid;
			$item->office = (empty($item->locationId)) ? $this->locationId : $item->office;

			// Currency
			if (!isset($item->currency) && !empty($this->locationId)) {
				$item->currency = Secretary\Database::getQuery('locations', (int) $this->locationId, 'id', 'currency', 'loadResult');
			} elseif (!isset($item->currency)) {
				$item->currency = $this->business['currency'];
			}
			$item->currencySymbol = !empty($item->currency) ? Secretary\Database::getQuery('currencies', $item->currency, 'currency', 'symbol', 'loadResult') : '';

			// Contact
			if (count($this->subjectIds ?? []) == 1)
				$this->subject = $this->subjectIds[0];
			if (empty($item->subjectid) && is_numeric($this->subject)) {
				$item->subjectid = (!empty($this->subject)) ? $this->subject : '';
				$item->subject = \Secretary\Helpers\Subjects::convertIDtoJSON($item->subjectid);
			}
			if (!is_array($item->subject))
				$item->subject = json_decode($item->subject, true);

			$item->category = Secretary\Database::getQuery('folders', intval($item->catid), 'id', 'number,alias,description,fields');

			// Default Text
			if ($item->id < 1) {
				if (!empty($item->category->description)) {
					$item->text = $item->category->description;
				} elseif (empty($item->text)) {
					$item->text = $this->business['defaultNote'];
				}
			}

			$item->message = array();
			$emailTemplateId = NULL;

			if (empty($item->createdEntry))
				$item->createdEntry = time();

			// Data fields	
			if (!empty($item->fields)) {
				$fields = json_decode($item->fields, true);
				if (isset($fields['pUsage'])) {
					$item->productUsage = $fields['pUsage'];
					unset($fields['pUsage']);
				}
				if (isset($fields['message'])) {
					$item->message = $fields['message'];
					unset($fields['message']);
				}
				if (isset($fields['repetition'])) {
					$item->repetition = $fields['repetition'];
					unset($fields['repetition']);
				}
				$item->fields = \Secretary\Helpers\Items::rebuildFieldsForDocument($fields);
			}

			// Neues Dokument: Vererbung von Datenfeldern aus der Kategorie
			if (empty($item->id)) {
				if (!empty($item->category->fields) && $catFields = json_decode($item->category->fields)) {
					$searchArray = array('docsSoll', 'docsHaben', 'docsSollTax', 'docsHabenTax');
					$newFields = array();
					foreach ($catFields as $key => $value) {
						if ('template' === $value[3] && empty($item->template)) {
							$item->template = $value[2];
						} else if ('pUsage' === $value[3] && empty($item->productUsage)) {
							$item->productUsage = $value[2];
						} else if ('emailtemplate' === $value[3] && empty($item->message)) {
							$emailTemplateId = (int) $value[2];
						} elseif (!empty(array_intersect($searchArray, $value))) {
						} else {
							$newFields[$key] = $value;
						}
					}
					$item->fields = \Secretary\Helpers\Items::rebuildFieldsForDocument($newFields);
				}
			}

			if (empty($item->template)) {
				$item->template = 0;
			}

			if (!empty($this->productUsage)) {
				$item->productUsage = $this->productUsage;
			}

			if (empty($item->items) && !empty($this->productIds)) {
				// Buy and sell products
				$products = array();
				foreach ($this->productIds as $productId) {
					$productData = Secretary\Database::getQuery('products', (int) $productId, 'id', '0 as quantity,0 as total,title,description,entity,taxRate,priceCost,priceSale', 'loadAssoc');
					if (!empty($productData)) {
						$productData['price'] = ($item->productUsage == 2) ? $productData['priceCost'] : $productData['priceSale'];
						$products[] = $productData;
					}
				}
				$item->items = json_encode($products, true);
			} elseif (empty($item->items) && !empty($this->time_id)) {

				$projectTitle = Secretary\Database::getQuery('times', $this->time_id, 'id', 'title', 'loadResult');
				$item->title = $projectTitle;

				// Projektaufgaben als Positionen
				$projectTasks = \Secretary\Helpers\Times::getProjectTasks($this->time_id);
				$products = array();
				foreach ($projectTasks as $task) {
					if (isset($task->title)) {

						$product = array();
						$product['quantity'] = round($task->totaltime / 3600, 1);
						$product['total'] = 0;
						$product['title'] = $task->title;
						$product['description'] = (isset($task->description)) ? $task->description : '';
						$product['taxRate'] = 0;
						$product['entity'] = 'h';
						$product['price'] = 0;

						$products[] = $product;
					}
				}
				$item->items = json_encode($products, true);
			}

			if (!isset($item->productUsage)) {
				$item->productUsage = 0;
			}
			$item->title = stripslashes($item->title);
			$item->text = stripslashes($item->text);
			$item->document_title = (empty($item->category->alias)) ? JText::_('COM_SECRETARY_DOCUMENT') : JText::_($item->category->alias);
			if ($item->deadline == '0000-00-00')
				$item->deadline = NULL;

			$item->templateInfoFields = \Secretary\Helpers\Templates::getTemplateInfoFields((array) $item);
			$item->templateInfoFields['fields'] = \Secretary\Helpers\Templates::getExtraFields($item->fields, array('fields' => $this->getNewFields((array) $item)));

			// Email Template zuletzt wegen Betreff 
			if (empty($item->message) && ($emailTemplateId > 0)) {
				$emailTemplate = Secretary\Database::getQuery('templates', (int) $emailTemplateId);
				if (!empty($emailTemplate)) {
					$item->message['subject'] = \Secretary\Helpers\Templates::transformText($emailTemplate->title, array('subject' => $item->subjectid), $item->templateInfoFields);
					$item->message['template'] = (int) $emailTemplateId;
				}
			}
			if (!isset($item->message['template']))
				$item->message['template'] = 0;

			self::$_item[$pk] = $item;
		}

		return self::$_item[$pk];
	}

	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\AdminModel::save()
	 */
	public function save($data)
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$user = \Secretary\Joomla::getUser();
		$table = $this->getTable();
		$pk = (!empty($data['id'])) ? $data['id'] : (int) $this->getState($this->getName() . '.id');
		$this->_uploadFile = \Secretary\Joomla::getApplication()->input->files->get('jform');

		// Access
		if (!(\Secretary\Helpers\Access::checkAdmin())) {
			if (!$user->authorise('core.create', 'com_secretary.document') || ($pk > 0 && !$user->authorise('core.edit.own', 'com_secretary.document.' . $pk))) {
				throw new Exception(JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
				return false;
			}
		}

		try {
			// Existing record
			if ($pk > 0) {
				$table->load($pk);
			}

			// Multiple subjects
			if (!empty($this->subjectIds)) {
				foreach ($this->subjectIds as $x => $subjectId) {
					$data['subject'] = \Secretary\Helpers\Subjects::convertIDtoJSON($subjectId);
					$data['subjectid'] = $subjectId;

					if (!$this->_save($pk, $user, $table, $data)) {
						return false;
					}
				}
			} else {
				if (!$this->_save($pk, $user, $table, $data)) {
					return false;
				}
			}
		} catch (Exception $e) {
			$this->setError($e->getMessage());
			return false;
		}

		$pkName = $table->getKeyName();

		if (isset($table->$pkName)) {
			$this->setState($this->getName() . '.id', $table->$pkName);
		}

		$this->cleanCache();
		return true;
	}

	private function _save(&$pk, &$user, &$table, &$data)
	{

		$table->prepareStore($data);

		if (!$table->bind($data)) {
			$this->setError($table->getError());
			return false;
		}
		if (!$table->check()) {
			$this->setError($table->getError());
			return false;
		}
		if (!$table->store()) {
			$this->setError($table->getError());
			return false;
		}

		// Done // get new Id
		$newID = (int) $table->id;

		// Dokument als Email Anhang speichern
		if (COM_SECRETARY_PDF && !empty($data['subject'][5])) {
			$this->_createPDF($data);
		}

		if (!empty($this->fileId)) {
			// create document from file
			// ToDo : Unternehmensunabhängig
			$fileId = \Secretary\Helpers\Uploads::checkEmptyFileId($this->fileId);
			if ($fileId > 0) {
				\Secretary\Helpers\Uploads::connectFileWithSheet($newID, $fileId, 'documents');
			}
		} elseif (empty($this->fileId) && $user->authorise('core.upload', 'com_secretary')) {
			// Upload File
			$uploadTitle = (isset($data['upload_title'])) ? $data['upload_title'] : NULL;
			\Secretary\Helpers\Uploads::upload('document', 'documents', $uploadTitle, $newID, $this->_uploadFile);
		}

		// Activity
		\Secretary\Helpers\Activity::set('documents', ($pk > 0) ? 'edited' : 'created', $data['catid'], $newID);

		return true;
	}

	private function _createPDF($data)
	{

		$defaultTemplate = \Secretary\Helpers\Templates::getTemplate($data['template']);

		if (empty($defaultTemplate))
			return false;

		$templateInfoFields = \Secretary\Helpers\Templates::getTemplateInfoFields($data);
		$templateInfoFields['fields'] = \Secretary\Helpers\Templates::getExtraFields($data['fields'], array('fields' => $this->getNewFields($data)));

		$body = \Secretary\Helpers\Templates::transformText($defaultTemplate->text, array('subject' => $data['subjectid']), $templateInfoFields);
		$header = \Secretary\Helpers\Templates::transformText($defaultTemplate->header, array('subject' => $data['subjectid']), $templateInfoFields);
		$footer = \Secretary\Helpers\Templates::transformText($defaultTemplate->footer, array('subject' => $data['subjectid']), $templateInfoFields);

		$filename = strtolower($templateInfoFields['document-title']) . '-' . $data['createdEntry'] . '.pdf';
		$path = SECRETARY_ADMIN_PATH . '/uploads/' . $this->business['id'] . '/emails/';
		//create folder if not exists
		jimport('joomla.filesystem.folder');
		if (!JFolder::exists($path)) {
			JFolder::create($path, 0755);
		}

		jimport('joomla.filesystem.file');
		if (JFile::exists($path . $filename)) {
			JFile::delete($path . $filename);
		}

		$config = array('title' => $templateInfoFields['document-title'], 'output' => array($path . $filename, 'F'), 'dpi' => $defaultTemplate->dpi, 'format' => $defaultTemplate->format, 'header' => $header, 'footer' => $footer, 'margins' => $defaultTemplate->margins);

		// Get Strategy
		require_once SECRETARY_ADMIN_PATH . '/application/pdf/pdf.php';
		$pdf = Secretary\PDF::getInstance();
		$pdf->execute($body, $defaultTemplate->css, $config);
	}

	private function getNewFields(array $item)
	{

		$newFields = array();
		$contact_fields = Secretary\Database::getQuery('subjects', intval($item['subjectid']), 'id', 'fields', 'loadResult');

		if (($item['id'] <= 0) && isset($item['category']) && $cf = json_decode($item['category']->fields, true)) {
			$catFields = $cf;
		} else {
			$catFields = array();
		}

		if ($cf = json_decode($contact_fields, true)) {
			$contactFields = $cf;
		} else {
			$contactFields = array();
		}
		$newFields = array_merge($catFields, $contactFields);

		return json_encode($newFields);
	}

	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\AdminModel::batch()
	 */
	public function batch($commands, $pks, $contexts)
	{
		\Secretary\Helpers\Batch::batch('documents', $commands, $pks, $contexts);
		$this->cleanCache();
		return true;
	}

	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\BaseDatabaseModel::cleanCache()
	 */
	protected function cleanCache($group = null, $client_id = 0)
	{
		parent::cleanCache('com_secretary');
	}
}