<?php
/**
 * @version     3.0.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
 */

namespace Secretary\PDF;

use mPDF;

// No direct access
defined('_JEXEC') or die;

class MPDFStrategy implements IPDFStrategy
{

    public function output($html,$css,$config = array())
    {
        
        $pdfParameter = \Secretary\Application::parameters()->get('pdf');
        if(isset($pdfParameter)) {
            switch ($pdfParameter) {
                default: case 'mpdf':
                    $this->mpdf6($html,$css,$config);
                    break;
                    
                case 'mpdf7':
                    $this->mpdf7($html,$css,$config);
                    break; 
            }
        }
    }
    
    private function mpdf7($html,$css,$config = array())
    {
        require_once JPATH_LIBRARIES . '/vendor/autoload.php';
        
        
        $header = isset($config['header']) ? $config['header'] : '';
        $footer = isset($config['footer']) ? $config['footer'] : '';
        $format = isset($config['format']) ? \Secretary\Helpers\Templates::getPaperTitleFromFormat($config['format']) : 'A4';
        
        // Margins
        $left = isset($config['mleft']) ? $config['mleft'] : 15;
        $right = isset($config['mright']) ? $config['mright'] : 15;
        $top = isset($config['mtop']) ? $config['mtop'] : 10;
        $bottom = isset($config['mbottom']) ? $config['mbottom'] : 10; 
        
        $mpdf = new \Mpdf\Mpdf([ 
            'format' => $format,
            'margin-top' => $top,
        ]); 
        
        $mpdf->SetHTMLHeader($header,'0');
        $mpdf->SetHTMLFooter($footer,'0');
        $mpdf->SetTitle($config['title']);
        $mpdf->SetDisplayMode('fullpage');
        
        $mpdf->WriteHTML($css,1);
        $mpdf->WriteHTML($html,2);
        
        if(!empty($config)) {
            if(isset($config['dpi'])) {
                $mpdf->dpi = $config['dpi'];
            }
            if(isset($config['title'])) {
                $mpdf->Bookmark($config['title']);
            }
        }
        
        if(!empty($config['output'])) {
            $mpdf->Output($config['output'][0], $config['output'][1]);
        } else {
            $mpdf->Output();
        }
    }
    
    private function mpdf6($html,$css,$config = array())
    {
        $libPath = JPATH_LIBRARIES. '/mpdf/mpdf.php'; 
    
        if(file_exists($libPath)) {
            require_once $libPath;
        } else {
            throw new \Exception('mPDF not exists');
            return false;
        }
        
        $header = isset($config['header']) ? $config['header'] : '';
        $footer = isset($config['footer']) ? $config['footer'] : '';
        $format = isset($config['format']) ? \Secretary\Helpers\Templates::getPaperTitleFromFormat($config['format']) : 'A4';
        
        // Margins
        $left = isset($config['mleft']) ? $config['mleft'] : 15;
        $right = isset($config['mright']) ? $config['mright'] : 15;
        $top = isset($config['mtop']) ? $config['mtop'] : 10;
        $bottom = isset($config['mbottom']) ? $config['mbottom'] : 10; 

        $mpdf = new \mPDF('',$format,'','',$left,$right,$top,$bottom,0,0);
        
        $mpdf->setAutoBottomMargin = true;
        $mpdf->setAutoTopMargin = true;
        
        $mpdf->SetHTMLHeader($header,'0');
        $mpdf->SetHTMLFooter($footer,'0');
        $mpdf->SetTitle($config['title']);
        $mpdf->SetDisplayMode('fullpage');
        
        $mpdf->WriteHTML($css,1);
        $mpdf->WriteHTML($html,2);
        
        if(!empty($config)) {
            if(isset($config['dpi'])) {
                $mpdf->dpi = $config['dpi'];
            }
            if(isset($config['title'])) {
                $mpdf->Bookmark($config['title']);
            }
        }
        
        if(!empty($config['output'])) {
            $mpdf->Output($config['output'][0], $config['output'][1]);
        } else {
            $mpdf->Output();
        }
    }
}