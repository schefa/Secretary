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
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class SecretaryControllerItem extends JControllerForm
{

    protected $app;
	protected $baseurl = null;

	public function __construct($config = array())
	{
		parent::__construct($config);
	
		$this->app 			= \Secretary\Joomla::getApplication();
		$this->extension	= $this->app->input->getCmd('extension', 'status');
		$this->module		= $this->app->input->getCmd('module', 'system');
		$this->baseurl		= 'index.php?option=com_secretary&view=items&extension='.$this->extension;
        $this->view_list = 'items';
        parent::__construct($config);
    }

    public function getModel($name = 'Item', $prefix = 'SecretaryModel', $config = array('ignore_request' => true))
    {
        return Secretary\Model::create($name,$prefix,$config);
    }
    
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
	{
		$append = parent::getRedirectToItemAppend($recordId);
		$append .= '&extension=' . $this->extension;
		if($this->extension == 'status') {
			$append .= '&module=' . $this->module;
		}
		return $append;
	}
	
	protected function getRedirectToListAppend()
	{
		$append = parent::getRedirectToListAppend();
		$append .= '&extension=' . $this->extension;
		return $append;
	}

	public function save($key = NULL, $urlVar = NULL)
	{
		parent::save( );
		$task	= $this->getTask();
		if($task == 'save') {
			if($this->extension == 'settings') {
				$this->setRedirect(JRoute::_('index.php?option=com_secretary&view=dashboard', false));
			} else {
				$this->setRedirect(JRoute::_('index.php?option=com_secretary&view='.$this->view_list.'&extension=' . $this->extension, false));
			}
		}
	}

	public function openFileDownload()
	{ 
		$user	= JFactory::getUser();
		$id		= $this->app->input->getInt('id');
		$canDownload = false;
		
		if($id > 0)
		{
			
			$db = \Secretary\Database::getDBO();
			$item = Secretary\Database::getQuery('uploads', intval($id) ,'id',$db->qn(array('extension','itemID','business','title','folder')));
			$section = Secretary\Application::getSingularSection($item->extension);
				
			// ACL
			$canDownload = $user->authorise('core.show', 'com_secretary.'.$section.'.'.$item->itemID);
			if(!$canDownload) $canDownload = $user->authorise('core.show', 'com_secretary.'. $section );
						
			if(!empty($item->title) && $canDownload) {
				
			    $file = SECRETARY_ADMIN_PATH . '/uploads/'.$item->business .'/'.$item->folder .'/'.$item->title;
				$filename = $item->title;
					
				// Clean	
				while (@ob_end_clean());
				@clearstatcache();
				
				// Header
				header("MIME-Version: 1.0");
				header("Content-Type: ". mime_content_type($file));
				header("Content-Transfer-Encoding: binary");
				header("Content-Disposition: attachment; filename=\"$filename\"");
				header("Accept-Ranges: bytes");
				
				// Cache abstellen
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
				header("Expires: 0");
				header('Pragma: no-cache');
				
				// Leert den Ausgabepuffer 
				flush();
				
				// Ausgabeversuch
				@readfile($file);
				
				// Ende
				exit(0);
				
			} else {
				
				$this->setMessage(JText::_('COM_SECRETARY_PERMISSION_FAILED'), 'error');
				$this->setRedirect(JRoute::_('index.php?option=com_secretary&view=items&extension=uploads', false));
			}
		}
	}
	
	public function openFile()
	{ 
	    $id		= $this->app->input->getInt('id');
		if($id > 0) {
		    $item = Secretary\Database::getQuery('uploads', intval($id) ,'id','business,title,folder');
		    $file = SECRETARY_ADMIN_PATH . '/uploads/'.$item->business .'/'.$item->folder .'/'.$item->title;
			$ext = explode('.', $item->title);
			$fileType = \Secretary\Helpers\Uploads::whatFileType(end($ext));
			
			if($fileType == 'pdf') {
				$this->setRedirect(JRoute::_('index.php?option=com_secretary&view=item&task=openFile&format=pdf&id='.$id, false));
			} elseif($fileType == 'image') {
				
				// Read image path, convert to base64 encoding
				$imageData = base64_encode(file_get_contents($file));
				$src = 'data: '.mime_content_type($file).';base64,'.$imageData;
				
				$class = (!empty($class)) ? 'class="'.$class.'"' : '';
				$width = (!empty($size)) ? 'width="'.$size.'"' : '';
				echo '<img '.$width.' '.$class.' src="' . $src . '">';
				
				$this->app->close();
				
			} else {
				header("Content-Type: ". end($ext));
				header("Content-Disposition: attachment; filename=\"$file\"");
				readfile ( $file);
			}
		}
	} 

}