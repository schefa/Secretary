<?php
/**
 * @version     3.0.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      GNU General Public License version 2 or later.
 */

namespace Secretary;

// No direct access
defined('_JEXEC') or die;

use Secretary\PDF\DomPDFStrategy;
use Secretary\PDF\MPDFStrategy;

class PDF
{
	
    private static $instance;
    private $_strategy;
    
    /**
     * Instance. Only open one object at the time
     * 
     * @return this
     */
    public static function getInstance() {
        if(null === static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }
    
    public function setStrategy(PDF\IPDFStrategy $strategy) {
        $this->_strategy = $strategy;
    }
    
    public function getStrategy() {
        if($this->_strategy === null) {
            $pdfParameter = Application::parameters()->get('pdf');
            if(isset($pdfParameter)) {
                switch ($pdfParameter) {
                    case 'mpdf': case 'mpdf7':
                        require_once SECRETARY_ADMIN_PATH . '/application/pdf/mpdf.php';
                        return new MPDFStrategy();
                        break; 
                    case 'dompdf': 
                        require_once SECRETARY_ADMIN_PATH . '/application/pdf/dompdf.php';
                        return new DomPDFStrategy();
                        break;
                }
            } 
        }
        return $this->_strategy;
    }
    
    public function execute($html,$css, $config = array()) {
        $this->_strategy = $this->getStrategy();
        if($this->_strategy !== null) {
            $this->setStrategy($this->_strategy);
        } else {
            throw new \Exception("No PDF library selected or installed");
            return false;
        }

        if(isset($config['margins']) && 
            ((is_string($config['margins']) && strlen($config['margins']) > 0) || is_array($config['margins'])))
        {
            if(is_string($config['margins']))
                $margins = explode(";",$config['margins']);
            else 
                $margins = $config['margins'];
            
            $config['mleft'] = isset($margins[0]) ? $margins[0] : 15;
            $config['mright'] = isset($margins[1]) ? $margins[1] : 15;
            $config['mtop'] = isset($margins[2]) ? $margins[2] : 10;
            $config['mbottom'] = isset($margins[3]) ? $margins[3] : 10;
        }
        
        ob_clean();
            $this->_strategy->output($html,$css,$config);
        ob_end_flush();
    }

}

namespace Secretary\PDF;

interface IPDFStrategy {
    public function output($html, $css, $config);
}
