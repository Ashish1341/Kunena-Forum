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

namespace Joomla\Component\Kunena\Site\Controller\Topic\Form\History;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Component\Kunena\Libraries\Attachment\Helper;
use Joomla\Component\Kunena\Libraries\Controller\Display;
use Joomla\Registry\Registry;
use function defined;

/**
 * Class ComponentKunenaControllerTopicFormHistoryDisplay
 *
 * TODO: merge to another controller...
 *
 * @since   Kunena 4.0
 */
class ComponentKunenaControllerTopicFormHistoryDisplay extends Display
{
	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $name = 'Topic/Edit/History';

	/**
	 * Prepare reply history display.
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

		$id       = $this->input->getInt('id');
		$this->me = \Joomla\Component\Kunena\Libraries\User\Helper::getMyself();

		$this->topic    = \Joomla\Component\Kunena\Libraries\Forum\Topic\Helper::get($id);
		$this->category = $this->topic->getCategory();
		$this->history  = \Joomla\Component\Kunena\Libraries\Forum\Message\Helper::getMessagesByTopic(
			$this->topic, 0, (int) $this->config->historylimit, 'DESC'
		);

		$this->replycount   = $this->topic->getReplies();
		$this->historycount = count($this->history);
		Helper::getByMessage($this->history);
		$userlist = [];

		foreach ($this->history as $message)
		{
			$messages[$message->id]           = $message;
			$userlist[(int) $message->userid] = (int) $message->userid;
		}

		if ($this->me->exists())
		{
			$pmFinder = new KunenaPrivateMessageFinder;
			$pmFinder->filterByMessageIds(array_keys($messages))->order('id');

			if (!$this->me->isModerator($this->category))
			{
				$pmFinder->filterByUser($this->me);
			}

			$pms = $pmFinder->find();

			foreach ($pms as $pm)
			{
				$registry = new Registry($pm->params);
				$posts    = $registry->get('receivers.posts');

				foreach ($posts as $post)
				{
					if (!isset($messages[$post]->pm))
					{
						$messages[$post]->pm = [];
					}

					$messages[$post]->pm[$pm->id] = $pm;
				}
			}
		}

		$this->history = $messages;

		\Joomla\Component\Kunena\Libraries\User\Helper::loadUsers($userlist);

		// Run events
		$params = new Registry;
		$params->set('ksource', 'kunena');
		$params->set('kunena_view', 'topic');
		$params->set('kunena_layout', 'history');

		PluginHelper::importPlugin('kunena');

		Factory::getApplication()->triggerEvent('onKunenaPrepare', ['kunena.messages', &$this->history, &$params, 0]);

		// FIXME: need to improve BBCode class on this...
		$this->attachments        = Helper::getByMessage($this->history);
		$this->inline_attachments = [];

		$this->headerText = Text::_('COM_KUNENA_POST_EDIT') . ' ' . $this->topic->subject;
	}

	/**
	 * Prepare document.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function prepareDocument()
	{

	}
}
