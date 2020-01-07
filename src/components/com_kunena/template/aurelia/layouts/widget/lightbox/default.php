<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.Widget
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
**/

namespace Joomla\Component\Kunena;

defined('_JEXEC') or die();

use function defined;

if (Config::getInstance()->lightbox != 1)
{
	return false;
}

$this->addStyleSheet('fancybox.css');
$this->addScript('fancybox-min.js');
