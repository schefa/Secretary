/*
* @package     com_secretary
* @copyright   Copyright (C) Fjodor Schäfer, SCHEFA.COM - All Rights Reserved.
*
**********************************************************************
* 
* These file is proprietary of SCHEFA.COM, copyrighted and cannot be redistributed
* in any form without prior permission from SCHEFA.COM
*			
**********************************************************************
*
* @license     SCHEFA.COM Proprietary Use License
*
*/
 
jQuery.noConflict();

if(typeof(secretary) == 'undefined') {
	var secretary = {};
}
if(typeof(secretary.jQuery) == 'undefined') {
	secretary.jQuery = jQuery.noConflict();
}

jQuery( document ).ready(function( $ ) {
	
	secretary.entry = {	
		
		counter : {

			set : function(value) {
				$('.item-counter').attr('counter', value);
				return;
			},	
			get : function() {
				return $('.item-counter').attr('counter');
			}
		},
			
		addDocument : function (id, nr, created, deadline, title, subtotal, tax, total, subjectid) {

			var counter = secretary.entry.counter.get(); 
			var html = $('.table-item-document:first').html();

			html = html.replace(/##counter##/g, counter);
			html = html.replace(/##id##/g, id);
			html = html.replace(/##title##/g, title);
			html = html.replace(/##price##/g, subtotal);
			html = html.replace(/##tax##/g, tax);
			html = html.replace(/##total##/g, total);
			html = html.replace(/##deadline##/g, deadline);
			html = html.replace(/##created##/g, created);
			html = html.replace(/##nr##/g, nr);
			html = html.replace(/##subjectid##/g, subjectid);

			if(subjectid > 0) {				
				$.ajax({
			        url: "index.php?option=com_secretary&task=ajax.searchSubjects&id="+subjectid,
			        success: function (data) {
			        	chosenSubject = JSON.parse(data);
			        	chosenSubject.fullname = (chosenSubject.firstname + " " + chosenSubject.lastname).trim();
						html = html.replace(/##subject##/g, chosenSubject.fullname);
			        },
			        async: false
			    });
			} else {
				html = html.replace(/##subject##/g, "?");
			}
			
			$('<li class="table-item table-item-document clearfix dd-item" data-id="'+ parseInt(counter)+'">' + html + '</li>').appendTo('.table-items-list ol.dd-list').show();
			secretary.entry.counter.set( parseInt(counter) + 1 );
			
			secretary.entry.calculate.total();
			return false;
		},
	
		add : function (title, desc, pno, quantity, entity, price, taxRate, total) {
				
				if(total == '') total = 0;
				var counter = secretary.entry.counter.get(); 
				
				// Autocomplete Product Title
				$( "input.table-item-title.pro" ).live('focus', function() {
					var pUsage = $('select.pUsage').val();
					$(this).autocomplete({
							source: 'index.php?option=com_secretary&task=ajax.searchProducts&u='+pUsage, 
							minLength:2,
							open: function(event, ui) { $(".ui-autocomplete").css("z-index", 1000); },
							select: function( event, ui ) {
								$( this ).val( ui.item.value );
								$( 'textarea.table-item-title[name="jform[items]['+counter+'][description]"]' ).val( ui.item.description );
								
								var pprice =  parseFloat(ui.item.price).toFixed(2);
								$( 'input.table-item-price[name="jform[items]['+counter+'][price]"]' ).val( pprice );
								var productTaxRate =  parseFloat(ui.item.taxRate).toFixed(2);
								$( 'input.table-item-taxrate[name="jform[items]['+counter+'][taxRate]"]' ).val( productTaxRate );
								
								var parent = $(this).closest('li.table-item');
								var qnty = parent.find('.table-item-quantity').val();
								var item_total = Number(qnty) * Number(pprice);
								parent.find('.table-item-total').val(item_total);
								secretary.entry.calculate.total();
								return;
							}
						})
						.autocomplete( "instance" )._renderItem = function( ul, item ) {
							return $( "<li>" )
							.append( '<a><span class="ui-menuitem-value">'+ item.value + '</span><br><span class="ui-menuitem-sub">' + item.price + '</span></a>' )
							.appendTo( ul );
						};
				});
				
				var html = $('.table-item:first').html();
				html = html.replace(/##counter##/g, counter);
				html = html.replace(/##title##/g, title);
				if(desc && desc !== 'undefined') {
				  html = html.replace(/##desc##/g, desc);
				} else {
				  html = html.replace(/##desc##/g, '');
				}
				html = html.replace(/##pno##/g, pno);
				html = html.replace(/##quantity##/g, quantity);
				
				if(isNaN(entity)) {
					html = html.replace(/##entity##/g, entity);
				} else {
					var searchEntity = '<option value="'+ entity +'">';
					var replaceEntity = '<option value="'+ entity +'" selected="selected">';
					html = html.replace(searchEntity, replaceEntity);
					html = html.replace(/##entity##/g, entity);
				}
				
				// Steuern abhängig vom Steuersatz und Typ
				if(typeof taxRate === "undefined" && isNaN(taxRate) ) {
					html = html.replace(/##taxRate##/g, Number(taxRatePerc) );
				} else {
					html = html.replace(/##taxRate##/g, Number(taxRate));
				}
				
				if(!isNaN(parseFloat(price))){
					price = parseFloat(price).toFixed(2);
				}
				html = html.replace(/##price##/g, price);
				
				if(!isNaN(parseFloat(total)) && title.length > 0){
				  total = parseFloat(total).toFixed(2);
				} else {
				  total = '';  
				}

				// if( desc != '') html.replace(/style=\"display:none;\"/g, /style=\"display:block;\"/);
				
				html = html.replace(/##total##/g, total);
				$('<li class="table-item clearfix dd-item" data-id="'+ parseInt(counter)+'">' + html + '</li>').appendTo('.table-items-list ol.dd-list').show();
				secretary.entry.counter.set( parseInt(counter) + 1 );
				
				secretary.entry.calculate.total();
				return;
	
			},
		
		calculate : {
				
			taxes : function( taxtype, rabattProz ) {
						
				$('.document-taxrate').empty();
				
				if(rabattProz > 0) {
					var rabattProz = (1 - ( rabattProz / 100 ));
				} else {
					var rabattProz = 1;
				}
				
				function onlyUnique(value, index, self) { 
					return self.indexOf(value) === index;
				}
				if (taxtype == 0) {
					var taxTotal = 0;
					$('.table-item-taxrate').val('');
					return false;
				}
					
				var taxRates = [] ;
				var taxObj =  [] ;
				
				var result = new Object();
				
				// Loop through the Table 
				$('li.table-item').each(function(e){
					var taxRate = 0;
					var taxTotal = 0;
					var taxTotal = Number($(this).find('input.table-item-total').val());
					var taxRate = Number($(this).find('input.table-item-taxrate').val());
					
					if(taxRate > 0  && taxTotal != 0) {
						if(taxtype == 1) { // inklusiv 
							var taxTotal = ( taxTotal - ( taxTotal / ( 1 + ( taxRate / 100 ) ) ) ) * rabattProz;
						} else if (taxtype == 2) { // exklusiv
							var taxTotal = ( taxTotal  * (taxRate / 100 ) ) * rabattProz;
						}
						
						if(!isNaN(taxTotal)) {
							taxObj.push( { 'rate' : taxRate, 'total' : taxTotal } );
							taxRates.push(taxRate);
						}
					}
				});
				
				var uniqueRates = taxRates.filter( onlyUnique ); 
				
				var taxTotal  = 0.0;
				for ( row in taxObj ) {
					if(taxObj.hasOwnProperty(row)){
						if(!result.hasOwnProperty(taxObj[row].rate)) {
							result[taxObj[row].rate] = 0;
						}
						result[taxObj[row].rate] +=  taxObj[row].total;
						taxTotal += taxObj[row].total ;
					}
				}
				
				for ( key in result ) {
					$('<div class="fullwidth"><div class="pull-right secretary-input-group document-clean-input clearfix"><div class="secretary-input-group-left"><span class="tax-proz">' + key + '%</span><span class="tax-val">' + parseFloat( result[key] ).toFixed(2) + '</span><input type="hidden" name="jform[taxtotal]['+ key +']" value="'+ result[key].toFixed(4) +'" /></div><div class="secretary-input-group-right currency-control">'+ currency + '</div></div></div>').appendTo('.document-taxrate');
				}
				
				return taxTotal;
			},
		
			total : function() {
			
				var rabatt = 0, rabattProz = 0;
				
				rabatt 	 	= $('#jform_rabatt').val();
				rabattProz	= $('#jform_rabatt_proz').val();
				var taxtype		= $('#jform_taxtype').val();
				var tax 	 	= taxRatePerc;
				var sum			= 0.0;
				
				$('.dd-list input.table-item-total').each(function(e){
					sum = Number(sum) + Number($(this).val());
				});
				
				if(rabatt > 0) {
					var rabattProz =  Number(rabatt) * 100 /  Number(sum);
					$('input#jform_rabatt_proz').val(parseFloat(rabattProz).toFixed(2));
				} else {
					if(rabattProz > 0) {
						var rabatt	= Number(sum) * (Number(rabattProz) / 100);
					} else {
						$('input#jform_rabatt_proz').val("");
					}
				}
				
				if(rabatt > 0) {
					var sum	= Number(sum) - Number(rabatt);
				}
				
				var taxtotal = secretary.entry.calculate.taxes(taxtype, rabattProz);
				
				if(taxtype == 1) {
					var subtotal = sum - taxtotal;
				} else if (taxtype == 2) {
					var subtotal = sum;
					var sum = subtotal + taxtotal;
				} else if (taxtype == 0) {
					var subtotal = sum;
					$('.table-item-taxrate').val('');
				}
				
				//$('.secretary-acc-total').val(sum);
				
				subtotal = parseFloat(subtotal).toFixed(2);
				sum = parseFloat(sum).toFixed(2) ;
				taxtotal = parseFloat(taxtotal).toFixed(2);
				
				$('.document-subtotal').val(subtotal );
				$('.document-total').val(sum );
				$('.document-taxtotal').val(taxtotal );
				$('.secretary-acc-sum').text(sum);
				
				if(rabatt > 0 && rabattProz > 0) {
					rabatt = parseFloat(rabatt).toFixed(2);
					$('input#jform_rabatt').val( rabatt );
				}
				return;
			},

		},
		
	};
	
	secretary.selectContactData = function( name, street, zip, location, phone, email, gender, id, connections ) {
			$( "input#jform_subject_name" ).val( name );
			$( "input#jform_subject_street" ).val( street );
			$( "input#jform_subject_zip" ).val( zip );
			$( "input#jform_subject_location" ).val( location );
			$( "input#jform_subject_phone" ).val( phone );
			$( "input#jform_subject_email" ).val( email );
			$( "select#jform_subject_gender" ).val( gender );
			$( "input#jform_subjectid" ).val( id );
			
			// Display block
			$("#display_contact_name").show();
			$("#contact_name").text(name);
			$("#jform_subject_name").hide();
			
			var options = $("#jform_subject_connection");
			options.empty();
			
			if(connections.length < 1) {
				options.parent().hide();
			} else  {
				options.parent().show();
				$.each(connections, function() {
					var texti = (this.note.length > 0) ? this.fullname + " ("+this.note+")" : this.fullname;
					options.append($("<option />").val(this.id).text(texti));
				});
			}
			
		};
		
	var productUsage = {
			value : 1,
			get : function() { $('#productUsage select').val(); } ,
			set : function(val) { this.value = val; } ,
		};
	
	$('#productUsage select').live('change', function() {
		productUsage.set($(this).val());
	});
	
	$('.add-subject-as-contact').live('click', function() {
		secretary.selectContactData( chosenSubject.fullname, chosenSubject.street, chosenSubject.zip, chosenSubject.location, chosenSubject.phone, chosenSubject.email, chosenSubject.gender, chosenSubject.id, chosenSubject.connections );
	});
	
	////////////////////////////////////////////
	// Autocomplete
	
	$( "input#jform_subject_name" ).autocomplete({
			source: 'index.php?option=com_secretary&task=ajax.searchSubjects', 
			minLength:2,
			open: function(event, ui) {
				$(".ui-autocomplete").css("z-index", 1000);
			},
			select: function( event, ui ) {
				secretary.selectContactData( ui.item.value, ui.item.street, ui.item.zip, ui.item.location, ui.item.phone, ui.item.email, ui.item.gender, ui.item.id, ui.item.connections );
			}
		})
		.autocomplete( "instance" )._renderItem = function( ul, item ) {
			var appendix = '<a><span class="ui-menuitem-value">'+ item.value + '</span><br>' ;
			appendix += '<span class="ui-menuitem-sub">';
			if(item.street.length > 0) appendix += item.street + ', ' ;
			appendix += item.zip +' '+ item.location +'</span></a>' ;
			
			return $( "<li>" )
				.append(appendix)
				.appendTo( ul );
		};
	
	// Check Number
	$('input.inputAjaxNumber').change(function(event){
		event.preventDefault();
		
		var cont= $('#ajaxNumber');
		var res = $('#ajaxNumberResult').hide();
		
		var nr		= $(this).val();
		var id		= cont.data('id');
		var catid	= cont.data('catid');
		
		$.ajax({
			url: "index.php?option=com_secretary&task=ajax.testAjaxNumber&nr=" + nr + "&catid=" + catid + "&id=" + id ,
			type: 'get',
			success: function(response){
				res.html(response).fadeIn();
			}
		});
	});
			  
	// Autocomplete Title
	$( "#jform_title" ).autocomplete({
			source: 'index.php?option=com_secretary&task=ajax.searchTitle', 
			minLength:3,
			open: function(event, ui) {
				$(".ui-autocomplete").css("z-index", 1000);
			},
			select: function( event, ui ) {
				$( this ).val( ui.item.value );
			}
		})
		.autocomplete( "instance" )._renderItem = function( ul, item ) {
			return $( "<li>" )
			.append( '<a><span class="ui-menuitem-value">'+ item.value + '</span>' )
			.appendTo( ul );
		};
		
	// Calculate die Gesamtsumme je Zeile
	
	$('li.table-item').live("keyup change", function() {
		var quantity	= $(this).find('input.table-item-quantity').val();
		var price 		= $(this).find('input.table-item-price').val();
		var total		= (quantity * price);
		
		if(!$(this).hasClass('table-item-document') && (total != '' || total !== 0)) {
			$(this).find('input.table-item-total').val(parseFloat(total).toFixed(2));
		}
		
		secretary.entry.calculate.total();		
	});
	
	$('#jform_rabatt').live("keyup change", function() { $('input#jform_rabatt_proz').val(""); });
	$('#jform_rabatt_proz').live("keyup change", function() { $('input#jform_rabatt').val(""); });
	
	$('.document-summary input').live("keyup change", function() {
		secretary.entry.calculate.total();
	});
	
	if(typeof(e_items) !== 'undefined') {
		for(var e in e_items){
			if(e_items.hasOwnProperty(e)){
				// Produktnummer
				var pno = "";
				if((typeof e_items[e].pno !== 'undefined') && e_items[e].pno.length > 0) {
					pno =  htmlEntities(e_items[e].pno);
				}
				if(typeof e_items[e].id !== 'undefined'){
					secretary.entry.addDocument(e_items[e].id, e_items[e].nr, e_items[e].created, e_items[e].deadline, htmlEntities(e_items[e].title), e_items[e].subtotal, e_items[e].tax, e_items[e].total, e_items[e].subjectid);
				} else {
					secretary.entry.add(htmlEntities(e_items[e].title), htmlEntities(e_items[e].description), pno, e_items[e].quantity,  htmlEntities(e_items[e].entity), e_items[e].price, e_items[e].taxRate, e_items[e].total);
				}
			}
		}
	}
	
	function htmlEntities(str) {
		return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
	}

	if($('div.table-items-list ol.dd-list').is(':empty')) {
		secretary.entry.add('', '', '', '', '', '', taxRatePerc , '');
	}

	$('#item-add').click(function(){		
		secretary.entry.add('', '', '', '', '', '', taxRatePerc , '');
		return false;						
	});

	$('#item-add-document').click(function(){		
		secretary.entry.addDocument('', '', '', '', '', '', '', '', 0);
		return false;						
	});
	
	$('#item-toggle-tax').click(function() {
		
		var down = !$(this).hasClass('active');
		$.ajax({ 
			url : 'index.php?option=com_secretary&task=ajax.toggleTaxRateColumn&v=' + (+ down)
		});
		
		$(this).toggleClass('active');
		$('.secretary-documents-table-items').toggleClass('taxSelection');
		$('.table-item-col-4').toggle();

		return false;
	});
	
	$('#item-toggle-pno').click(function() {
		$(this).toggleClass('active');
		$('.secretary-documents-table-items').toggleClass('pnoSelection');
		return false;
	});
	
	$('#jform_taxtype').live('change', function( event ) {
		secretary.entry.calculate.total();
	});
	
	$('.table-item-quantity').live('keyup change', function( event ) {
		value = $(this).val().replace(',','.');
		$(this).val(value);
	});
	
	$('.table-item-taxrate').live('focusout', function() {
		secretary.entry.calculate.total();
	});
	
	$('.table-item-remove').live('click',function(){
		$(this).parents('.table-item').remove();
		secretary.entry.calculate.total();
		return false;			
	});
	
	$('.open-desc').live('click', function() {
    	$(this).children().toggleClass("fa-plus fa-minus");
		$(this).next('textarea').toggle();
	});
	
	$('#openRabattDialog').click(function(){
		$(this).toggleClass('active');
		$('.table-rabatt-row').toggle();
	});
	
	$('#myTab a').click(function (e) {
		e.preventDefault();
		$(this).tab('show');
    });
	
	$('#jform_currency').live('change',function(event) {
		var currency = $(this).val();
		$.ajax({
			url: "index.php?option=com_secretary&task=ajax.getCurrency&term=" + currency ,
			type: 'get',
			success: function(response){
				$('.currency-control').text(response).fadeIn();
			}
		});
	});
	
});
