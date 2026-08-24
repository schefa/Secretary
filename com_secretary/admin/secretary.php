<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Form\Form;
use Joomla\CMS\HTML\HTMLHelper;

if (!defined('DS'))
{
	define('DS', DIRECTORY_SEPARATOR);
}

if (!defined('SECRETARY_ADMIN_PATH'))
{
	define('SECRETARY_ADMIN_PATH', JPATH_ADMINISTRATOR . '/components/com_secretary');
}
define('SECRETARY_MEDIA_PATH', Uri::root() . 'media/secretary');

// Access 
$app = Factory::getApplication();
$view = $app->input->get('view');
$format = $app->input->get('format');
$layout = $app->input->get('layout');
$task = $app->input->get('task');

if ($app->isClient('administrator') && !$app->getIdentity()->authorise('core.manage', 'com_secretary'))
{
	throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'));
	
	return false;
}

// Dependancies
require_once SECRETARY_ADMIN_PATH . '/application/Secretary.php';
Table::addIncludePath(SECRETARY_ADMIN_PATH . '/models/tables');
Form::addFormPath(SECRETARY_ADMIN_PATH . '/models/forms');

$pdf = Secretary\PDF::getInstance();
define('SECRETARY_VERSION', Secretary\Application::getVersion());
define('COM_SECRETARY_PDF', (null !== $pdf->getStrategy()));

// Head
//
// format=raw fragments (Preview/Email/repetition popups) used to skip this
// whole block, because they only ever got AJAX-injected into an already-
// fully-styled parent admin page. Now that every popup loads as a standalone
// <iframe> document (native Joomla dialogs can't safely inject fetched HTML -
// see the PDF/ajax-sanitizer issue), a raw response IS the whole page as far
// as the browser is concerned, so it needs its own styles/scripts too.
$document = Factory::getDocument();

HTMLHelper::_('jquery.framework');

$document->addScript(SECRETARY_MEDIA_PATH . '/assets/jquery/jquery-ui.min.js?v=' . SECRETARY_VERSION);
$document->addScript(SECRETARY_MEDIA_PATH . '/js/secretary.js?v=' . SECRETARY_VERSION);

if ($layout == 'edit')
{
	HTMLHelper::_('behavior.formvalidator');
	HTMLHelper::_('behavior.keepalive');
}

// Powers every data-joomla-dialog trigger in the component (PDF/email/preview
// popups, batch dialogs).
Factory::getApplication()->getDocument()->getWebAssetManager()->useScript('joomla.dialog-autocreate');

$document->addStyleSheet(SECRETARY_MEDIA_PATH . '/assets/jquery/jquery-ui.css?v=' . SECRETARY_VERSION);
$document->addStyleSheet(SECRETARY_MEDIA_PATH . '/css/secretary.css?v=' . SECRETARY_VERSION);
$document->addStyleSheet(SECRETARY_MEDIA_PATH . '/css/custom.css?v=' . SECRETARY_VERSION);
$document->addStyleSheet(SECRETARY_MEDIA_PATH . '/assets/fontawesome/css/all.min.css');
$document->addStyleSheet(SECRETARY_MEDIA_PATH . '/assets/fontawesome/css/v4-shims.min.css');
\Secretary\Html::_('layout.templateCssStyle');

$title = 'Secretary';

if (!empty($view))
{
	$title .= ' - ' . Text::_('COM_SECRETARY_' . $view);
}
$document->setTitle($title);

// Display
$controller = BaseController::getInstance('Secretary', array('base_path' => SECRETARY_ADMIN_PATH));
$controller->execute($app->input->get('task'));
$controller->redirect();