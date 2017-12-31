<?php
/**
 * @version     3.2.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
 */
 
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class SecretaryModelItems extends JModelList
{

    protected $app;
    private $extension;
    private $business;
    private $tableType;
    
    /**
     * Class constructor
     * 
     * @param array $config
     */
    public function __construct($config = array())
	{
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'a.id',
                'title', 'a.title',
                'desc', 'a.desc',
            );
        }
        
		$this->business     = \Secretary\Application::company();
		$this->app 			= \Secretary\Joomla::getApplication();
		$this->extension	= $this->app->input->getCmd('extension', 'status');
		$this->tableType    = (string) $this->extension;
		
		if(!in_array($this->extension, array('activities','currencies','entities','fields','status','uploads'))) {
		    throw new Exception('Extension not found',404);
		    return false;
		}
		
        parent::__construct($config);
    }
	
    /**
     * {@inheritDoc}
     * @see \Joomla\CMS\MVC\Model\ListModel::populateState()
     */
    protected function populateState($ordering = null, $direction = null)
    {       
        $limit = $this->getUserStateFromRequest($this->context . '.list.limit', 'limit', $this->app->get('list_limit'), 'int');
        $this->setState('list.limit', $limit);
        
        $order = 'a.title';
        if ($this->extension == 'status' || $this->extension == 'fields') {
            $section = $this->app->getUserStateFromRequest($this->context . '.filter.section', 'filter_section');
            $this->setState('filter.section', $section);
            
            if($this->extension == 'status') $order = 'a.ordering';
        } elseif ($this->extension == 'settings') {
            $order = 'a.id';
        }
        
        $params = Secretary\Application::parameters();
        $this->setState('params', $params);
        
        parent::populateState($order, 'asc');  
    }
	
    /**
     * {@inheritDoc}
     * @see \Joomla\CMS\MVC\Model\ListModel::getListQuery()
     */
    protected function getListQuery()
	{
        $db		= $this->getDbo();
        $query	= $db->getQuery(true);
		
		$query->select( $this->getState( 'list.select', 'a.*' ) )
				->from($db->quoteName('#__secretary_'.$db->escape($this->tableType),'a'));
				
		if ($this->extension == 'accounts') {
			$query->select('s.state AS typ')
				->leftJoin($db->quoteName('#__secretary_accounts_system','s').' ON s.id = a.kid');
			$query->where('a.year = '. (int) date('Y'));
		}
		
		$section = $this->getState('filter.section');
		if (!empty($section) && $section !== 'system' && ($this->extension == 'status' || $this->extension == 'fields'))
		    $query->where('a.extension = ' . $db->quote($section) );
		
		$orderCol = $this->getState('list.ordering'); 
		if ($orderCol) {
		    $orderBY = $orderCol . ' ASC';
		    if($this->extension == 'status') $orderBY .= ',a.extension,a.title';
		    
			$query->order($db->escape($orderBY));
		}
		
        return $query;
    }
	
    /**
     * Method to get extra email files which were created automatically 
     * 
     * @return stdClass[] files
     */
    public function getEmailFiles()
	{
        $result    = array();
        $path      = SECRETARY_ADMIN_PATH.'/uploads/'.$this->business['id'].'/emails/';
	    if (is_dir($path))  {
    	    $files = array_diff(scandir($path), array('.', '..'));
            foreach($files as $k => $file) {
                $parts = preg_split( "/(-|\.)/", $file );
                $item = new stdClass();
                $item->file = $this->business['id'].'/emails/'.$file;
                $item->document = Secretary\Database::getQuery('documents',$parts[1],'createdEntry','id,nr');
                $item->title = $file;
                $result[$k] = $item;
            }    
	    }
	    
	    return $result;
    }
	
}
