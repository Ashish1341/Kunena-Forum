<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.Statistics
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Language\Text;
use function defined;

/**
 * Class ComponentKunenaControllerStatisticsWhoisonlineDisplay
 *
 * @since   Kunena 4.0
 */
class ComponentKunenaControllerWidgetWhoisonlineDisplay extends KunenaControllerDisplay
{
	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $name = 'Widget/WhoIsOnline';

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $usersUrl;

	/**
	 * Prepare Who is online display.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	protected function before()
	{
		parent::before();

		$this->config = Config::getInstance();

		if (!$this->config->get('showwhoisonline'))
		{
			throw new KunenaExceptionAuthorise(Text::_('COM_KUNENA_NO_ACCESS'), '404');
		}

		$me        = KunenaUserHelper::getMyself();
		$moderator = intval($me->isModerator()) + intval($me->isAdmin());

		$users = KunenaUserHelper::getOnlineUsers();
		KunenaUserHelper::loadUsers(array_keys($users));
		$onlineusers = KunenaUserHelper::getOnlineCount();

		$who = '<strong>' . $onlineusers['user'] . ' </strong>';

		if ($onlineusers['user'] == 1)
		{
			$who .= Text::_('COM_KUNENA_WHO_ONLINE_MEMBER') . '&nbsp;';
		}
		else
		{
			$who .= Text::_('COM_KUNENA_WHO_ONLINE_MEMBERS') . '&nbsp;';
		}

		$who .= Text::_('COM_KUNENA_WHO_AND');
		$who .= '<strong> ' . $onlineusers['guest'] . ' </strong>';

		if ($onlineusers['guest'] == 1)
		{
			$who .= Text::_('COM_KUNENA_WHO_ONLINE_GUEST') . '&nbsp;';
		}
		else
		{
			$who .= Text::_('COM_KUNENA_WHO_ONLINE_GUESTS') . '&nbsp;';
		}

		$who                 .= Text::_('COM_KUNENA_WHO_ONLINE_NOW');
		$this->membersOnline = $who;

		$this->onlineList = [];
		$this->hiddenList = [];

		foreach ($users as $userid => $usertime)
		{
			$user = KunenaUserHelper::get($userid);

			if (!$user->showOnline)
			{
				if ($moderator)
				{
					$this->hiddenList[$user->getName()] = $user;
				}
			}
			else
			{
				$this->onlineList[$user->getName()] = $user;
			}
		}

		ksort($this->onlineList);
		ksort($this->hiddenList);

		$profile        = KunenaFactory::getProfile();
		$this->usersUrl = $profile->getUserListURL();
	}

	/**
	 * Prepare document.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	protected function prepareDocument()
	{
		$this->setTitle(Text::_('COM_KUNENA_MENU_STATISTICS_WHOSONLINE'));
	}
}
