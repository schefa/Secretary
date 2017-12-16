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
jQuery( document ).ready(function( $ ) {
	
	/*********** Toggle Designer **************************************************/
	
	var canvasEl = document.getElementById('secretary-canvas');
	var canvasTextareaEl = document.getElementById('secretary-canvas-input');
	$('.secretary-template-designer div').click(function(event){
		toggleActive( this );
		if($(this).hasClass('designer')) {
			canvasEl.innerHTML = canvasTextareaEl.value;
			canvasTextareaEl.style.display = "none";
			canvasEl.style.display = "block";
			document.getElementById('secretary-template-designer-options').style.display = "block";
		} else if($(this).hasClass('html')) {
			canvasTextareaEl.value = canvasEl.innerHTML;
			canvasTextareaEl.style.display = "block";
			canvasEl.style.display = "none";
			document.getElementById('secretary-template-designer-options').style.display = "none";
		}
	})
	
	
	/*************************************************************/
	
	var canvas = '#secretary-canvas';
	var canvasOffset = $(canvas).offset();
	
	var boxToolbar = '<div class="box-toolbar"><div class="box-toolbar-group"><div class="box-mover ui-draggable-handle"><i class="fa fa-arrows"></i></div><div class="box-delete"><i class="fa fa-remove"></i></div></div><div class="box-toolbar-group"><div class="box-label">CSS</div><div class="box-cssclass" contenteditable="true">classname</div></div></div>';
	
	/********** Box ***************************************************/
	
	$('.add-box').click(function(){
		zindex++;
		var type = $(this).data('type');
		switch(type) {
			default : case 'textarea': 
				var $canvasElement = $('<div class="ui-draggable ui-draggable-handle secretary-canvas-box" style="position:absolute;top:0px;left:0px;z-index:'+zindex+'"><div class="box-text" contentEditable="true"></div>' + boxToolbar + '</div>');
				setDraggable($canvasElement);
				$canvasElement.appendTo(canvas);
				break;
		}
	});
	
	$('.box-delete').live('click',function(event){
		event.preventDefault();
		$(this).closest('.secretary-canvas-box').remove();	
	});
	
	$('.box-cssclass').live('keyup',function(event){
		var parent = $(this).closest('.secretary-canvas-box');
		var boxText = parent.find('.box-text');
		boxText.attr('class', 'box-text');	
		var text = $(this).text();
		boxText.addClass(text);	
	})
	
	/********** Draggable ***************************************************/
	
	function setDraggable( element ) {
		element.draggable({ 
			handle: '.box-mover',
			cursor: 'move',
			scroll: false,
			stop: function(e, ui) {
				
					var textArea = element.children('.box-text');
					var offsetToolbar = element.children('btn-group');
					
					var posLeft = e.pageX - canvasOffset.left;
					var posTop = e.pageY - canvasOffset.top;
					
					var boxWidth = textArea.width();
					if(posLeft + boxWidth > $(canvas).width())
						posLeft =  $(canvas).width() - boxWidth;
						
					var boxHeight = textArea.height();
					if( (posTop + boxHeight ) > $(canvas).height())
						posTop =  $(canvas).height() - boxHeight - offsetToolbar;
					
					if(posTop < 0 && posLeft < 0)
						$(this).css({ left: 0, top: 0  });
					else if(posTop < 0)
						$(this).css({ left: posLeft, top: 0  });
					else if(posLeft < 0)
						$(this).css({ left: 0, top: posTop  });
					
					textArea.css({ left: element.position().left, top: element.position().top + offsetToolbar });
					
				}
		});
	};
	setDraggable($('.secretary-canvas-box'));
	
    $(".secretary-drag-field").draggable({ helper: 'clone', cursor: 'move' });
    $(canvas).droppable({
        drop: function (event, ui) {
            var $canvas = $(this);
			zindex++;
            if (!ui.draggable.hasClass('secretary-canvas-box')) {
                var $canvasElement = ui.draggable.clone();
				$canvasElement.removeClass('secretary-drag-field');
                $canvasElement.addClass('secretary-canvas-box');
                $canvasElement.draggable({ containment: canvas, handle: '.box-mover' });
                $canvas.append($canvasElement);
                $canvasElement.css({
                    left: (event.pageX - canvasOffset.left),
                    top: (event.pageY - canvasOffset.top),
                    position: 'absolute',
					zIndex: zindex
                });
				$canvasElement.find('.box-text').attr('contenteditable', true);
				$(boxToolbar).appendTo($canvasElement);
            }
        }
    });
		
	/********************/
	/*** Resize Paper ***/
	/********************/
	
	$('#secretary_format').change(function() {
		var value = $(this).val();
		arr = value.split(";");
		formatWidth = parseInt(arr[0]);
		formatHeight = parseInt(arr[1]);
		proportion = arr[0].replace(/[0-9]/g, '');;
		calcNewPaperOffset();
	});

	$('.secretary-template-dpi div').click(function(event) {
		toggleActive( this );
		dpi = Number($(this).text());
		$('#secretary_dpi').val(dpi);
		calcNewPaperOffset();
	});
	
	function toggleActive( target ) {
		$(target).parent().children().removeClass('active');
		$(target).addClass('active');
	}

	function calcNewPaperOffset() {
		switch(proportion) {
			case 'mm':
				var canvasWidth = ((formatWidth / 10) / 2.54) * dpi;
				$(canvas).width(canvasWidth);
				var canvasHeight = ((formatHeight / 10) / 2.54) * dpi;
				$(canvas).height(canvasHeight);
				break;
			case '%':
				$(canvas).css({ width: formatWidth + proportion, height: formatHeight + proportion });
				break;
		}
	};
	
	calcNewPaperOffset();
	
});
