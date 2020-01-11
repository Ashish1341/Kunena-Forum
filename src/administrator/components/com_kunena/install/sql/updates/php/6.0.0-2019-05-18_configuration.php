<?php
/**
 * Kunena Component
 *
 * @package        Kunena.Installer
 *
 * @copyright      Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\Updates\Php;

defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use function defined;

// Kunena 6.0.0: Set Aurelia as default template in config when update
/**
 * @param   string  $parent parent
 *
 * @return  array
 *
 * @since   Kunena 6.0
 *
 * @throws  \Exception
 */
function kunena_600_2019_05_18_configuration($parent)
{
	$config = \Kunena\Forum\Libraries\Factory\KunenaFactory::getConfig();

	if (isset($config->template))
	{
		if ($config->template == 'crypsis' || $config->template == 'crypsisb4')
		{
			$config->set('template', 'aurelia');
		}
	}

	// Save configuration
	$config->save();

	return ['action' => '', 'name' => Text::_('COM_KUNENA_INSTALL_600_CONFIGURATION'), 'success' => true];
}
