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

	$( document ).ready(readyFn);
	
	function readyFn() { 
		$('.message-talk-item-form-bottom > button[type="submit"]').click(function(){
		});
		
		$('.message-talk-item-top select').on('change',function(){
			var id		= $(this).data('id');
			var value	= $(this).val();
			$.ajax({ 
				url : 'index.php?option=com_secretary&task=message.changeStatus&id=' + id + '&value='+value
			}).done(function(data) {
				alert(data);
			});
		});
		 
		$('#secretary-form-message').keyup(function() {
			var postLength = $(this).val().length;
			$('.counter').text(postLength);
			if(postLength == 0) {
				$('.btn.btn-success').addClass('disabled');
			}
			else {
				$('.btn.btn-success').removeClass('disabled');
			}
		});
	};
	
	angular
	.module('SecretaryChat', ['ngRoute','ngSanitize'])
    .controller('SecretaryChatCtrl', ['$location', '$scope', '$http', '$httpParamSerializer', function($location, $scope, $http, $httpParamSerializer) {
    	 
    	var rootUrl = 'index.php?option=com_secretary&task=';   
        $scope.onlineUsers = null;  
        $scope.lastMsg = null;
        
        $scope.saveMessage = function(form, callback) {
        	$scope.user.refer_to = ($scope.firstMsg !== null ) ? $scope.firstMsg.id : REFERTO;
        	var data = $httpParamSerializer($scope.user);
            if (! ($scope.user.message && $scope.user.message.trim())) {
                return;
            }
            $scope.user.subject = '';
            $scope.user.message = '';
            return $http({
                method: 'POST',
                url: rootUrl+'messages.addMessage',
                data: data,
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            }).success(function(data) {
                $scope.getMessages();
            });
        };
        
        $scope.deleteMsg = function(id) {
        	if(!id) return;
            return $http({
                method: 'DELETE',
                url:  rootUrl+'messages.deleteMessage'+"&id="+id, 
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            }).success(function(data) {
                $scope.getMessages();
            });
        }
        
        $scope.getMessages = function() {
            return $http({
            	method : 'GET',
            	url : rootUrl+'messages.getMessages' + "&rid="+ REFERTO,
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            }).success(function(data) {
                $scope.messages = [];
                angular.forEach(data, function(message) { 
                    $scope.messages.push(message);
                });
                $scope.firstMsg  = ($scope.messages.length >= 1) ? $scope.messages[0] : {};
                var lastMsg = $scope.messages[$scope.messages.length - 1];
                var lastMsgId = lastMsg && lastMsg.id;
                if ($scope.lastMsg && $scope.lastMsg.id !== lastMsgId) {
                	$scope.scrollDown();
                }
                $scope.lastMsg = lastMsg;
            });
        };
        
        $scope.pingServer = function() {
            return $http({ 
            	method : 'POST',
            	url: rootUrl+'messages.ping' + "&rid="+ REFERTO,
            }).success(function(data) {
                $scope.onlineUsers = data;
            });
        };
         
        $scope.changeMsgState = function(itemId){
        	var newState = $scope.selectedState[itemId]; 
        	return $http({
        		method : 'POST',
        		url: rootUrl+'messages.changeState',
                data: $httpParamSerializer({ item : itemId, status : newState}),
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        	});
        };
        
        $scope.init = function() {
            window.setInterval($scope.getMessages, WAITMSG);
            window.setInterval($scope.pingServer, WAITPING);
        };
        
        $scope.scrollDown = function() {
            var p;
            p = window.setInterval(function() {
            	var d = document.getElementsByClassName("secretary-chat-messages")[0];
            	d.scrollTop = window.Number.MAX_SAFE_INTEGER * 0.001;
                window.clearInterval(p);
            }, 100);
        };

        $scope.init();
        $scope.scrollDown();

    }]);
})(jQuery, Secretary);