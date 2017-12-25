/**
 * @version     3.0.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      GNU General Public License version 2 or later.
 */

(function($, Secretary) {

	$( document ).ready(readyFn);
	
	function readyFn() { 

		Secretary.Accounting = {
			
			total : function() { return $('#acc_total_amount_total').val(); },
			subtotal : function() { return $('#acc_total_amount_subtotal').val(); },
			taxtotal : function() { return $('#acc_total_amount_tax').val(); },
			clear : function() { $('#secretary-accounting input').val(''); },
			
			add : function(type, account, accountid, sum) {
				
				var counter = $('.add-' + type).attr('counter'); 
				counter =  parseInt(counter);
				
				var html = $('.secretary-acc-row:first').html();
				html = html.replace(/##counter##/g, counter);
				html = html.replace(/##type##/g, type);
				html = html.replace(/##account##/g, account);	
				html = html.replace(/##accountid##/g, accountid);	
				html = html.replace(/##sum##/g, parseFloat(sum).toFixed(2) );
				
				$('<div class="secretary-acc-row clearfix">' + html + '</div>').appendTo('.secretary-acc-rowlist-'+type).show();
				$('.add-' + type).attr('counter', parseInt(counter) + 1);
				
				return false;
			},
			
			run : function() {
				// accounting.clear();
		
				var total	= Secretary.Accounting.total();
				var soll	= 0.0;
				var haben	= 0.0;
				
				if(typeof(accJSON) === 'undefined')	{
					if($('.secretary-acc-rowlist-s').is(':empty')) {
						Secretary.Accounting.add('s','','','');
					}
					if($('.secretary-acc-rowlist-h').is(':empty')) {
						Secretary.Accounting.add('h','','','');
					}
					return;
				}
				
				for(var accType in accJSON){
					if(accJSON.hasOwnProperty(accType)){
						for(var booking in accJSON[accType]){
							if(accJSON[accType].hasOwnProperty(booking)){
								Secretary.Accounting.add(accType,accJSON[accType][booking][0],accJSON[accType][booking][1],accJSON[accType][booking][2]);
							}
						}
					}
				}
			
			},
			
			check : function () {
				
				/*
				return $('.secretary-acc-total').live('change',function(){
					var total = parseFloat($('.secretary-acc-sum').text()).toFixed(2);
					var soll = total;
					$('.acc_s_sum').each(function() {
						soll -= Number($(this).val()).toFixed(2);
					});
					var haben = total;
					$('.acc_h_sum').each(function() {
						haben -= Number($(this).val()).toFixed(2);
					});
					if(soll !== 0 || haben !== 0) {
						alert('Warning!');
						return false;
					}
				});
				*/
			}
	
		};
	  
		Secretary.Accounting.check();
		
		$('.btn-buchen').bind('click', function (event) {
			event.preventDefault();
			
			if(Secretary.Accounting.check() === false)
				return;
			
			$(this).addClass('ui-autocomplete-loading');
			var form = $(this).parents('form:first');
			var formTask = $('#formtask');
			formTask.val('ajax.buchen');
			var container = $(this);
			var id		= $(this).data('id');
			$.ajax({
				type: 'POST',
				url:  "index.php?option=com_secretary&task=ajax.buchen&id=" + id,
				data: form.serialize(),
				success: function (response) {
					res = JSON.parse(response);
					if(res[0] == 200) {
						$('#secretary-document-accounting').empty();
						$('#secretary-document-accounting').text(res[1]);
					} else {
						$('<div class="alert alert-warning fullwidth">'+res[1]+'</div>').appendTo('#secretary-document-accounting');
					}
					container.removeClass('ui-autocomplete-loading');
					formTask.val('');
	            }
			});
		});
		
		$('.secretary-acc-add').on('click', function() {
			var type = $(this).data('type');
			var rest = parseFloat($('.secretary-acc-sum').text());
			
			$('.acc_'+type+'_sum').each(function() {
				rest -= Number($(this).val());
			});
			
			Secretary.Accounting.add(type, '', '', rest);
		});
		
		$('.acc-row-remove').live('click', function() {
			$(this).parents('.secretary-acc-row').remove();
		});
		
		Secretary.Accounting.run();
		
		$("input.search-accounts, input.search-accounts_system").live('focus', function() {
			$(this).autocomplete({
				source: 'index.php?option=com_secretary&task=ajax.searchAccounts', 
				minLength:2,
				open: function(event, ui) {
					$(".ui-autocomplete").css("z-index", 1000);
				},
				select: function( event, ui ) {
					$(this).val(  ui.item.nr +" "+ ui.item.title );
					$(this).next().val( ui.item.id );
					return false;
				}
			})
			.autocomplete( "instance" )._renderItem = function( ul, item ) {
				return $( "<li>" )
				.append( '<a><span class="ui-menuitem-value">'+ item.nr + ' ' + item.title + '</span></a>' )
				.appendTo( ul );
			};
		});
		 
	};
	
})(jQuery, Secretary);