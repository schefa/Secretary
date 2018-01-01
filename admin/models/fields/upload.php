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
 
// No direct access
defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

class JFormFieldUpload extends JFormFieldList
{
	
	protected $type = 'upload';
	protected static $_items = array();
	
	function getInput()
	{
		
	    $fileId			= \Secretary\Joomla::getApplication()->input->getInt('secf','');
	    $canUpload		= \Secretary\Joomla::getUser()->authorise('core.upload', 'com_secretary');
		$documentSize	= \Secretary\Application::parameters()->get('documentSize');
		$imageWidth		= isset($this->element['width']) ? intval($this->element['width']) : 200;
		
		$html = array();
		
		if(!empty($this->value)) {
			$logoImage = Secretary\Database::getQuery('uploads', $this->value, 'id');
			$file 		= \Secretary\Helpers\Uploads::getUploadFile($logoImage, '', $imageWidth, TRUE);
			
			if($file) {
				$html[] = '<div class="upload-file fullwidth">'. $file . '</div>';
			}
			
			if($canUpload && $file && empty($fileId)) {
				
				$html[] = '<div class="upload-file-delete">';
				$html[] = 	'<input type="checkbox" name="deleteDocument" >&nbsp;'. JText::_('COM_SECRETARY_DELETE');
				$html[] = 	'<input type="hidden" value="'.$this->value.'" name="jform[upload_title]">';
				$html[] = '</div>';
				
			} elseif(!empty($fileId)) {
				$html[] = '<input type="hidden" name="secf" value="'. $fileId .'" />';
            }
		}
		 
		if($canUpload) {
			$html[] = '<div class="upload-file"><input type="file" name="'.$this->name.'" id="'.$this->id.'"></div>';
			$html[] = '<p class="secretary-desc fullwidth">'. JText::_('COM_SECRETARY_DOCUMENT_SIZE_ALLOWED') .' '. \Secretary\Utilities\Number::human_filesize($documentSize) . 'B</p>';
		} else {
			$html[] = '<div class="alert alert-warning">'.JText::_('COM_SECRETARY_PERMISSION_FAILED').'</div>';
		}
		
		return implode("\n",$html);
	}
	
}