<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Activity
 *
 * @copyright     Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Activity;

defined('_JEXEC') or die();

use function defined;

/**
 * Class KunenaActivity
 *
 * @since   Kunena 2.0
 */
class Activity
{
	/**
	 * Triggered before posting a new topic.
	 *
	 * @param   string  $message  Message
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function onBeforePost($message)
	{
	}

	/**
	 * Triggered after posting a new topic.
	 *
	 * @param   string  $message  Message
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function onAfterPost($message)
	{
	}

	/**
	 * Triggered before replying to a topic.
	 *
	 * @param   string  $message  Message
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function onBeforeReply($message)
	{
	}

	/**
	 * Triggered after replying to a topic.
	 *
	 * @param   string  $message  Message
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function onAfterReply($message)
	{
	}

	/**
	 * Triggered before posting a new topic.
	 *
	 * @param   string  $message  Message
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function onBeforeHold($message)
	{
	}

	/**
	 * Triggered before editing a post.
	 *
	 * @param   string  $message  Message
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function onBeforeEdit($message)
	{
	}

	/**
	 * Triggered after editing a post.
	 *
	 * @param   string  $message  Message
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function onAfterEdit($message)
	{
	}

	/**
	 * @param   string  $message  Message
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function onAfterDelete($message)
	{
	}

	/**
	 * @param   string  $message  Message
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function onAfterUndelete($message)
	{
	}

	/**
	 * @param   string  $message  Message
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function onAfterDeleteTopic($message)
	{
	}

	/**
	 * Triggered after (un)subscribing a topic.
	 *
	 * @param   int  $topicid  Topic Id.
	 * @param   int  $action   1 = subscribe, 0 = unsubscribe.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function onAfterSubscribe($topicid, $action)
	{
	}

	/**
	 * Triggered after (un)favoriting a topic.
	 *
	 * @param   int  $topicid  Topic Id.
	 * @param   int  $action   1 = favorite, 0 = unfavorite.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function onAfterFavorite($topicid, $action)
	{
	}

	/**
	 * Triggered after (un)stickying a topic.
	 *
	 * @param   int  $topicid  Topic Id.
	 * @param   int  $action   1 = sticky, 0 = unsticky.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function onAfterSticky($topicid, $action)
	{
	}

	/**
	 * Triggered after (un)locking a topic.
	 *
	 * @param   int  $topicid  Topic Id.
	 * @param   int  $action   1 = lock, 0 = unlock.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function onAfterLock($topicid, $action)
	{
	}

	/**
	 * Triggered after giving thankyou to a message.
	 *
	 * @param   int  $actor    Actor user Id (usually current user).
	 * @param   int  $target   Target user Id.
	 * @param   int  $message  Message Id.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function onAfterThankyou($actor, $target, $message)
	{
	}

	/**
	 * Triggered after removing thankyou from a message.
	 *
	 * @param   int  $actor    Actor user Id (usually current user).
	 * @param   int  $target   Target user Id.
	 * @param   int  $message  Message Id.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function onAfterUnThankyou($actor, $target, $message)
	{
	}

	/**
	 * Triggered after changing user karma.
	 *
	 * @param   int  $target  Target user Id.
	 * @param   int  $actor   Actor user Id (usually current user).
	 * @param   int  $delta   Points added / removed.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function onAfterKarma($target, $actor, $delta)
	{
	}

	/**
	 * Get list of medals.
	 *
	 * @param   int  $userid  Users id
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function getUserMedals($userid)
	{
	}

	/**
	 * Get user points.
	 *
	 * @param   int  $userid  Users id
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function getUserPoints($userid)
	{
	}
}
