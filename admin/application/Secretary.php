<?php
/**
 * @version     3.2.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
 */
 
namespace Secretary {
    
    use JFactory;
    use JRegistry;
    
    if(!defined('SECRETARY_ADMIN_PATH')) define('SECRETARY_ADMIN_PATH', JPATH_ADMINISTRATOR .'/components/com_secretary');
    
    // No direct access
    defined('_JEXEC') or die; 
         
    \Secretary\Application::loadFunctionsFromFolder( SECRETARY_ADMIN_PATH.'/application/'); 
    \Secretary\Application::loadFunctionsFromFolder( SECRETARY_ADMIN_PATH.'/application/helpers/');
    \Secretary\Application::loadFunctionsFromFolder( SECRETARY_ADMIN_PATH.'/application/utilities/');
    
    require_once SECRETARY_ADMIN_PATH.'/application/pdf/pdf.php';
    require_once SECRETARY_ADMIN_PATH.'/application/webservice/Webservice.php';
    
    /**
     * PHP doesnt support enumerator
     * 
     * @author schefa
     */
    abstract class DataTypeEnum {
        const String    = "string";
        const Textarea  = "textarea";
        const Clean     = "clean";
        const Integer   = "integer";
        const Float     = "float";
        const Money     = "money";
        const Date      = "date";
        const ImageFile = "imagefile";
    }
    
    class Application {
        
        private static $_params;
        protected static $_company = array();
        public static $version = "";
        public static $lastversion = false;
        
        /**
         * Singular => Database Tables
         */
        public static $sections = array(
            'component'=>'component',
            'system'=>'system',
            'accounting'=>'accountings', // however not the database table!
            'business'=>'businesses',
            'document'=>'documents',
            'folder'=>'folders',
            'item'=>'items',
            'market'=>'markets',
            'message'=>'messages',
            'product'=>'products',
            'reports'=>'reports',
            'location'=>'locations',
            'subject'=>'subjects',
            'time'=>'times',
            'template'=>'templates',
        );
        
        /**
         * Get the current company
         * 
         * @return array company
         */
        public static function company()
        {
            if (empty(self::$_company)) {
                $db		= Database::getDBO();
                $q      = $db->getQuery(true);
                $q->select('*')
                   ->from($db->qn('#__secretary_businesses'))
                    ->where($db->qn('home').'=1');
                
                $db->setQuery($q);
                $company = $db->loadAssoc();
                
                if(!empty($company)) {
                    $company['currencySymbol'] = Database::getQuery('currencies',$company['currency'],'currency','symbol','loadResult');
                }
                
                self::$_company = $company;
            }
            return self::$_company;
        } 
        
        /**
         * Get Secretary configuration settings
         * 
         * @return \JRegistry parameter settings
         */
        public static function parameters()
        {
            if (empty(self::$_params)) {
                $db	= Database::getDBO();
                $q  = $db->getQuery(true);
                $q->select('params')
                ->from($db->qn('#__secretary_settings'))
                ->where($db->qn('id').'=1');
                
                $db->setQuery($q);
                $result = $db->loadResult();
                $return = new JRegistry( json_decode($result, true) );
                self::$_params = $return;
            }
            return self::$_params;
        }
        
        /**
         * Current Secretary version
         * 
         * @return string
         */
        public static function getVersion() {
            if(empty(self::$version)) {
                $xmlPath = SECRETARY_ADMIN_PATH ."/secretary.xml";
                if(file_exists($xmlPath)) {
                    $xml = JFactory::getXML( $xmlPath );
                    self::$version = $xml->version;
                }
            }
            return self::$version;
        } 
        
        /**
         * Get latest version via webservice
         * 
         * @return string latest version
         */
        public static function getLatestVersion() {
            
            $key = 'secretary_version_' . date('Y_m_d');
            $cache = JFactory::getCache('com_secretary','');
            
            $cache->setCaching(1);
            
            if (!($cache->get($key)))
            {
                try {
                    if($xml_obj = simplexml_load_file("https://www.schefa.com/updates/secretary.xml")) {
                        self::$lastversion = (string) $xml_obj->update->version;
                        $cache->store(self::$lastversion, $key, "com_secretary");
                    }
                } catch(\Exception $e) {
                    throw new $e->getMessage();
                }
            }
            
            self::$lastversion = $cache->get($key);
            return self::$lastversion;
        }
        
        /**
         * Translates the name of a view into the singular section title (e.g. documents => document)
         * Useful for ACL 
         * 
         * @param string $view
         * @return string section
         */
        public static function getSingularSection($view) {
            $result = NULL;
            if(isset($view)) {
                if(isset(self::$sections[(string) $view])) {
                    $result = $view;
                } elseif($key = array_search($view,self::$sections)) {
                    $result = $key;
                }
            }
            return $result;
        }
        
        /**
         * Loads PHP Classes
         * 
         * @param string $folder name of the folder where the files are
         */
        public static function loadFunctionsFromFolder($folder) {
            if (is_dir($folder) && $handle = opendir($folder)) {
                while (false !== ($entry = readdir($handle))) {
                    if ($entry != "." && $entry != ".." && strpos($entry,'.php') !== false ) {
                        require_once $folder . $entry;
                    }
                }
                closedir($handle);
            }
        }
    }
}