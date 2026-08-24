<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Secretary\HTML;

defined('_JEXEC') or die;

require_once SECRETARY_ADMIN_PATH . '/application/HTML.php';

use Joomla\CMS\Factory;
use Joomla\CMS\Session\Session;
use Joomla\CMS\HTML\HTMLHelper;

class Search
{

	public static function contacts($class = NULL)
	{
		Factory::getApplication()->getDocument()->getWebAssetManager()
			->useScript('joomla.dialog-autocreate');

		// Build the script.
		$script = array();

		// Select button script
		$script[] = '  function removeOptions(selectbox) { var i; for(i=selectbox.options.length-1;i>=0;i--) { selectbox.remove(i); } }; ';
		$script[] = ' function jSelectUser( name, street, zip, location, phone, email, gender, id, connections) {';
		$script[] = '		document.getElementById("jform_subject_name").value = name;';
		$script[] = '		document.getElementById("jform_subject_street").value = street;';
		$script[] = '		document.getElementById("jform_subject_zip").value = zip ;';
		$script[] = '		document.getElementById("jform_subject_location").value = location;';
		$script[] = '		document.getElementById("jform_subject_phone").value = phone;';
		$script[] = '		document.getElementById("jform_subject_email").value = email;';
		$script[] = '		document.getElementById("jform_subject_gender").value = gender;';
		$script[] = '		document.getElementById("jform_subjectid").value = id;
                		    
                            document.getElementById("display_contact_name").style.display = "block";
                            document.getElementById("jform_subject_name").style.display = "none";
                            document.getElementById("contact_name").innerHTML = name; 
		
                		    var connectionDropdown =  document.getElementById("jform_subject_connection");
                		    removeOptions(connectionDropdown);
                		    
		                    var done = 0;
                            for(var i in connections)
                            {
                                if(connections.hasOwnProperty(i) && typeof(connections[i].id) !== "undefined") {
                                   var texti = (typeof(connections[i].note) !== "undefined") ? connections[i].fullname + " ("+connections[i].note+")" : connections[i].fullname;
                                   var opt = document.createElement("option");
                                
                                   opt.value= connections[i].id;
                                   opt.innerHTML = texti;
		    
                                   connectionDropdown.appendChild(opt);
		                           done++;
                                }
                            }
                			if(done < 1) {
                				connectionDropdown.parentNode.style.display="none";
                			} else  {
                				connectionDropdown.parentNode.style.display="block";
	                        }';
		$script[] = '		if (Joomla.Modal.getCurrent()) { Joomla.Modal.getCurrent().close(); }';
		$script[] = '	}';

		// Add the script to the document head.
		Factory::getDocument()->addScriptDeclaration(implode("\n", $script));

		$link	= 'index.php?option=com_secretary&amp;view=subjects&amp;layout=modal&amp;tmpl=component&amp;' . Session::getFormToken() . '=1';

		return '<a class="btn-select-contacts ' . $class . ' pull-right hasTooltip" title="' . HTMLHelper::tooltipText('COM_SECRETARY_CONTACTS_MODAL_DIALOG') . '" href="' . $link . '" data-joomla-dialog=\'{"popupType": "iframe", "src": "' . $link . '"}\'><i class="fa fa-search"></i></a>';
	}

	public static function documents($class = NULL)
	{
		Factory::getApplication()->getDocument()->getWebAssetManager()
			->useScript('joomla.dialog-autocreate');

		// Build the script.
		$script = array();

		// Select button script
		$script[] = 'function jSelectDocument( id,nr,category,contact,total,currency ) {';
		$script[] = '	var item = { ';
		$script[] = '		id : id,';
		$script[] = '		value : nr + category,';
		$script[] = '		category : category,';
		$script[] = ' 		contact : contact,';
		$script[] = '		total : total,';
		$script[] = '		currency : currency';
		$script[] = '	}; Secretary.Search.drawBudgetContainer( item , "input.search-documents"); if (Joomla.Modal.getCurrent()) { Joomla.Modal.getCurrent().close(); }';
		$script[] = '}';

		// Add the script to the document head.
		Factory::getDocument()->addScriptDeclaration(implode("\n", $script));

		$link	= 'index.php?option=com_secretary&amp;view=documents&amp;layout=modal&amp;tmpl=component&amp;' . Session::getFormToken() . '=1';

		return '<a class="' . $class . '" href="' . $link . '" data-joomla-dialog=\'{"popupType": "iframe", "src": "' . $link . '"}\'><i class="fa fa-search"></i></a>';
	}


	public static function locations($class = NULL, $extension = NULL)
	{
		Factory::getApplication()->getDocument()->getWebAssetManager()
			->useScript('joomla.dialog-autocreate');

		// Build the script.
		$script = array();

		// Select button script
		$script[] = '	function jSelectLocation( id, title, extension, category ) {';
		$script[] = '		Secretary.Search.drawBlockInput("input.search-locations", title);';
		$script[] = '		document.getElementById("jform_location_id").value = id;';
		$script[] = '		var input = document.getElementsByClassName("search-locations");';
		$script[] = '		input[0].value = title;';
		$script[] = '		if (Joomla.Modal.getCurrent()) { Joomla.Modal.getCurrent().close(); }';
		$script[] = '	}';


		// Add the script to the document head.
		Factory::getDocument()->addScriptDeclaration(implode("\n", $script));

		$link	= 'index.php?option=com_secretary&amp;view=locations&amp;extension=' . $extension . '&amp;layout=modal&amp;tmpl=component&amp;' . Session::getFormToken() . '=1';

		return '<a class="' . $class . '" href="' . $link . '" data-joomla-dialog=\'{"popupType": "iframe", "src": "' . $link . '"}\'><i class="fa fa-search"></i></a>';
	}
}
