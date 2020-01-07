<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.Application
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Component\ComponentHelper;
use function defined;

/**
 * Class ComponentKunenaControllerApplicationMiscDisplay
 *
 * @since   Kunena 4.0
 */
class ComponentKunenaControllerApplicationMiscDefaultDisplay extends KunenaControllerApplicationDisplay
{
	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $header;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $body;

	/**
	 * Return custom display layout.
	 *
	 * @return  KunenaLayout
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	protected function display()
	{
		$menu_item = $this->app->getMenu()->getActive();

		$doc             = Factory::getApplication()->getDocument();
		$componentParams = ComponentHelper::getParams('com_config');
		$robots          = $componentParams->get('robots');

		if ($robots == '')
		{
			$doc->setMetaData('robots', 'index, follow');
		}
		elseif ($robots == 'noindex, follow')
		{
			$doc->setMetaData('robots', 'noindex, follow');
		}
		elseif ($robots == 'index, nofollow')
		{
			$doc->setMetaData('robots', 'index, nofollow');
		}
		else
		{
			$doc->setMetaData('robots', 'nofollow, noindex');
		}

		if ($menu_item)
		{
			$params             = $menu_item->getParams();
			$params_title       = $params->get('page_title');
			$params_keywords    = $params->get('menu-meta_keywords');
			$params_description = $params->get('menu-meta_description');
			$params_robots      = $params->get('robots');

			if (!empty($params_title))
			{
				$title = $params->get('page_title');
				$doc->setTitle($title);
			}
			else
			{
				$title = $this->config->board_title;
				$doc->setTitle($title);
			}

			if (!empty($params_description))
			{
				$description = $params->get('menu-meta_description');
				$doc->setDescription($description);
			}
			else
			{
				$description = $this->config->board_title;
				$doc->setDescription($description);
			}

			if (!empty($params_robots))
			{
				$robots = $params->get('robots');
				$doc->setMetaData('robots', $robots);
			}
		}

		// Display layout with given parameters.
		$content = KunenaLayoutPage::factory('Misc/Default')
			->set('header', $this->header)
			->set('body', $this->body);

		return $content;
	}

	/**
	 * Prepare custom text output.
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

		$params       = ComponentHelper::getParams('com_kunena');
		$this->header = $params->get('page_title');
		$Itemid       = $this->input->getInt('Itemid');

		if (!$Itemid)
		{
			if ($this->config->custom_id)
			{
				$itemidfix = $this->config->custom_id;
			}
			else
			{
				$menu      = $this->app->getMenu();
				$getid     = $menu->getItem(KunenaRoute::getItemID("index.php?option=com_kunena&view=misc"));
				$itemidfix = $getid->id;
			}

			if (!$itemidfix)
			{
				$itemidfix = KunenaRoute::fixMissingItemID();
			}

			$controller = BaseController::getInstance("kunena");
			$controller->setRedirect(KunenaRoute::_("index.php?option=com_kunena&view=misc&Itemid={$itemidfix}", false));
			$controller->redirect();
		}

		$body   = $params->get('body');
		$format = $params->get('body_format');

		$this->header = htmlspecialchars($this->header, ENT_COMPAT, 'UTF-8');

		if ($format == 'html')
		{
			$this->body = trim($body);
		}
		elseif ($format == 'text')
		{
			$this->body = function () use ($body) {

				return htmlspecialchars($body, ENT_COMPAT, 'UTF-8');
			};
		}
		else
		{
			$this->body = function () use ($body) {

				$cache = Factory::getCache('com_kunena', 'callback');
				$cache->setLifeTime(180);

				return $cache->get(['KunenaHtmlParser', 'parseBBCode'], [$body]);
			};
		}
	}
}
