<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Forum.Topic.User
 *
 * @copyright     Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Forum\Topic\User;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\Exception\ExecutionFailureException;
use Kunena\Forum\Libraries\Error\KunenaError;
use Kunena\Forum\Libraries\Forum\Message\Message;
use Kunena\Forum\Libraries\Forum\Topic\Topic;
use Kunena\Forum\Libraries\Table\KunenaTableObject;
use Kunena\Forum\Libraries\Tables\KunenaUserTopics;
use function defined;

/**
 * Class \Kunena\Forum\Libraries\Forum\Topic\TopicUser
 *
 * @since   Kunena 6.0
 * @property int    $user_id
 * @property int    $topic_id
 * @property int    $category_id
 * @property int    $posts
 * @property int    $last_post_id
 * @property int    $owner
 * @property int    $favorite
 * @property int    $subscribed
 * @property string $params
 *
 */
class User extends CMSObject
{
	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	protected $_exists = false;

	/**
	 * @var     DatabaseDriver|null
	 * @since   Kunena 6.0
	 */
	protected $_db = null;

	/**
	 * @internal
	 *
	 * @param   mixed  $topic  topic
	 * @param   mixed  $user   user
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function __construct($topic = null, $user = null)
	{
		$topic = \Kunena\Forum\Libraries\Forum\Topic\Helper::get($topic);

		// Always fill empty data
		$this->_db = Factory::getDBO();

		// Create the table object
		$table = $this->getTable();

		// Lets bind the data
		$this->setProperties($table->getProperties());
		$this->_exists     = false;
		$this->topic_id    = $topic->id;
		$this->category_id = $topic->category_id;
		$this->user_id     = \Kunena\Forum\Libraries\User\Helper::get($user)->userid;
	}

	/**
	 * Method to get the topics table object.
	 *
	 * @param   string  $type    Topics table name to be used.
	 * @param   string  $prefix  Topics table prefix to be used.
	 *
	 * @return  boolean|Table|KunenaTableObject|KunenaUserTopics
	 *
	 * @since   Kunena 6.0
	 */
	public function getTable($type = 'KunenaUserTopics', $prefix = 'Table')
	{
		static $tabletype = null;

		// Set a custom table type is defined
		if ($tabletype === null || $type != $tabletype ['name'] || $prefix != $tabletype ['prefix'])
		{
			$tabletype ['name']   = $type;
			$tabletype ['prefix'] = $prefix;
		}

		// Create the user table object
		return Table::getInstance($tabletype ['name'], $tabletype ['prefix']);
	}

	/**
	 * @param   mixed  $id      id
	 * @param   mixed  $user    user
	 * @param   bool   $reload  reload
	 *
	 * @return  User
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public static function getInstance($id = null, $user = null, $reload = false)
	{
		return Helper::get($id, $user, $reload);
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function reset()
	{
		$this->topic_id = 0;
		$this->load();
	}

	/**
	 * Method to load a \Kunena\Forum\Libraries\Forum\Topic\TopicUser object by id.
	 *
	 * @param   int    $topic_id  Topic id to be loaded.
	 * @param   mixed  $user      user
	 *
	 * @return  boolean  True on success
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function load($topic_id = null, $user = null)
	{
		if ($topic_id === null)
		{
			$topic_id = $this->topic_id;
		}

		if ($user === null && $this->user_id !== null)
		{
			$user = $this->user_id;
		}

		$user = \Kunena\Forum\Libraries\User\Helper::get($user);

		// Create the table object
		$table = $this->getTable();

		// Load the KunenaTable object based on id
		if ($topic_id)
		{
			$this->_exists = $table->load(['user_id' => $user->userid, 'topic_id' => $topic_id]);
		}
		else
		{
			$this->_exists = false;
		}

		// Assuming all is well at this point lets bind the data
		$this->setProperties($table->getProperties());

		return $this->_exists;
	}

	/**
	 * Method to delete the \Kunena\Forum\Libraries\Forum\Topic\TopicUser object from the database.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function delete()
	{
		if (!$this->exists())
		{
			return true;
		}

		// Create the table object
		$table = $this->getTable();

		$result = $table->delete(['topic_id' => $this->topic_id, 'user_id' => $this->user_id]);

		if (!$result)
		{
			$this->setError($table->getError());
		}

		$this->_exists = false;

		return $result;
	}

	/**
	 * @param   null|bool  $exists  exists
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 */
	public function exists($exists = null)
	{
		$return = $this->_exists;

		if ($exists !== null)
		{
			$this->_exists = $exists;
		}

		return $return;
	}

	/**
	 * @param   Message  $message    message
	 * @param   int      $postDelta  postdelta
	 *
	 * @return  boolean|void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function update(Message $message = null, $postDelta = 0)
	{
		$this->posts       += $postDelta;
		$this->category_id = $this->getTopic()->category_id;

		if ($message && !$message->hold && $message->thread == $this->topic_id)
		{
			if ($message->parent == 0)
			{
				$this->owner = 1;
			}

			if ($this->last_post_id < $message->id)
			{
				$this->last_post_id = $message->id;
			}
		}
		elseif (!$message || (($message->hold || $message->thread != $this->topic_id) && $this->last_post_id == $message->id))
		{
			$query = $this->_db->getQuery(true);
			$query->select('COUNT(*) AS posts, MAX(id) AS last_post_id, MAX(IF(parent=0,1,0)) AS owner')
				->from($this->_db->quoteName('#__kunena_messages'))
				->where($this->_db->quoteName('userid') . ' = ' . $this->_db->quote($this->user_id))
				->andWhere($this->_db->quoteName('thread') . ' = ' . $this->_db->quote($this->topic_id))
				->andWhere($this->_db->quoteName('moved') . ' = 0')
				->andWhere($this->_db->quoteName('hold') . ' = 0')
				->group('userid, thread');
			$query->setLimit(1);
			$this->_db->setQuery($query);

			try
			{
				$info = $this->_db->loadAssocList();
			}
			catch (ExecutionFailureException $e)
			{
				KunenaError::displayDatabaseError($e);

				return;
			}

			if ($info)
			{
				$this->bind($info);
			}
		}

		return $this->save();
	}

	/**
	 * @return  Topic
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function getTopic()
	{
		return \Kunena\Forum\Libraries\Forum\Topic\Helper::get($this->topic_id);
	}

	/**
	 * @param   array  $data    data
	 * @param   array  $ignore  ignore
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function bind(array $data, array $ignore = [])
	{
		$data = array_diff_key($data, array_flip($ignore));
		$this->setProperties($data);
	}

	/**
	 * Method to save the \Kunena\Forum\Libraries\Forum\Topic\TopicUser object to the database.
	 *
	 * @param   bool  $updateOnly  Save the object only if not a new topic.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function save($updateOnly = false)
	{
		// Create the topics table object
		$table = $this->getTable();
		$table->bind($this->getProperties());
		$table->exists($this->_exists);

		// Check and store the object.
		if (!$table->check())
		{
			$this->setError($table->getError());

			return false;
		}

		// Are we creating a new topic
		$isnew = !$this->_exists;

		// If we aren't allowed to create new topic return
		if ($isnew && $updateOnly)
		{
			return true;
		}

		// Store the topic data in the database
		if (!$result = $table->store())
		{
			$this->setError($table->getError());
		}

		// Fill up \Kunena\Forum\Libraries\Forum\Topic\TopicUser object in case we created a new topic.
		if ($result && $isnew)
		{
			$this->load();
		}

		return $result;
	}
}
