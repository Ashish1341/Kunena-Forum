<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.Message
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Joomla\Component\Kunena\Site;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Plugin\PluginHelper;
use function defined;

/**
 * Class ComponentKunenaControllerCategoryIndexActionsDisplay
 *
 * @since   Kunena 4.0
 */
class ComponentKunenaControllerCategoryIndexActionsDisplay extends KunenaControllerDisplay
{
	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $name = 'Category/Index/Actions';

	/**
	 * @var     KunenaForumTopic
	 * @since   Kunena 6.0
	 */
	public $category;

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	public $categoryButtons;

	/**
	 * Prepare message actions display.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	protected function before()
	{
		parent::before();

		$catid = $this->input->getInt('id');
		$me    = KunenaUserHelper::getMyself();

		$this->category = KunenaForumCategory::getInstance($catid);

		$token = Session::getFormToken();

		$task   = "index.php?option=com_kunena&view=category&task=%s&catid={$catid}&{$token}=1";
		$layout = "index.php?option=com_kunena&view=topic&layout=%s&catid={$catid}";

		$this->template        = KunenaFactory::getTemplate();
		$this->categoryButtons = new CMSObject;

		// Is user allowed to post new topic?
		if ($this->category->isAuthorised('topic.create'))
		{
			$this->categoryButtons->set('create',
				$this->getButton(sprintf($layout, 'create'), 'create', 'topic', 'communication', true)
			);
		}

		// Is user allowed to mark forums as read?
		if ($me->exists())
		{
			$this->categoryButtons->set('markread',
				$this->getButton(sprintf($task, 'markread'), 'markread', 'category', 'user', true)
			);
		}

		// Is user allowed to subscribe category?
		if ($this->category->isAuthorised('subscribe'))
		{
			$subscribed = $this->category->getSubscribed($me->userid);

			if (!$subscribed)
			{
				$this->categoryButtons->set('subscribe',
					$this->getButton(sprintf($task, 'subscribe'), 'subscribe', 'category', 'user', true)
				);
			}
			else
			{
				$this->categoryButtons->set('unsubscribe',
					$this->getButton(sprintf($task, 'unsubscribe'), 'unsubscribe', 'category', 'user', true)
				);
			}
		}

		PluginHelper::importPlugin('kunena');

		$this->app->triggerEvent('onKunenaGetButtons', ['category.action', $this->categoryButtons, $this]);
	}

	/**
	 * Get button.
	 *
	 * @param   string  $url    Target link (do not route it).
	 * @param   string  $name   Name of the button.
	 * @param   string  $scope  Scope of the button.
	 * @param   string  $type   Type of the button.
	 * @param   bool    $id     Id of the button.
	 *
	 * @return  KunenaLayout
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function getButton($url, $name, $scope, $type, $id = null)
	{
		return KunenaLayout::factory('Widget/Button')
			->setProperties(['url' => KunenaRoute::_($url), 'name' => $name, 'scope' => $scope, 'type' => $type, 'id' => $id]);
	}
}
