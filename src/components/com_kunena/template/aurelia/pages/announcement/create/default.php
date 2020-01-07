<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Pages.Announcement
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
**/

namespace Kunena;

defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use function defined;

$content = $this->execute('Announcement/Edit');

$this->addBreadcrumb(
	Text::_('COM_KUNENA_ANN_ANNOUNCEMENTS'),
	'index.php?option=com_kunena&view=announcement&layout=list'
);

echo $content;
