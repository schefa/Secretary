/**
 * @version     3.0.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
 * 
 */

(function($, Secretary) {
 
	Secretary.Charts = function( output ) {

		var settings = { 
			id					: '',
			series				: null,
			labels				: null,
			classes				: null,
			height				: '400px',
			width				: '100%',
			horzLines			: 11,
			minRectWidth		: 5,
			maxRectWidth		: 30,
			svgPaddingBottom	: 30,
			shortener			: 0.95,
			yScaleFromZero		: true,
			tooltip				: true,
			noDataText			: "insufficient data",
			legend				: { series : null, rowHeight : 30, space : 6, align : "left" },
			style				: { zeroAxis : "stroke:#999;stroke-width:1px", dashedLine : "stroke:#bbb;stroke-dasharray: 2, 2;" }
		}
		
		if (arguments[1] && typeof arguments[1] === "object") {
			this.options = Secretary.Charts.Utils.extendDefaults(settings, arguments[1]);
		}

		if( typeof arguments[1].series !== 'undefined' ) {
				
			// Resize listener
			function createChart() {
				
				var maxValueArray		= Secretary.Charts.Utils.maxValueFromMultiDArray( settings.series );
				var container			= document.getElementById(settings.id);

				settings.height			= parseInt(settings.height);
				settings.container		= container;
				settings.maxValue		= maxValueArray[0];
				settings.minValue		= maxValueArray[1];
				settings.svgPaddingLeft = 9 * (Math.round(settings.maxValue).toString().length) + 5;

				if(settings.maxValue < 0) {
					Secretary.Charts.SVGFactory.create.ErrorBox(settings);
					return;
				}
				
				if((container.offsetWidth) > 0) { // problem with hidden element
					settings.svgWidth	= parseInt(container.offsetWidth);
				} else {
					var outerContainer	= document.getElementsByClassName('tab-content');
					settings.svgWidth	= outerContainer[0].offsetWidth - 30;
				}

				settings.windowWidth = window.innerWidth || e.clientWidth || g.clientWidth;
				if(output.toString() === 'pie') {
					if(settings.windowWidth < (parseInt(settings.width,10) + 50)) {
						settings.width = settings.windowWidth - 50;
					}
					settings.height = settings.width;
				}
				
				if(settings.yScaleFromZero === false) 
					settings.maxValue	= settings.maxValue - settings.minValue;

				var totalColumns = (settings.labels !== null && settings.series.length < settings.labels.length) ?  settings.labels.length :  settings.series.length;
				settings.rectDistance	= parseFloat((settings.svgWidth - settings.svgPaddingLeft) / totalColumns);
				settings.steps			= parseFloat(((settings.height - settings.svgPaddingBottom)/(settings.maxValue)) * settings.shortener);
				
				var adjustedRectWidth	= ((settings.svgWidth - settings.svgPaddingLeft)/(totalColumns * maxValueArray[2] * 1.1 ) ) - 6;

				if(adjustedRectWidth < 3 || settings.rectWidth < 3) {
					settings.rectWidth	=  settings.minRectWidth;
				} else {
					settings.rectWidth	=  (adjustedRectWidth < settings.maxRectWidth) ? adjustedRectWidth : settings.maxRectWidth;
				}
				settings.xAxisBarCenter = (((settings.svgWidth-settings.svgPaddingLeft)/totalColumns)-settings.rectWidth)/2;
				
				var factory 		= Secretary.Charts.SVGFactory;
				factory.settings	= settings;
				factory.svg			= factory.create.SVG(container);
				
				if(output.toString() === 'bars' || output.toString() === 'graph') {
					factory.draw.YAxis();
					factory.draw.XAxis();
					switch(output.toString()) {
						case 'bars': factory.draw.Bars(); break;
						case 'graph':factory.draw.Graph(); break;
					}
				} else if(output.toString() === 'pie') {
					factory.draw.Pie();
				}
			}
			window.addEventListener('resize', createChart, false);
			createChart();
		} else {
			settings.container	= document.getElementById(settings.id);
			Secretary.Charts.SVGFactory.create.ErrorBox(settings);
			return;
		}
	};
	
	Secretary.Charts.SVGFactory = {
		
		svg : null,
		settings : {},
		svgNS	: "http://www.w3.org/2000/svg",
		
		create : {
			
			SVG : function (container) {
				var element = Secretary.Charts.SVGFactory.svg; 
				while (container.hasChildNodes()) {
					container.removeChild(container.lastChild); 
				}
				var svg = document.createElementNS(Secretary.Charts.SVGFactory.svgNS, 'svg');
					svg.setAttributeNS(null, 'class', 'secretary-charts-svg');
					svg.setAttributeNS(null, 'width', Secretary.Charts.SVGFactory.settings.width );
					svg.setAttributeNS(null, 'height', parseInt(Secretary.Charts.SVGFactory.settings.height,10));

				container.appendChild(svg);	
				return svg;
			},
			
			ErrorBox : function (settings) { 
				while (settings.container.hasChildNodes()) {
					settings.container.removeChild(settings.container.lastChild); 
				}
				var alertDiv = document.createElement('div');
				alertDiv.className = "alert alert-warning";
				alertDiv.innerHTML = settings.noDataText;
				settings.container.appendChild(alertDiv);
				return; 
			},
			
			Circle : function ( parent, attributes ) {
		        var circle = document.createElementNS(Secretary.Charts.SVGFactory.svgNS,"circle" );
				for(var key in attributes) {
					if(attributes.hasOwnProperty(key))
						circle.setAttributeNS(null,key,attributes[key]);
				}
				parent.appendChild(circle);
			},
			
			DashedLine : function ( parent, attributes) {
				var line = document.createElementNS(Secretary.Charts.SVGFactory.svgNS,"line");
				if(typeof attributes !== 'undefined') {
					for(var key in attributes) {
						if(attributes.hasOwnProperty(key))
							line.setAttributeNS(null,key,attributes[key]);
					}
				};
				parent.appendChild(line);
			}, 
			
			Group : function( id, transform ) {
				var group = document.createElementNS(Secretary.Charts.SVGFactory.svgNS,"g");
				group.setAttribute('id', Secretary.Charts.SVGFactory.settings.id +"_"+ id );
				if(typeof(transform) !== "undefined")
					group.setAttribute('transform', transform);
				return group;	
			},
			
			Line : function ( parent, attributes ) {
				var line = document.createElementNS(Secretary.Charts.SVGFactory.svgNS,"line");
				if(typeof attributes !== 'undefined') {
					for(var key in attributes) {
						if(attributes.hasOwnProperty(key))
							line.setAttributeNS(null,key,attributes[key]);
					}
				}
				parent.appendChild(line);
				return line;
			},
			
			PieSection : function ( parent, section, tooltip) {
		        var newSection = document.createElementNS(Secretary.Charts.SVGFactory.svgNS,"path" );
		        newSection.setAttributeNS(null, 'class', section.cssClass);
		        newSection.setAttributeNS(null, 'd', 'M' + section.L + ',' + section.L + ' L' + section.L + ',0 A' + section.L + ',' + section.L + ' 0 ' + section.arcSweep + ',1 ' + section.X + ', ' + section.Y + ' z');
		        newSection.setAttributeNS(null, 'transform', 'rotate(' + (section.R) + ', '+ section.L+', '+ section.L+')');

		        parent.appendChild(newSection);
		        return newSection;
			},
			
			Rectangle : function (parent,attributes) {
		        var rect = document.createElementNS(Secretary.Charts.SVGFactory.svgNS,"rect" );
				for(var key in attributes) {
					if(attributes.hasOwnProperty(key))
						line.setAttributeNS(null,key,attributes[key]);
				}
		        parent.appendChild(rect);
			},
			
			Text : function ( op ) {
				var textnode = document.createTextNode( op.text );			
				var text = document.createElementNS(Secretary.Charts.SVGFactory.svgNS, "text");
				cssClass = (typeof(cssClass) !== 'undefined') ? " "+ op.cssClass : "";
				text.setAttributeNS(null,"class","text text-" + Math.round(op.x) +  cssClass);
				text.setAttributeNS(null, "x", op.x );
				text.setAttributeNS(null, "y", op.y );
				text.setAttributeNS(null, "text-anchor", "start");
				text.appendChild(textnode);
				op.parent.appendChild(text);
			},
			
			Tooltip : function ( container ) {
				var tooltip = document.createElement("div");
					tooltip.className = "secretary-charts-tooltip";
					tooltip.style.display = "none"; 
				    container.appendChild(tooltip);
			    return tooltip;
			},
			
		},
	
		draw : {
			
			Bar : function ( options ) {
				var settings	= Secretary.Charts.SVGFactory.settings,
					fullRectHeight = 0, rectHeight = 0, rectPosY = 0, lastValue = 0, sumValues = 0;

				if( typeof options.data === 'object' ) {
					for( var el in options.data )
					    if( options.data.hasOwnProperty( el ) )
					    	sumValues += parseFloat( options.data[el] );
				} else 
					sumValues = options.data;

				fullRectHeight = sumValues * settings.steps;
				if(!settings.yScaleFromZero && settings.minValue < 0) fullRectHeight -= settings.minValue * settings.steps;
				
				// Create areas inside a single bar chart
				options.rectPosY = settings.height - settings.svgPaddingBottom - fullRectHeight;
				options.x = 0;
				if( Object.prototype.toString.call( options.data ) === '[object Array]' || isNaN(parseFloat(options.data))) {
					// Multiple Status
					for( var status in options.data ) {
						if( options.data.hasOwnProperty( status ) ) {
							options.value = options.data[status];
							options.lastValue = lastValue;
							options.status = status;
							options.cssClass = (typeof options.className !== 'undefined') ? options.className[options.x] : 'line-'+options.x;
							
							this.BarStatus( options, settings);
							
							lastValue = parseFloat(options.value);
							options.x++;
						}
					}
				} else {
					// One status
					options.value = options.data;
					options.lastValue = lastValue;
					options.cssClass = (typeof options.className !== 'undefined') ? options.className[options.x] : 'line-'+options.x;
					
					this.BarStatus( options, settings);
				}
			},

			BarStatus : function ( options, settings ) {

				// X Position
				var rectX = settings.rectPosX + ( settings.rectWidth * options.barNr * 1.2 );
				rectX	 += settings.xAxisBarCenter - ((settings.rectWidth * options.bars / 2 ) - settings.rectWidth);
					
				// Y Position
				rectHeight =  ( parseFloat(options.value) * settings.steps) ; 
				if(options.x > 0) options.rectPosY = options.rectPosY + (options.lastValue * settings.steps);
				
				// Draw
				var item = Secretary.Charts.SVGFactory.create.Line( options.parent, { 
							"x1" : rectX, "x2" : rectX , "y1" : (rectHeight + options.rectPosY), "y2" : options.rectPosY,
							"class" : 'line '+options.cssClass, "style" : "stroke-width:"+ settings.rectWidth +"px"
						});
				
				if(typeof(item) !== 'undefined' && settings.tooltip !== false) {
					item.tip =  options.tip;
					item.parentId = options.parentId;
					item.title = "";
					if(typeof options.label !== 'undefined')
						item.title += "<div class=\"secretary-charts-tooltip-subtext\">"+options.label+"</div>";
					item.title += "<div class=\"secretary-charts-tooltip-title\">"+options.value+"</div>";
					if(typeof(options.status) !== 'undefined' && isNaN(options.status))
						item.title += "<div class=\"secretary-charts-tooltip-subtext\">"+ options.status + "</div>";
					Secretary.Charts.Utils.Tooltip.attach(item);
				}

			},

			Bars : function() {
				
				var settings	= Secretary.Charts.SVGFactory.settings;
				var group		= Secretary.Charts.SVGFactory.create.Group('bars');
				var tooltip		= (settings.tooltip !== false) ? Secretary.Charts.SVGFactory.create.Tooltip(settings.container) : '';
				
				// Draw Bars
				for(var barsGroup = 0; barsGroup < settings.series.length; barsGroup++) {
					
					if((typeof settings.series[barsGroup] !== 'undefined')) {
	
						// X Position
						settings.rectPosX = settings.svgPaddingLeft + ( settings.rectDistance *  barsGroup ) ;
						var options = {};
						options.barNr 	= 0;
						options.parent	= group;
						if(settings.labels !== null && typeof settings.labels[barsGroup] !== 'undefined') {
							if(typeof settings.labels[barsGroup][1] !== 'undefined') { options.label = settings.labels[barsGroup][1];
							} else { options.label = settings.labels[barsGroup].toString();}
						}
						options.tip		= tooltip;
						options.parentId = settings.id;
						options.bars	= 1;
						
						if( typeof settings.series[barsGroup] === 'number' || typeof(settings.series[barsGroup].length) === 'undefined'  ) {
							options.data	= settings.series[barsGroup];
							Secretary.Charts.SVGFactory.draw.Bar(options);
						} else {
							while( options.barNr < settings.series[barsGroup].length ) {
								if((typeof settings.series[barsGroup][options.barNr] !== 'undefined')) {
									options.data	= settings.series[barsGroup][options.barNr];
									options.bars	= settings.series[barsGroup].length;
									options.className = (settings.classes !== null && typeof settings.classes[barsGroup][options.barNr] !== 'undefined') ? settings.classes[barsGroup][options.barNr] : '';
									Secretary.Charts.SVGFactory.draw.Bar(options);
									options.barNr++;
								}
							} 
						}
					}
				}

		        this.Legend({ legend : settings.legend, parent : settings.container });
		        
				Secretary.Charts.SVGFactory.svg.appendChild(group);
				
			},

			Graph : function() {
				var settings	= Secretary.Charts.SVGFactory.settings;
				var group		= Secretary.Charts.SVGFactory.create.Group('graph');

				var endY = 0;
				var endX = 0;

				var tooltip = (settings.tooltip !== false) ? Secretary.Charts.SVGFactory.create.Tooltip(settings.container) : '';
				var pathLine = "M50";
				
				for(var barsGroup = 0; barsGroup < settings.series.length; barsGroup++)
				{
					
					if((typeof settings.series[barsGroup] !== 'undefined') )
					{
						
						// X Position
						settings.rectPosX = settings.svgPaddingLeft + settings.xAxisBarCenter + ( settings.rectDistance *  barsGroup );
					
						var fullRectHeight = 0, 
							rectHeight = 0, 
							rectPosY = 0;
						
						if(settings.yScaleFromZero === false)
							settings.series[barsGroup] -= settings.minValue;
						fullRectHeight = settings.series[barsGroup] * settings.steps;
						
						// Y Position
						var startY = settings.height - settings.svgPaddingBottom - fullRectHeight;
						if(status > 0) startY = startY + ( (series[barsGroup]) * settings.steps);
						if(endY == 0) endY = startY;
						
						// X Position
						var startX = settings.rectPosX;
						if(endX == 0) endX = startX;
						
						// Draw
						Secretary.Charts.SVGFactory.create.Line( group, {"class":"line","x1" : startX, "x2": endX, "y1" : startY, "y2" : endY });
						var item = Secretary.Charts.SVGFactory.create.Line(group,{"class":"point","x1":startX,"x2":startX,"y1":startY,"y2":startY});
						
						// pathLine += "," + (Math.round(startY*3)/3).toFixed(3) + "," + (startX + startY) /2;
						
				        if(typeof(item) !== 'undefined' && settings.tooltip !== false) {
					        item.tip =  tooltip;
					        item.parentId = settings.id;
					        item.title = "";
					        if(settings.labels !== null && settings.labels[1].length > 0)
					        	item.title += "<div class=\"secretary-charts-tooltip-subtext\">"+ settings.labels[1]+"</div>";
					        item.title += "<div class=\"secretary-charts-tooltip-title\">"+  settings.series[barsGroup] + "</div>";
					        Secretary.Charts.Utils.Tooltip.attach(item);
				        }
						endY = startY;
						endX = startX;
					}
				}
				/*
				var path = document.createElementNS(Secretary.Charts.SVGFactory.svgNS, "path");
				path.setAttributeNS(null, "d", pathLine );
				Secretary.Charts.SVGFactory.svg.appendChild(path);
				*/
				
				Secretary.Charts.SVGFactory.svg.appendChild(group);
			},

			Pie : function () {

				var settings	= Secretary.Charts.SVGFactory.settings;
				settings.percs	= [];
				
				var sum = settings.series.reduce(function(pv, cv) { return pv + cv; }, 0);
				for(var k = 0; k < settings.series.length; k++) 
					settings.percs[k] = Math.round( 1000 * settings.series[k] / sum ) / 10;
				
				var calcSectors = function( data ) {
				        var sectors = [];
				        var l = parseInt(data.width,10) / 2, a = 0, aRad = 0, z = 0, x = 0, y = 0, X = 0, Y = 0 , R = 0 ;
				 
				        for( var k = 0; k < data.series.length; k++ ) {
				            a = parseFloat(360 * data.percs[k] / 100); 
				            aCalc = ( a > 180 ) ? 360 - a : a;
				            aRad = aCalc * Math.PI / 180;
				            z = Math.sqrt( 2*l*l - ( 2*l*l*Math.cos(aRad) ) );
				            if( aCalc <= 90 ) { x = l*Math.sin(aRad);
				            } else { x = l*Math.sin((180 - aCalc) * Math.PI/180 ); }
				            y = Math.sqrt( z*z - x*x ); Y = y;
				            if( a <= 180 ) { X = l + x; arcSweep = 0;
				            } else { X = l - x; arcSweep = 1; }
				            var CSSClass = ((data.classes) != null) ? data.classes[k] : '';
				            
				            sectors.push({ label : data.labels[k], value: data.series[k], perc: data.percs[k], cssClass: CSSClass, arcSweep: arcSweep, L: l, X: X, Y: Y, R: R });
				            R = R + a;
				        };

				        return sectors
				    };
				
				sectors = calcSectors(settings);
			    var newSVG = Secretary.Charts.SVGFactory.svg;

		        var titles = [];
				var tooltip = Secretary.Charts.SVGFactory.create.Tooltip(settings.container);
			    var groupSections = Secretary.Charts.SVGFactory.create.Group("sections");
			    for(var j = 0; j < sectors.length; j++) {

		            var CSSClass = (settings.classes != null) ? settings.classes[j] : '';
		            
		            var label = (settings.labels[j].toString().length > 32) ? settings.labels[j].toString().substr(0,32)+'...': settings.labels[j];
		        	titles[j] = '<span class="legend-perc '+ CSSClass +'">'+ settings.percs[j] +'%</span><span class="legend-text">'+ label +'</span>';
		        	
			        var item = Secretary.Charts.SVGFactory.create.PieSection(groupSections, sectors[j]);
			        item.tip =  tooltip;
			         
			        item.title = "<div class=\"secretary-charts-tooltip-title\">"+ sectors[j].label + "</div>";
			        item.title += "<div class=\"secretary-charts-tooltip-subtext\">"+ sectors[j].value +" (" + sectors[j].perc + "%)</div>";
			        
			        item.parentId = settings.id;
			        Secretary.Charts.Utils.Tooltip.attach(item);
			    };
		        newSVG.appendChild(groupSections); 

		        settings.legend.series = titles;
		        this.Legend({ legend : settings.legend, parent : settings.container });
		        
		        if(typeof settings.donut !== 'undefined') {
		        	var m = parseInt(settings.width,10) / 2;
			        var r = parseInt(settings.donut, 10);
			        Secretary.Charts.SVGFactory.create.Circle( newSVG, {'cx':m,'cy':m,'r':(r/2),'class':'donut'});
		        }
			},

			Legend : function ( options ) {
				
			    if(options.legend.series == null) 
			    	return false;
			    
			    var div = document.createElement('div');
			    div.className = 'secretary-charts-legend fullwidth';
			    
			    var ul = document.createElement('ul');
			    if(typeof(options.legend.align) !== 'undefined'){
			    	ul.style.textAlign = options.legend.align.toString();
			    }
			    
			    if(options.legend.series !== null) {
			    	var cnt = options.legend.series.length;
				    for(var i = 0; i < cnt; i++)
				    { 
				    	var li = document.createElement('li');
				    	li.innerHTML = options.legend.series[i];
				    	ul.appendChild(li);
				    }
			    }
			    
			    div.appendChild(ul);
			    options.parent.appendChild(div);

			},

			XAxis : function() {
				
				var settings = Secretary.Charts.SVGFactory.settings;
				var xAxisGroup = Secretary.Charts.SVGFactory.create.Group('xAxis');
				var fullHeight = settings.height - settings.svgPaddingBottom;

				Secretary.Charts.SVGFactory.create.Line(xAxisGroup,{"class":"axisZero","x1":settings.svgPaddingLeft,"x2":settings.svgPaddingLeft,"y1":fullHeight,"y2":0,"style":settings.style.zeroAxis});
				if(settings.labels == null) {
					Secretary.Charts.SVGFactory.svg.appendChild(xAxisGroup);
					return;
				}
				
				for(var i = 0; i < settings.labels.length; i++)
				{
					var group = Secretary.Charts.SVGFactory.create.Group('xAxis_'+i);
					var posX = settings.svgPaddingLeft + settings.rectDistance * i;
					Secretary.Charts.SVGFactory.create.Text({
							parent : group,text : settings.labels[i][0].toString(),
							x : posX + settings.xAxisBarCenter,y : settings.height-10
						});
					
					if(i > 0)
						Secretary.Charts.SVGFactory.create.DashedLine( group,{"class":"dashedline xline"+ i,"x1":posX,"x2":posX,"y1":fullHeight,"y2":0,"style":settings.style.dashedLine});
					xAxisGroup.appendChild(group);
				}
				Secretary.Charts.SVGFactory.svg.appendChild(xAxisGroup);
			},
			
			YAxis : function() {
				
				var settings = Secretary.Charts.SVGFactory.settings;
				var yAxisValue = (Math.round( settings.maxValue / ( settings.horzLines  ) ));
				var yAxisValueLength = yAxisValue.toString().length;
				var abschnittsFaktor = 5 * Math.pow(10, yAxisValueLength - 2);
				
				if(yAxisValue == 0) yAxisValue = 1; 
				var horzLinesCorrection = ( settings.maxValue / settings.horzLines );
				
				for(var x = 0; x < 5; x++) {
					if( yAxisValue % abschnittsFaktor !== 0) {
						var l = Math.pow(10, -abschnittsFaktor.toString().length);
						yAxisValue = Math.ceil(yAxisValue * l) / l;
						if((yAxisValue * (settings.horzLines - 1)) < settings.maxValue)
							yAxisValue += Math.pow(10,l-1);
					} else {
						break;
					}
				}
				
				if((yAxisValue * settings.horzLines < settings.maxValue))
					yAxisValue *= 2;
					
				var yAxisGroup = Secretary.Charts.SVGFactory.create.Group('yAxis');
				var fullHeight = settings.height - settings.svgPaddingBottom;
				var fullWidth  = settings.svgWidth;
				
				for(var linesNeeded = 0; linesNeeded < settings.horzLines ; linesNeeded++)
					if(settings.maxValue <= (yAxisValue * linesNeeded))
						break;
				
				if((settings.steps * yAxisValue * linesNeeded) >= fullHeight)
					settings.steps = (fullHeight - 10)/(yAxisValue * linesNeeded); 
				
				if(!settings.yScaleFromZero && settings.minValue < 0) {
					var group=Secretary.Charts.SVGFactory.create.Group('zero');
					var posY=fullHeight + settings.minValue * settings.steps;
					Secretary.Charts.SVGFactory.create.Text({ parent:group,text:settings.minValue.toString(), x : 0, y : fullHeight });
					Secretary.Charts.SVGFactory.create.Line(group,{"class":"axisZero yline0","x1":settings.svgPaddingLeft,"x2":fullWidth,"y1":posY,"y2":posY,"style":settings.style.zeroAxis});
					yAxisGroup.appendChild(group);
				}
				
				for(var i = 0; i <= linesNeeded ; i++)
				{
					var posY=fullHeight-(yAxisValue*settings.steps*i);
					var group=Secretary.Charts.SVGFactory.create.Group('yAxis_'+i);
					
					posY = (!isNaN(posY)) ? posY : 0;

					if(i == 0) {
						Secretary.Charts.SVGFactory.create.Line(group,{"class":"axisZero","x1":settings.svgPaddingLeft,"x2":fullWidth,"y1":posY,"y2":posY,"style":settings.style.zeroAxis});
					} else {
						Secretary.Charts.SVGFactory.create.DashedLine(group,{"class":"dashedline yline"+i,"x1":settings.svgPaddingLeft,"x2":fullWidth,"y1":posY, "y2":posY,"style":settings.style.dashedLine});
					}
					
					// Labels
					if(settings.yScaleFromZero === false)
						var label = ((yAxisValue * i ) + settings.minValue).toString();
					else
						var label = (yAxisValue * i).toString();
									
					if(i > 0) {
						Secretary.Charts.SVGFactory.create.Text({ parent : group, text : label.toString(), x : 0, y : posY });
					}
					
					yAxisGroup.appendChild(group);
					
				}
				
				Secretary.Charts.SVGFactory.svg.appendChild(yAxisGroup);
			}
		}
	};
		
	Secretary.Charts.Utils = {
		
		Tooltip : {

			attach : function (element) {
				element.addEventListener("mouseenter", this.pathMouseEnter);
				element.addEventListener("mouseleave", this.pathMouseLeave);
				element.addEventListener("mousemove", this.pathMouseMove);
			},
			
		    pathMouseEnter : function (e){
		    	e.target.tip.style.display = "block";
		    	e.target.tip.innerHTML = e.target.title;
		    },
		    pathMouseLeave : function (e){
		    	e.target.tip.style.display = "none";
		    },
		    pathMouseMove : function(e) {
		    	var pos = Secretary.Charts.Utils.getElementPosition(e.target.parentId);
		    	if (e.clientX || e.clientY)
				{
					PosX = e.clientX ;
					PosY = e.clientY ;
				}
				PosX = PosX - pos[0];
				PosY = PosY - pos[1];

			    var windowWidth = window.innerWidth || e.clientWidth || g.clientWidth;
				if((e.clientX + e.target.tip.offsetWidth + 30) > windowWidth)
					PosX = PosX - e.target.tip.offsetWidth - 20;

			    var windowHeight = window.innerHeight || e.clientHeight || g.clientHeight;
				if((e.clientY + e.target.tip.offsetHeight + 40) > windowHeight)
					PosY = PosY - e.target.tip.offsetHeight;

		    	e.target.tip.style.top = PosY + "px",
		    	e.target.tip.style.left = PosX + 10 + "px";
		    }
		},

		getElementPosition : function(elementID) {
			var elem = document.getElementById(elementID);
			var rect = elem.getBoundingClientRect();
			return [  rect.left, rect.top ];
		},
 
		maxValueFromMultiDArray : function ( series ) {
			var newArray = [],
				y = 0, 
				maxBars = 1;
				
			for(var i = 0; i < series.length; i += 1) {
				if(typeof(series[i]) !== 'undefined') {
					if( typeof series[i] === 'object' ) {
						var bar = 0;
						for(var bars in series[i]) {
							if(series[i].hasOwnProperty(bars)) {

								var sumValues = 0;
								if( typeof series[i][bars] === 'object' ) {
									for( var status in series[i][bars] ) {
										if( series[i][bars].hasOwnProperty( status ) ) {
											if(typeof newArray[y] === 'undefined') newArray[y] = 0;
											sumValues = parseFloat( series[i][bars][status] );
											newArray[y] += sumValues;
										}
									}
								} else {
									sumValues += parseFloat( series[i][bars] );
									if(typeof newArray[i] === 'undefined') newArray[i] = 0;
									newArray[i] += sumValues;
								}
								y++;
								bar++;
								if(bar >= maxBars) maxBars = bar;
							} 
						}
					} else if( typeof series[i] === 'number' )  {
						newArray[y] = series[i];
						y++; 
					}
				}
			}
			return [ Math.max.apply(null, newArray) , Math.min.apply(null, newArray) , maxBars ];
		},
		
		// Utility method to extend settings with user options
		extendDefaults : function (source, properties) {
			var property;
			for (property in properties) {
				if (properties.hasOwnProperty(property)) {
					source[property] = properties[property];
				}
			}
			return source;
		}
	};
		
		
})(jQuery, Secretary);
