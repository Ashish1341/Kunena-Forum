<?php
/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      AltaUserPoints
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena;

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;
use function defined;

/**
 * plgKunenaAltaUserPoints class to handle integration with AltaUserPoints
 *
 * @since  5.0
 */
class plgKunenaAltaUserPoints extends CMSPlugin
{
	/**
	 * Constructor of plgKunenaAltaUserPoints class
	 *
	 * @param   object &$subject  The object to observe
	 * @param   array   $config   An optional associative array of configuration settings.
	 *
	 * @since   Kunena 6.0
	 */
	public function __construct(&$subject, $config)
	{
		// Do not load if Kunena version is not supported or Kunena is offline
		if (!(class_exists('KunenaForum') && KunenaForum::isCompatible('4.0') && KunenaForum::installed()))
		{
			return;
		}

		$aup = JPATH_SITE . '/components/com_altauserpoints/helper.php';

		if (!file_exists($aup))
		{
			if (PluginHelper::isEnabled('kunena', 'altauserpoints'))
			{
				$db    = Factory::getDBO();
				$query = $db->getQuery(true);
				$query->update('`#__extensions`');
				$query->where($db->quoteName('element') . ' = ' . $db->quote('altauserpoints'));
				$query->where($db->quoteName('type') . ' = ' . $db->quote('plugin'));
				$query->where($db->quoteName('folder') . '= ' . $db->quote('kunena'));
				$query->set($db->quoteName('enabled') . ' = 0');
				$db->setQuery($query);
				$db->execute();
			}

			return;
		}

		require_once $aup;

		parent::__construct($subject, $config);

		$this->loadLanguage('plg_kunena_altauserpoints.sys', JPATH_ADMINISTRATOR) || $this->loadLanguage('plg_kunena_altauserpoints.sys', KPATH_ADMIN);
	}

	/**
	 * Get Kunena avatar integration object.
	 *
	 * @return  KunenaAvatar|void
	 * @since   Kunena 6.0
	 */
	public function onKunenaGetAvatar()
	{
		if (!$this->params->get('avatar', 1))
		{
			return;
		}

		require_once __DIR__ . "/avatar.php";

		return new KunenaAvatarAltaUserPoints($this->params);
	}

	/**
	 * Get Kunena profile integration object.
	 *
	 * @return  KunenaProfile|void
	 * @since   Kunena 6.0
	 */
	public function onKunenaGetProfile()
	{
		if (!$this->params->get('profile', 1))
		{
			return;
		}

		require_once __DIR__ . "/profile.php";

		return new KunenaProfileAltaUserPoints($this->params);
	}

	/**
	 * Get Kunena activity stream integration object.
	 *
	 * @return  KunenaActivity|void
	 * @since   Kunena 6.0
	 */
	public function onKunenaGetActivity()
	{
		if (!$this->params->get('activity', 1))
		{
			return;
		}

		require_once __DIR__ . "/activity.php";

		return new KunenaActivityAltaUserPoints($this->params);
	}
}
