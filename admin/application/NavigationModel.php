<?php
/**
 * @version     3.0.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
 */

namespace Secretary\NavigationModel;

// No direct access
defined('_JEXEC') or die;

interface INavigationType {}

class Headline implements INavigationType {
    public $title;
    public $headline;
    
    public function __construct( $title,$headline = true) {
        $this->title =  $title;
        $this->headline =  $headline;
    }
}

class HeadlineAccordion extends Headline {
    public $id;
    public $icon;
    public $title;
    public $class;
    public $accordionHeadline;
    public $headline;
    public $separator;
    public $section;
    public $links;
    public $counter;
    
    public function __construct( $id, $icon, $title, $class = '', $accordionHeadline = false, $headline = false, $separator = NULL, $section = NULL, $links = NULL, $counter = NULL) {
        $this->id =  $id;
        $this->icon =  $icon;
        $this->title =  $title;
        $this->class =  $class;
        $this->accordionHeadline =  $accordionHeadline;
        $this->headline =  $headline;
        $this->separator =  $separator;
        $this->section =  $section;
        $this->links =  $links;
        $this->counter =  $counter;
    }
}

class Item implements INavigationType {
    
    public $id;
    public $url;
    public $icon;
    public $title;
    public $horzline;
    
    public function __construct($id, $link, $icon, $title, $horzline = false) {
        $this->id           = $id;
        $this->url          = $link;
        $this->icon         = $icon;
        $this->title        = $title;
        $this->horzline     = $horzline;
    }
}

class ItemAccordion extends Item {
    public $id;
    public $icon;
    public $title;
    public $class; 
    public $separator;
    public $section;
    public $url;
    public $counter;
    
    public function __construct( $id, $title, $class = '', $icon, $separator = NULL, $section = NULL, $links = NULL, $counter = NULL) {
        $this->id =  $id;
        $this->icon =  $icon;
        $this->title =  $title;
        $this->class =  $class;  
        $this->separator =  $separator;
        $this->section =  $section;
        $this->url =  $links;
        $this->counter =  $counter;
    }
} 

class SubItem extends Item {
    
    public $pid;
    public $url;
    public $icon;
    public $title;
    public $catid;
    
    public function __construct($pid, $link, $title, $icon = NULL, $catid = NULL) {
        $this->pid      = $pid;
        $this->url      = $link;
        $this->icon     = $icon;
        $this->title    = $title;
        $this->catid    = $catid;
    }
}

class Factory {
    public static function create( INavigationType $object ) {
        return $object;
    }
}

