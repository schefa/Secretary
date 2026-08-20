<?php

namespace Secretary\PDF;

defined('_JEXEC') or die;

class MPDFStrategy implements IPDFStrategy
{

    public function output($html, $css, $config = array())
    {
        // mpdf/mpdf is installed into its own isolated Composer project under
        // libraries/mpdf-lib rather than libraries/vendor, so its autoloader
        // never touches (and can't clobber) Joomla core's own Composer autoload.
        $autoload = JPATH_LIBRARIES . '/mpdf-lib/vendor/autoload.php';
        
        if (!file_exists($autoload))
		{
            throw new \Exception('mPDF not exists');
        }
        require_once $autoload;

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

        $mpdf->SetHTMLHeader($header, '0');
        $mpdf->SetHTMLFooter($footer, '0');
        $mpdf->SetTitle($config['title']);
        $mpdf->SetDisplayMode('fullpage');

        $mpdf->WriteHTML($css, 1);
        $mpdf->WriteHTML($html, 2);

        if (!empty($config))
		{
            if (isset($config['dpi']))
			{
                $mpdf->dpi = $config['dpi'];
            }
            
            if (isset($config['title']))
			{
                $mpdf->Bookmark($config['title']);
            }
        }

        if (!empty($config['output']))
		{
            $mpdf->Output($config['output'][0], $config['output'][1]);
        }
        else
		{
            $mpdf->Output();
        }
    }
}
