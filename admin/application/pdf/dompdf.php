<?php
/**
 * @version     3.2.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
 */

namespace Secretary\PDF;

// No direct access
defined('_JEXEC') or die;

use Dompdf\Dompdf;
class DomPDFStrategy implements IPDFStrategy {
    
    public function output($html,$css,$config = array()) {

        $header = (isset($config['header']) && strlen($config['header']) > 0) ? '<div id="dompdf_header">'.$config['header'].'</div>' : '';
        $footer = (isset($config['footer']) && strlen($config['footer']) > 0) ? '<div id="dompdf_footer">'.$config['footer'].'</div>' : '';
        $left = isset($config['mleft']) ? $config['mleft'] : 15;
        $right = isset($config['mright']) ? $config['mright'] : 15;
        $top = isset($config['mtop']) ? $config['mtop'] : 10;
        $bottom = isset($config['mbottom']) ? $config['mbottom'] : 10;
        
        $input = '<html><head><meta http-equiv="Content-Type" content="text/html;">
<style type="text/css">
@page{margin-left:'.$left.'mm;margin-right:'.$right.'mm;margin-top:'.$top.'mm;margin-bottom:'.$bottom.'mm;}
    
#dompdf_header { position: fixed; left:0; top: -'.$top.'mm;right:0;height:'.$top.'mm;}
#dompdf_footer { position: fixed; left:0;bottom:0;right:0;}
#dompdf_footer .page:after { content: counter(page, upper-roman); }
'.$css.'</style></head><body>';
        
        $input .= $header.$footer.'<div id="dompdf_content">'. $html .'</div></body></html>';
        
        require_once JPATH_LIBRARIES. '/dompdf/autoload.inc.php';
        
        $dompdf = new Dompdf();
        
        $format = isset($config['format']) ? \Secretary\Helpers\Templates::getPaperTitleFromFormat($config['format'],true) : 'A4';
        $dompdf->setPaper($format['t'],$format['p']);
        
        $dompdf->loadHtml($input);
        $dompdf->render();
        
        $dompdf->add_info('Title',$config['title']);
        
        if(!empty($config['output'])) {
            $output = $dompdf->output();
            file_put_contents($config['output'][0], $output);
            return;
        } else {
            $dompdf->stream('',array('Attachment'=>0));
        }
    }
}