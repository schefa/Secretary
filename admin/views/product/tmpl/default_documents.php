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
defined('_JEXEC') or die;

$model = \Secretary\Model::create('document');
$histories = json_decode($this->item->history);

echo '<div class="row-fluid">';
echo '<h3 class="title">'. JText::_('COM_SECRETARY_HISTORY') .'</h3>';

if(!empty($histories))
{
    echo '<table class="table table-hover">';
    echo '<tr>';
    echo '<td>'.'</td>';
    echo '<td>'.JText::_('COM_SECRETARY_DOCUMENT').'</td>';
    echo '<td>'.JText::_('COM_SECRETARY_QUANTITY').'</td>';
    echo '<td>'.JText::_('COM_SECRETARY_PRICE').'</td>';
    echo '<td>'.JText::_('COM_SECRETARY_TOTAL').'</td>';
    echo '</tr>';
    foreach($histories AS $key => $history)
    {
        foreach($history AS $timestamp => $values)
        {
            $id = Secretary\Database::getQuery('documents', $timestamp,'createdEntry','id','loadResult');
            $document = $model->getItem($id); 
            echo '<tr>';
                echo '<td>'.'</td>';
                echo '<td>';
                echo $document->created .' / ';
                if(!is_null($document->category)) echo JText::_($document->category->alias);
                echo '</td>';
                echo '<td>'. $values[1] .'</td>';
                echo '<td>'. Secretary\Utilities\Number::getNumberFormat($values[2],$document->currencySymbol) .'</td>';
                echo '<td>'. Secretary\Utilities\Number::getNumberFormat($values[3],$document->currencySymbol) .'</td>';
            echo '</tr>';
        }
    }
    echo '</table>';
}

echo '</div>';
 ?>
    