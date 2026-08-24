<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


class SecretaryModelItems extends \Joomla\CMS\MVC\Model\ListModel
{

    protected $app;
    private $extension;
    private $business;
    private $tableType;
    private $emailFilesTotal = 0;

    /**
     * Class constructor
     * 
     * @param array $config
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields']))
		{
            $config['filter_fields'] = array(
                'id',
                'a.id',
                'title',
                'a.title',
                'desc',
                'a.desc',
            );
        }

        $this->business = \Secretary\Application::company();
        $this->app = \Secretary\Joomla::getApplication();
        $this->extension = $this->app->input->getCmd('extension', 'status');
        $this->tableType = (string) $this->extension;

        if (!in_array($this->extension, array('activities', 'currencies', 'entities', 'fields', 'plugins', 'settings', 'status', 'uploads')))
		{
            throw new Exception('Extension not found', 404);
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
        
        if ($this->extension == 'status' || $this->extension == 'fields')
		{
            $section = $this->app->getUserStateFromRequest($this->context . '.filter.section', 'filter_section');
            $this->setState('filter.section', $section);

            if ($this->extension == 'status')
            {
                $order = 'a.ordering';
            }
        }
        elseif ($this->extension == 'settings')
		{
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
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        $query->select($this->getState('list.select', 'a.*'))
            ->from($db->quoteName('#__secretary_' . $db->escape($this->tableType), 'a'));

        $section = $this->getState('filter.section');
        
        if (!empty($section) && $section !== 'system' && ($this->extension == 'status' || $this->extension == 'fields'))
        {
            $query->where('a.extension = ' . $db->quote($section));
        }

        $orderCol = $this->getState('list.ordering');
        
        if ($orderCol)
		{
            $orderBY = $orderCol . ' ASC';
            
            if ($this->extension == 'status')
            {
                $orderBY .= ',a.extension,a.title';
            }

            $query->order($db->escape($orderBY));
        }

        return $query;
    }

    /**
     * Method to list the plugins shipped with this component
     *
     * @return stdClass[] plugins
     */
    public function getPlugins()
    {
        $result = array();
        $path = JPATH_PLUGINS . '/secretary';

        if (is_dir($path))
		{
            $folders = array_diff(scandir($path), array('.', '..'));
            
            foreach ($folders as $element)
			{
                $manifest = $path . '/' . $element . '/' . $element . '.xml';
                
                if (!is_file($manifest) || !($xml = simplexml_load_file($manifest)))
				{
                    continue;
                }

                $item = new stdClass;
                $item->name = 'plg_secretary_' . $element;
                $item->version = (string) $xml->version;
                $item->author = (string) $xml->author;
                $result[] = $item;
            }
        }

        return $result;
    }

    /**
     * Method to get extra email files which were created automatically
     *
     * Paginated via the filestart/filelimit request vars (kept separate from the
     * main list's limit/limitstart so the two independent tables on this page
     * don't fight over the same request vars). Slicing happens on the plain
     * filenames before the per-file document lookup below runs, so only the
     * files on the current page pay for that query - with several thousand
     * auto-generated files this previously ran one query per file on every
     * page load regardless of how many were actually shown.
     *
     * @return stdClass[] files
     */
    public function getEmailFiles()
    {
        $limit = $this->app->input->getInt('filelimit', 25);
        $limit = ($limit > 0) ? $limit : 25;
        $start = max(0, $this->app->input->getInt('filestart', 0));

        $result = array();
        $path = SECRETARY_ADMIN_PATH . '/uploads/' . $this->business['id'] . '/emails/';
        
        if (is_dir($path))
		{
            $files = array_values(array_diff(scandir($path), array('.', '..')));
            $this->emailFilesTotal = count($files);

            foreach (array_slice($files, $start, $limit) as $k => $file)
			{
                $parts = preg_split("/(-|\.)/", $file);
                $item = new stdClass;
                $item->file = $this->business['id'] . '/emails/' . $file;
                $item->document = Secretary\Database::getQuery('documents', $parts[1], 'createdEntry', 'id,nr');
                $item->title = $file;
                $result[$k] = $item;
            }
        }

        return $result;
    }

    /**
     * Total count of auto-generated email files, for the getEmailFiles() pager.
     *
     * @return int
     */
    public function getEmailFilesTotal()
    {
        $this->get('EmailFiles');
        
        return $this->emailFilesTotal;
    }
}