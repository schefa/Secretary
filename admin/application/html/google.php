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

require_once SECRETARY_ADMIN_PATH .'/application/HTML.php';

use JText;
use Secretary\Application;

// No direct access
defined('_JEXEC') or die;

class Google
{
	
	public static function items() {
		
	}
	
	public static function maps( $items, $type = 'contacts' )
	{
		
	    $params = Application::parameters();
	    $key       = $params->get('gMapsAPI',"");
	    $keyString = (strlen($key)>0) ? 'key='.$key.'&amp;' : "";
	    
		$new      = array();
		$html     = array();
		$html[]   = '<div id="map" class="fullwidth" style="width:100%;height:400px;"></div>';
		
		foreach($items AS $i => $item)
		{ 
			if($item->lat != 0 && $item->lng != 0)
			{
				$anschrift = "";
				
				if($type == 'contacts')
					$anschrift = addslashes(html_entity_decode($item->firstname, ENT_QUOTES)) ." ". addslashes(html_entity_decode($item->lastname, ENT_QUOTES)) ;
			   
				if(!empty($item->street))
				  $anschrift .= "<br>".addslashes(html_entity_decode($item->street, ENT_QUOTES));
				  
				if(!empty($item->location))
				  $anschrift .= "<br>".$item->zip." ".addslashes(html_entity_decode($item->location, ENT_QUOTES));
				
				$new [] = "{'name': '". $anschrift ."', 'lat' : '". $item->lat ."', 'lng' : '". $item->lng ."'},";
				
			}
			
		}
		
						
		$html[] =  '<script type="text/javascript">';
		
		$html[] = '
(function($) {  
		    
    var points = ['. implode('',$new ). '];
    window.initMap = function() {
        
        var infowindow = new google.maps.InfoWindow();
        var getBoundsZoomLevel = function(bounds, mapDim) {
            var WORLD_DIM = { height: 256, width: 256 };
            var ZOOM_MAX = 18;
        
            function latRad(lat) {
                var sin = Math.sin(lat * Math.PI / 180);
                var radX2 = Math.log((1 + sin) / (1 - sin)) / 2;
                return Math.max(Math.min(radX2, Math.PI), -Math.PI) / 2;
            }
        
            function zoom(mapPx, worldPx, fraction) {
                return Math.floor(Math.log(mapPx / worldPx / fraction) / Math.LN2);
            }
        
            var ne = bounds.getNorthEast();
            var sw = bounds.getSouthWest();
        
            var latFraction = (latRad(ne.lat()) - latRad(sw.lat())) / Math.PI;
            
            var lngDiff = ne.lng() - sw.lng();
            var lngFraction = ((lngDiff < 0) ? (lngDiff + 360) : lngDiff) / 360;
            
            var latZoom = zoom(mapDim.height, WORLD_DIM.height, latFraction);
            var lngZoom = zoom(mapDim.width, WORLD_DIM.width, lngFraction);
        
            return Math.min(latZoom, lngZoom, ZOOM_MAX);
        };
        
        var createMarkerForPoint = function(point) {
            var marker = new google.maps.Marker({
                position: new google.maps.LatLng(point.lat, point.lng)
            });
			google.maps.event.addListener(marker, \'click\', (function(marker) {
			  return function() {
				infowindow.setContent(point.name);
				infowindow.open(map, marker);
			  }
			})(marker));
            return marker;
        };
        
        function createBoundsForMarkers(markers) {
            var bounds = new google.maps.LatLngBounds();
            $.each(markers, function() {
                bounds.extend(this.getPosition());
            });
            return bounds;
        }
        
        var mapDiv = document.getElementById(\'map\');
        
        var mapDim = {
            height: mapDiv.offsetHeight,
            width: mapDiv.offsetWidth
        }
        
        var markers = [];
        $.each(points, function() { markers.push(createMarkerForPoint(this)); });
        
        var bounds = (markers.length > 0) ? createBoundsForMarkers(markers) : null;
        var map = new google.maps.Map(mapDiv , {
            center: (bounds) ? bounds.getCenter() : new google.maps.LatLng(0, 0),
            zoom: (bounds) ? getBoundsZoomLevel(bounds, mapDim) : 0,
    		mapTypeId: google.maps.MapTypeId.ROADMAP
        });
        
        $.each(markers, function() { this.setMap(map); });
	}   
})( jQuery );';
		
		$html[] = '</script><script async defer src="https://maps.google.com/maps/api/js?'.$keyString.'callback=initMap"></script>';
		
		return implode('', $html);
	}
	
}
