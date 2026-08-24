<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */

// com_secretary's helper files all start with `defined('_JEXEC') or die;` as
// their Joomla direct-access guard - define it here so they can be loaded
// standalone under PHPUnit, without a full Joomla bootstrap.
if (!defined('_JEXEC'))
{
    define('_JEXEC', 1);
}

require_once __DIR__ . '/vendor/autoload.php';
