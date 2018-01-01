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

$user		= Secretary\Joomla::getUser();
$userId		= (int) Secretary\Database::getQuery('subjects', (int) $user->id,'created_by','id','loadResult');

$started = array();
$expandedParent = 0;
$expanded = 0;
$taskCount = 0;
$firstNoProject = false;
foreach ($this->items as $i => $item) : 
    
    if(!is_int($i)) {
        continue;
    }
    
	$prevItem = isset($this->items[$i-1]) ? $this->items[$i-1] : false;
	$nextItem = isset($this->items[$i+1]) ? $this->items[$i+1] : false; 
	
	if($i == 0 && $item->task_id > 0) {
		$firstNoProject = true;
		$firstLevel = $item->level; 
	}
			
	// Project tasks
	if($item->task_id > 0) { 
		$taskCount++;
		
		$sortable = '';
		if((($nextItem !== false && $nextItem->parent_id == $item->parent_id)
			or ($prevItem !== false && $prevItem->parent_id == $item->parent_id)) && $item->level != 1) {
			$sortable = '';
		}
		
		// Project Task begins 
		echo '<!-- Aufgabe Start --><div class="secretary-sort-row'.$sortable.'">';
		
		Secretary\HTML::_('times.listViewProjectTaskRow', $i , $item, $userId, $this->vorspannSecs, $this->intervall);
		
		if ($nextItem  == true) {
		    // Next item is TASK
			if( isset($nextItem->parent_id) && $nextItem->parent_id >= 0) { 
				// next has Parent = TASK 
			    if( $nextItem->parent_id == $item->task_id && $nextItem->level > $item->level) {
					// start list new level
					echo '<!-- Liste Start 1 --><div class="secretary-row-down fullwidth">';
					$expanded = $item->level ;
				} elseif( $nextItem->level == $item->level) {
					// Gleiches Level
					echo '</div><!-- Aufgabe Ende 1 -->';
				} elseif( $nextItem->parent_id != $item->parent_id) {
					echo '</div><!-- Aufgabe Ende 2 -->';
					// new level  Liste Ende 
					$expanded = $item->level - 1;
					$levelDifference = $item->level - $nextItem->level;
					if( $levelDifference >= 1) {
					    
						if($i == 0 && $expandedParent == 0) {
							$expandedParent++; 
							echo '</div>';
						} elseif($firstNoProject && ($expandedParent >= 1 || $levelDifference >= 1)) {
							$expandedParent--; 
						} elseif(!$firstNoProject) { 
							echo str_repeat('</div><!--  Liste Ende 3 -->
                                            </div><!-- Aufgabe Ende 3 -->', $levelDifference );
						}
						
					}	  			
				}
			} else {
			    
			    // Next is PROJECT
				$expanded = $item->level - 1; 
				if($firstNoProject) { 
				    $expandedParent = 0;
				} 
				if($expanded > 0 || $expandedParent > 0) {
				    if(!$firstNoProject)  { 
						// project list close 
						if($expanded > 0 ) {
							echo  str_repeat('</div><!-- Aufgabe Ende 4 --></div><!--  Liste Ende 4 -->', $expanded ); 
						}
						echo  str_repeat('</div>', $expandedParent );
				    } else {
				        echo '</div><!-- Aufgabe Ende 1 -->';
					}
					 
					$expanded = 0;
					$expandedParent = 0;
				}
			}
		} elseif($nextItem === false) {
			$expanded = $item->level - 1;
			if($expanded > 0 || $expandedParent > 0) { 
				if($firstNoProject) { 
					$expandedParent = 0;
				    $expanded = $expanded - $firstLevel + 1; 
				} 
				if($expanded > 0 ) {  
			   		echo  str_repeat('</div><!-- Aufgabe Ende --></div><!-- Liste Ende --> ', $expanded );
				} elseif(!$firstNoProject) echo '</div><!-- Aufgabe Ende --></div><!-- Liste Ende -->';
			  	echo  str_repeat('</div> ', $expandedParent );
			  	
				$expanded = 0;
				$expandedParent = 0;
			}
		}
		
		// Close Project DIV
	 	if($item->tasks_count == $taskCount) echo '</div></div>'; 
		
	// Projects
	} else {
		$taskCount = 0;
	?>
        
        <div class="secretary-row fullwidth row<?php echo ($i % 2) . ' times-list-'. $item->extension; ?> secretary-sort-row">
            
		<?php Secretary\HTML::_('times.listViewProject', $i , $item, $this->vorspannSecs, $this->intervall); ?>
	
	<?php
        // Project has tasks
		if( $item->tasks_count > 0 ) {
		    // Projekt div offen
			$expandedParent = 1;
			echo '<div class="fullwidth project-down-row secretary-row-down">';
			// hat Aufgaben, aber nicht sichtbar, dann schliessen
			if($nextItem === false) {
				$expandedParent = 0;
				echo '</div></div>';
			}
		} else {
			echo '</div>';
		}
	} 
	
	endforeach;
	
	if($expanded > 0) {
		echo str_repeat('</div>', $expanded); 
	}
	
 ?> 