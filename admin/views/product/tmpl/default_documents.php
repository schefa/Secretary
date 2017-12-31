<?php
/**
 * @version     3.0.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
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
    