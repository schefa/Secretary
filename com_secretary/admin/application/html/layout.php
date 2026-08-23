<?php

namespace Secretary\HTML;

require_once SECRETARY_ADMIN_PATH . '/application/HTML.php';


defined('_JEXEC') or die;

class Layout
{

    /**
     * Method to get additional css for template style
     */
    public static function templateCssStyle()
    {
        $params = \Secretary\Application::parameters();
        $value = $params->get('templateColor', 'white');
        $filename = SECRETARY_MEDIA_PATH . '/css/style.' . $value . '.css';
        \Joomla\CMS\Factory::getDocument()->addStyleSheet($filename);
    }

    /**
     * Alert message when current version is obsolete
     *
     * @return string message
     */
    public static function latestVersionMsg()
    {
        if (version_compare(\Secretary\Application::getLatestVersion(), \Secretary\Application::getVersion(), '>'))
		{
            return '<div class="alert alert-info">' . \Joomla\CMS\Language\Text::_('COM_SECRETARY_DOWNLOAD_NEW_VERSION') . '&nbsp;<a href="https://github.com/schefa/Secretary/releases" class="btn btn-info" target="_blank">Download</a></div>';
        }
    }

    /**
     * Footer
     * 
     * @param string $isBackend
     * @return string
     */
    public static function footer($isBackend = false)
    {
        $html = array();
        $html[] = '<div class="secretary-footer software-property-of-schefa text-center">';
        $html[] = '<ul>';
        $html[] = '<li>Powered by <a href="https://github.com/schefa/Secretary" target="_blank">Secretary</a> ' . SECRETARY_VERSION . '</li>';
        $html[] = '<li><a href="https://www.paypal.com/donate/?hosted_button_id=VKN76VER5RSWL" target="_blank">Donate</a></li>';

        if ($isBackend)
		{
            \Joomla\CMS\Factory::getApplication()->getDocument()->getWebAssetManager()
                ->useScript('joomla.dialog-autocreate');

            $html[] = '<li><a href="#" data-joomla-dialog=\'{"popupType": "inline", "src": "#secretary-lastversion-dialog"}\'>Version Check</a></li>';
        }

        $html[] = '</ul>';

        $html[] = '</div>';

        if ($isBackend)
		{
            $html[] = self::lastversion();
        }

        return implode('', $html);
    }

    /**
     * Top Toolbar
     *
     * @return string Top toolbar
     */
    public static function topToolbar()
    {

        $app = \Secretary\Joomla::getApplication();
        $html = array();

        $html[] = '<div class="secretary-topbar-container fullwidth clearfix">';
        $html[] = '<div class="secretary-toggle-sidebar btn btn-default"><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></div>';

        $sidebarToggle    = (int) $app->getUserState('filter.toggleSidebar');

        if ($sidebarToggle == 0)
		{
            $html[] = '<span id="sidebar-angle" class="hide-sidebar"><i class="fa fa-angle-left"></i></span>';
        }
        else
		{
            $html[] = '<span id="sidebar-angle" class="show-sidebar"><i class="fa fa-angle-right"></i></span>';
        }

        $html[] = '<div class="secretary-topbar-business">';
        $html[] = '<div class="topbar-title">' . \Secretary\Application::company()['title'] . '</div>';
        $html[] = '</div>';

        $html[] = '<div class="secretary-topbar-right"><ul>';

        /*$html[] =	'<li class="secretary-template-color"><span class="secretary-template-green"></span>
         <ul>
         <li><a onclick="document.getElementById(\'filter_search\').value=\'\';this.form.submit();">green</a></li>
         <li>red</li>
         </ul>
         </li>';*/
        $html[] = '</ul></div>';

        $html[] =  '</div>';

        return implode("", $html);
    }

    /**
     * Last version dialog
     */
    public static function lastversion()
    {
        $version     = \Secretary\Application::getVersion();
        $lastversion = \Secretary\Application::getLatestVersion();

        $html   = array();
        $html[] = '<template id="secretary-lastversion-dialog">';
        $html[] = '<div class="secretary-modal-top">';
        $html[] = '<button type="button" class="close" onclick="this.closest(\'joomla-dialog\').close();">x</button>';
        $html[] = '<h3>Version Check</h3>';
        $html[] = '</div>';

        $html[] = '<div class="secretary-modal-content fullwidth">';
        $html[] = 'Installed version:&nbsp;';
        $html[] = '<br><strong>' . $version . '</strong>';

        $html[] = '<br><br>Last published version:&nbsp;';
        $html[] = '<br><strong>' . $lastversion . '</strong>';

        if (version_compare($lastversion, $version, '>'))
		{
            $html[] = '<br><br><a href="https://github.com/schefa/Secretary/releases" class="btn btn-primary" target="_blank">Download</a>';
        }

        $html[] = '<hr/>';
        $html[] = '<h3>Impressum</h3>';
        $html[] = '<p>' . \Joomla\CMS\Language\Text::_('COM_SECRETARY_DISCLAIMER') . '</p>';
        $html[] = '</div>';
        $html[] = '</template>';

        return implode('', $html);
    }
}
