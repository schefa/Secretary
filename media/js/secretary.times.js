/**
 * @version     3.0.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      GNU General Public License version 2 or later.
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
