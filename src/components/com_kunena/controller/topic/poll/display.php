<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.Topic
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Joomla\Component\Kunena\Site;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Language\Text;
use function defined;

/**
 * Class ComponentKunenaControllerTopicPollDisplay
 *
 * @since   Kunena 4.0
 */
class ComponentKunenaControllerTopicPollDisplay extends KunenaControllerDisplay
{
	/**
	 * @var     KunenaUser
	 * @since   Kunena 6.0
	 */
	public $me;

	/**
	 * @var     KunenaForumCategory
	 * @since   Kunena 6.0
	 */
	public $category;

	/**
	 * @var     KunenaForumTopic
	 * @since   Kunena 6.0
	 */
	public $topic;

	/**
	 * @var     KunenaForumTopicPoll
	 * @since   Kunena 6.0
	 */
	public $poll;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $uri;

	/**
	 * Prepare poll display.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  null
	 * @throws  Exception
	 */
	protected function before()
	{
		parent::before();

		$this->topic    = \Joomla\Component\Kunena\Libraries\Forum\Topic\Helper::get($this->input->getInt('id'));
		$this->category = $this->topic->getCategory();
		$this->config   = \Joomla\Component\Kunena\Libraries\KunenaFactory::getConfig();
		$this->me       = \Joomla\Component\Kunena\Libraries\User\Helper::getMyself();

		// Need to check if poll is allowed in this category.
		$this->topic->tryAuthorise('poll.read');

		$this->poll       = $this->topic->getPoll();
		$this->usercount  = $this->poll->getUserCount();
		$this->usersvoted = $this->poll->getUsers();

		if (is_object($this->poll->getMyVotes()))
		{
			$this->userhasvoted = $this->poll->getMyVotes();
		}
		else
		{
			$this->userhasvoted = 0;
		}

		if (!empty($this->alwaysVote))
		{
			// Authorise forced vote.
			$this->topic->tryAuthorise('poll.vote');
			$this->topic->tryAuthorise('reply');
			$this->name = 'Topic/Poll/Vote';
		}
		elseif (!$this->userhasvoted && $this->topic->isAuthorised('poll.vote') && $this->topic->isAuthorised('reply'))
		{
			$this->name = 'Topic/Poll/Vote';
		}
		else
		{
			$this->name = 'Topic/Poll/Results';

			$this->show_title = true;

			$this->users_voted_list     = [];
			$this->users_voted_morelist = [];

			if ($this->config->pollresultsuserslist && !empty($this->usersvoted))
			{
				$userids_votes = [];

				foreach ($this->usersvoted as $userid => $vote)
				{
					$userids_votes[] = $userid;
				}

				$loaded_users = \Joomla\Component\Kunena\Libraries\User\Helper::loadUsers($userids_votes);

				$i = 0;

				foreach ($loaded_users as $userid => $user)
				{
					if ($i <= '4')
					{
						$this->users_voted_list[] = $loaded_users[$userid]->getLink();
					}
					else
					{
						$this->users_voted_morelist[] = $loaded_users[$userid]->getLink();
					}

					$i++;
				}
			}
		}

		$this->uri = "index.php?option=com_kunena&view=topic&layout=poll&catid={$this->category->id}&id={$this->topic->id}";
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
		$menu_item = $this->app->getMenu()->getActive();

		if ($menu_item)
		{
			$params             = $menu_item->getParams();
			$params_title       = $params->get('page_title');
			$params_keywords    = $params->get('menu-meta_keywords');
			$params_description = $params->get('menu-meta_description');

			if (!empty($params_title))
			{
				$title = $params->get('page_title');
				$this->setTitle($title);
			}
			else
			{
				$this->setTitle(Text::_('COM_KUNENA_POLL_NAME') . ' ' . \Joomla\Component\Kunena\Libraries\Html\Parser::parseText($this->poll->title));
			}

			if (!empty($params_keywords))
			{
				$keywords = $params->get('menu-meta_keywords');
				$this->setKeywords($keywords);
			}
			else
			{
				$this->setKeywords(Text::_('COM_KUNENA_POLL_NAME') . ' ' . \Joomla\Component\Kunena\Libraries\Html\Parser::parseText($this->poll->title));
			}

			if (!empty($params_description))
			{
				$description = $params->get('menu-meta_description');
				$this->setDescription($description);
			}
			else
			{
				$this->setDescription(Text::_('COM_KUNENA_POLL_NAME') . ' ' . \Joomla\Component\Kunena\Libraries\Html\Parser::parseText($this->poll->title));
			}
		}
	}
}
