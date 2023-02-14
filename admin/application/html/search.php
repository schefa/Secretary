<?php

/**
 * @version     3.2.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * 
 */

namespace Secretary\HTML;

require_once SECRETARY_ADMIN_PATH . '/application/HTML.php';

use JFactory;
use JSession;
use JHtml;

// No direct access
defined('_JEXEC') or die;

class Search
{

	public static function contacts($class = NULL)
	{

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
		$script[] = '		SqueezeBox.close();';
		$script[] = '	}';

		// Add the script to the document head.
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

		$link	= 'index.php?option=com_secretary&amp;view=subjects&amp;layout=modal&amp;tmpl=component&amp;' . JSession::getFormToken() . '=1';

		return '<a class="modal btn-select-contacts ' . $class . ' pull-right hasTooltip" title="' . JHtml::tooltipText('COM_SECRETARY_CONTACTS_MODAL_DIALOG') . '"  href="' . $link . '" rel="{handler: \'iframe\', size: {x: 800, y: 450}}"><i class="fa fa-search"></i></a>';
	}

	public static function documents($class = NULL)
	{

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
		$script[] = '	}; Secretary.Search.drawBudgetContainer( item , "input.search-documents"); SqueezeBox.close();';
		$script[] = '}';

		// Add the script to the document head.
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

		$link	= 'index.php?option=com_secretary&amp;view=documents&amp;layout=modal&amp;tmpl=component&amp;' . JSession::getFormToken() . '=1';

		return '<a class="modal ' . $class . '" href="' . $link . '" rel="{handler: \'iframe\', size: {x: 800, y: 450}}"><i class="fa fa-search"></i></a>';
	}


	public static function locations($class = NULL, $extension = NULL)
	{

		// Build the script.
		$script = array();

		// Select button script
		$script[] = '	function jSelectLocation( id, title, extension, category ) {';
		$script[] = '		Secretary.Search.drawBlockInput("input.search-locations", title);';
		$script[] = '		document.getElementById("jform_location_id").value = id;';
		$script[] = '		var input = document.getElementsByClassName("search-locations");';
		$script[] = '		input[0].value = title;';
		$script[] = '		SqueezeBox.close();';
		$script[] = '	}';


		// Add the script to the document head.
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

		$link	= 'index.php?option=com_secretary&amp;view=locations&amp;extension=' . $extension . '&amp;layout=modal&amp;tmpl=component&amp;' . JSession::getFormToken() . '=1';

		return '<a class="modal ' . $class . '" href="' . $link . '" rel="{handler: \'iframe\', size: {x: 800, y: 450}}"><i class="fa fa-search"></i></a>';
	}
}
