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

(function($, Secretary) {
	
	$(document).ready(readyFn);
	
	function readyFn($) {

	    $('#add_document').click(function(){
	        var extension = $('#add_new_document').val();
	        var catid = $('input[name="catid"]').val();
	        var url = 'index.php?option=com_secretary&task=time.add&extension='+ extension+'&catid='+ catid ;
	        window.location.href = url;
	    }); 
    
		$('.center input[type=\"checkbox\"]').click(function(){
			if($(this).attr("checked")) {
				var extension = $(this).parent().data('extension');
				$('#extension').val(extension);
			} else {
				$('#extension').val('');
			}
		});
    
		$('.projectTimer a').click(function() {
			$(this).parent().children().toggle();
			var itemID = $(this).parent().data("item");
			var projectID = $(this).parent().data("project");
			var task = $(this).data("task");
			$.ajax({
				url: "index.php?option=com_secretary&task=ajax.projectTimer&action=" + task + "&itemID=" + itemID + "&pid=" + projectID ,
				type: 'get',
				success: function(response){
					if(task == 'stop') $('.totalworktime-'+ itemID).text(response);
				}
			});
		});
	}
	
}(jQuery, Secretary));
