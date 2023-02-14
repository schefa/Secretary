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

$backweek = $this->week - 1;
$nextweek = $this->week + 1;

?>
<h2 class="times-week">
    <a class="btn btn-default" href="<?php echo Secretary\Route::create('times', array('week' => $backweek, 'section' => 'week')); ?>"><?php echo JText::_('JPREV'); ?></a>
    <span><?php echo $this->week . '.  ' . JText::_('COM_SECRETARY_CALENDARWEEK'); ?></span>
    <a class="btn btn-default" href="<?php echo Secretary\Route::create('times', array('week' => $nextweek, 'section' => 'week')); ?>"><?php echo JText::_('JNEXT'); ?></a>
</h2>
<div class="week-view clearfix">
    <div class="week-start">
        <div class="week-head">
        </div>
        <div class="week-data">
            <div class="week-row">06:00 - 08:00</div>
            <div class="week-row">08:00 - 10:00</div>
            <div class="week-row">10:00 - 12:00</div>
            <div class="week-row">12:00 - 14:00</div>
            <div class="week-row">14:00 - 16:00</div>
            <div class="week-row">16:00 - 18:00</div>
            <div class="week-row">18:00 - 20:00</div>
            <div class="week-row">20:00 - 22:00</div>
            <div class="week-row">22:00 - 00:00</div>
        </div>
    </div>
    <?php
    for ($day = 1; $day <= 7; $day++) {
        $dayOfWeek = date('Y-m-d', strtotime($this->year . "W" . $this->week . $day));
    ?>
        <div class="week">
            <div class="week-head">
                <h3><?php echo JText::_(\Secretary\Helpers\Times::getWeekDayname($day)); ?></h3>
                <p><?php echo $dayOfWeek; ?></p>
            </div>
            <div class="week-data">
                <div class="week-row"></div>
                <div class="week-row"></div>
                <div class="week-row"></div>
                <div class="week-row"></div>
                <div class="week-row"></div>
                <div class="week-row"></div>
                <div class="week-row"></div>
                <div class="week-row"></div>
                <div class="week-row"></div>
                <?php

                if (isset($this->data[$dayOfWeek])) {
                    $i = 0;
                    $countEvents = count(($this->data[$dayOfWeek] ?? []));
                    $width = "width:" . round(94 / $countEvents, 2) . "%;";
                    foreach ($this->data[$dayOfWeek] as $x => $events) {

                        if ($x > 0) {
                            $leftmargin = ($countEvents >= 1) ?  (100 / $countEvents)  : "";
                            $leftmargin = $leftmargin * ($i);
                            $leftmargin = "left:" . $leftmargin . "%;";
                        } else {
                            $leftmargin = "";
                        }
                ?>
                        <span class="week-time" style="top:<?php echo $events->startHours * 30; ?>px;<?php echo $events->timeColor; ?>height:<?php echo $events->endHours * 30; ?>px;<?php echo $width . $leftmargin; ?>">
                            <a class="week-time-title hasTooltip" title="<?php echo $events->startTime . ' - ' . $events->endTime; ?>" href="<?php echo Secretary\Route::create('time', array('id' => $events->id, 'extension' => $events->extension)); ?>">
                                <?php echo $events->title; ?>
                            </a>
                        </span>
                <?php
                        $i++;
                        unset($leftmargin);
                    }
                }

                ?>
            </div>
        </div>
    <?php } ?>
</div>