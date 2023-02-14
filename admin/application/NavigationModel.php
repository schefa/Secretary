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

namespace Secretary\NavigationModel;

// No direct access
defined('_JEXEC') or die;

interface INavigationType
{
}

class Headline implements INavigationType
{
    public $title;
    public $headline;

    public function __construct($title, $headline = true)
    {
        $this->title = $title;
        $this->headline = $headline;
    }
}

class HeadlineAccordion extends Headline
{
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

    public function __construct($id, $icon, $title, $class = '', $accordionHeadline = false, $headline = false, $separator = NULL, $section = NULL, $links = NULL, $counter = NULL)
    {
        $this->id = $id;
        $this->icon = $icon;
        $this->title = $title;
        $this->class = $class;
        $this->accordionHeadline = $accordionHeadline;
        $this->headline = $headline;
        $this->separator = $separator;
        $this->section = $section;
        $this->links = $links;
        $this->counter = $counter;
    }
}

class Item implements INavigationType
{

    public $id;
    public $url;
    public $icon;
    public $title;
    public $horzline;

    public function __construct($id, $link, $icon, $title, $horzline = false)
    {
        $this->id = $id;
        $this->url = $link;
        $this->icon = $icon;
        $this->title = $title;
        $this->horzline = $horzline;
    }
}

class ItemAccordion extends Item
{
    public $id;
    public $icon;
    public $title;
    public $class;
    public $separator;
    public $section;
    public $url;
    public $counter;

    public function __construct($id, $title, $class = '', $icon = NULL, $separator = NULL, $section = NULL, $links = NULL, $counter = NULL)
    {
        $this->id = $id;
        $this->icon = $icon;
        $this->title = $title;
        $this->class = $class;
        $this->separator = $separator;
        $this->section = $section;
        $this->url = $links;
        $this->counter = $counter;
    }
}

class SubItem extends Item
{

    public $pid;
    public $url;
    public $icon;
    public $title;
    public $catid;

    public function __construct($pid, $link, $title, $icon = NULL, $catid = NULL)
    {
        $this->pid = $pid;
        $this->url = $link;
        $this->icon = $icon;
        $this->title = $title;
        $this->catid = $catid;
    }
}

class Factory
{
    public static function create(INavigationType $object)
    {
        return $object;
    }
}