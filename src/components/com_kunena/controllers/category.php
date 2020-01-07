<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controllers
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Joomla\Component\Kunena\Site;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;
use function defined;

require_once KPATH_ADMIN . '/controllers/categories.php';

/**
 * Kunena Category Controller
 *
 * @since   Kunena 2.0
 */
class KunenaControllerCategory extends KunenaAdminControllerCategories
{
	/**
	 * @param   array  $config  config
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function __construct($config = [])
	{
		parent::__construct($config);
		$this->baseurl  = 'index.php?option=com_kunena&view=category&layout=manage';
		$this->baseurl2 = 'index.php?option=com_kunena&view=category';
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function jump()
	{
		$catid = $this->app->input->getInt('catid', 0);

		if (!$catid)
		{
			$this->setRedirect(\Joomla\Component\Kunena\Libraries\Route\KunenaRoute::_('index.php?option=com_kunena&view=category&layout=list', false));
		}
		else
		{
			$this->setRedirect(\Joomla\Component\Kunena\Libraries\Route\KunenaRoute::_("index.php?option=com_kunena&view=category&catid={$catid}", false));
		}
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  null
	 * @throws  Exception
	 */
	public function markread()
	{
		if (!Session::checkToken('request'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$catid    = $this->app->input->getInt('catid', 0);
		$children = $this->app->input->getBool('children', 0);

		if (!$catid)
		{
			// All categories
			$session = \Joomla\Component\Kunena\Libraries\KunenaFactory::getSession();
			$session->markAllCategoriesRead();

			if (!$session->save())
			{
				$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_SESSION_SAVE_FAILED'), 'error');
			}
			else
			{
				$this->app->enqueueMessage(Text::_('COM_KUNENA_GEN_ALL_MARKED'));
			}
		}
		else
		{
			// One category
			$category = \Joomla\Component\Kunena\Libraries\Forum\Category\Helper::get($catid);

			if (!$category->isAuthorised('read'))
			{
				$this->app->enqueueMessage($category->getError(), 'error');
				$this->setRedirectBack();

				return;
			}

			$session = \Joomla\Component\Kunena\Libraries\KunenaFactory::getSession();

			if ($session->userid)
			{
				$categories = [$category->id => $category];

				if ($children)
				{
					// Include all levels of child categories.
					$categories += $category->getChildren(-1);
				}

				// Mark all unread topics in selected categories as read.
				KunenaForumCategoryUserHelper::markRead(array_keys($categories));

				if (count($categories) > 1)
				{
					$this->app->enqueueMessage(Text::_('COM_KUNENA_GEN_ALL_MARKED'));
				}
				else
				{
					$this->app->enqueueMessage(Text::_('COM_KUNENA_GEN_FORUM_MARKED'));
				}
			}
		}

		$this->setRedirectBack();
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  null
	 * @throws  Exception
	 */
	public function subscribe()
	{
		if (!Session::checkToken('get'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$category = \Joomla\Component\Kunena\Libraries\Forum\Category\Helper::get($this->app->input->getInt('catid', 0));

		if (!$category->isAuthorised('read'))
		{
			$this->app->enqueueMessage($category->getError(), 'error');
			$this->setRedirectBack();

			return;
		}

		if ($this->me->exists())
		{
			$success = $category->subscribe(1);

			if ($success)
			{
				$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_CATEGORY_USER_SUBCRIBED', $category->name));
			}
		}

		$this->setRedirectBack();
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  null
	 * @throws  Exception
	 */
	public function unsubscribe()
	{
		if (!Session::checkToken('request'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$me = \Joomla\Component\Kunena\Libraries\User\Helper::getMyself();

		$userid = $this->app->input->getInt('userid');

		$catid  = $this->app->input->getInt('catid', 0);
		$catids = $catid
			? [$catid]
			: array_keys($this->app->input->get('categories', [], 'post'));
		$catids = ArrayHelper::toInteger($catids);

		$categories = \Joomla\Component\Kunena\Libraries\Forum\Category\Helper::getCategories($catids);

		foreach ($categories as $category)
		{
			if (!$category->isAuthorised('read'))
			{
				$this->app->enqueueMessage($category->getError(), 'error');
				continue;
			}

			if ($this->me->exists())
			{
				$success = $category->subscribe(0, $userid);

				if ($success && $userid == $me->userid)
				{
					$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_GEN_CATEGORY_NAME_UNSUBCRIBED', $category->name));
				}
				else
				{
					$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_CATEGORY_NAME_MODERATOR_UNSUBCRIBED_USER', $category->name));
				}
			}
		}

		$this->setRedirectBack();
	}
}
