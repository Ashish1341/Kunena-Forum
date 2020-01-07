<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.Widget
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Joomla\Component\Kunena;

defined('_JEXEC') or die();

use Exception;
use function defined;

/**
 * Class ComponentKunenaControllerWidgetAnnouncementDisplay
 *
 * @since   Kunena 4.0
 */
class ComponentKunenaControllerWidgetAnnouncementDisplay extends KunenaControllerDisplay
{
	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $name = 'Widget/Announcement';

	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	public $announcement;

	/**
	 * Prepare announcement box display.
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	protected function before()
	{
		parent::before();

		$config = Config::getInstance();

		if (!$config->showannouncement)
		{
			return false;
		}

		$items              = KunenaForumAnnouncementHelper::getAnnouncements();
		$this->announcement = array_pop($items);

		if (!$this->announcement || !$this->announcement->isAuthorised('read'))
		{
			return false;
		}

		$view   = $this->input->getWord('view', 'default');
		$layout = $this->input->getWord('layout', 'default');

		if ($view == 'topic' && $layout != 'default' || $view == 'user' || $view == 'search' || $view == 'announcement' && $layout == 'default')
		{
			return false;
		}

		return true;
	}
}
