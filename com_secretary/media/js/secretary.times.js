/**
 * @copyright    Copyright (C) 2026 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
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
			if($(this).prop("checked")) {
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
				url: "index.php?option=com_secretary&task=ajax.projectTimer&action=" + task + "&itemID=" + itemID + "&pid=" + projectID + "&" + Joomla.getOptions('csrf.token') + "=1",
				type: 'get',
				success: function(response){
					if(task == 'stop') $('.totalworktime-'+ itemID).text(response);
				}
			});
		});
	}
	
}(jQuery, Secretary));
