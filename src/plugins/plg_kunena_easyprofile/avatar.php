<?php
/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Easyprofile
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Joomla\Component\Kunena;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\Registry\Registry;
use function defined;

/**
 * Class KunenaAvatarEasyprofile
 *
 * @since   Kunena 6.0
 */
class KunenaAvatarEasyprofile extends KunenaAvatar
{
	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	protected $params = null;

	/**
	 * KunenaAvatarEasyprofile constructor.
	 *
	 * @param   object  $params params
	 *
	 * @since   Kunena 6.0
	 */
	public function __construct($params)
	{
		$this->params = $params;
	}

	/**
	 * @return  mixed
	 *
	 * @since   Kunena 6.0
	 */
	public function getEditURL()
	{
		return Route::_('index.php?option=com_jsn&view=profile');
	}

	/**
	 * @param   object  $user  user
	 * @param   int     $sizex sizex
	 * @param   int     $sizey sizey
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function _getURL($user, $sizex, $sizey)
	{
		if (!$user->userid == 0)
		{
			$user = KunenaFactory::getUser($user->userid);
			$user = JsnHelper::getUser($user->userid);

			if ($sizex <= 50)
			{
				$avatar = Uri::root(true) . '/' . $user->getValue('avatar_mini');
			}
			else
			{
				$avatar = Uri::root(true) . '/' . $user->getValue('avatar');
			}
		}
		elseif ($this->params->get('guestavatar', "easyprofile") == "easyprofile")
		{
			$avatar = Uri::root(true) . '/components/com_jsn/assets/img/default.jpg';
		}
		else
		{
			$db    = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select($db->quoteName('params'))
				->from('#__jsn_fields')
				->where('alias = \'avatar\'');
			$db->setQuery($query);
			$params   = $db->loadResult();
			$registry = new Registry;
			$registry->loadString($params);
			$params = $registry->toArray();

			if ($params['image_defaultvalue'] <> "")
			{
				$avatar = Uri::root(true) . '/' . $params['image_defaultvalue'];
			}
			else
			{
				$avatar = Uri::root(true) . '/components/com_jsn/assets/img/default.jpg';
			}
		}

		return $avatar;
	}
}
