<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      User
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\User;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Date\Date;
use Kunena\Forum\Libraries\Config\Config;
use function defined;

/**
 * Class \Kunena\Forum\Libraries\User\KunenaUserFinder
 *
 * @since   Kunena 6.0
 */
class Finder extends \Kunena\Forum\Libraries\Database\Object\Finder
{
	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $table = '#__users';

	/**
	 * @var     Config|mixed
	 * @since   Kunena 6.0
	 */
	protected $config;

	/**
	 * Constructor.
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function __construct()
	{
		parent::__construct();

		$this->config = Config::getInstance();
		$this->limit  = $this->config->userlist_rows;

		$this->query->leftJoin($this->db->quoteName('#__kunena_users', 'ku') . ' ON ' . $this->db->quoteName('ku.userid') . ' = ' . $this->db->quoteName('a.id'));
	}

	/**
	 * Filter by time, either on registration or last visit date.
	 *
	 * @param   Date  $starting  Starting date or null if older than ending date.
	 * @param   Date  $ending    Ending date or null if newer than starting date.
	 * @param   bool  $register  True = registration date, False = last visit date.
	 *
	 * @return  $this
	 *
	 * @since   Kunena 6.0
	 */
	public function filterByTime(Date $starting = null, Date $ending = null, $register = true)
	{
		$name = $register ? 'registerDate' : 'lastvisitDate';

		if ($starting && $ending)
		{
			$this->query->where($this->db->quoteName('a.' . $name) . ' BETWEEN ' . $this->db->quote($starting->toUnix()) . ' AND ' . $this->db->quote($ending->toUnix()));
		}
		elseif ($starting)
		{
			$this->query->where($this->db->quoteName('a.' . $name) . ' > ' . $this->db->quote($starting->toUnix()));
		}
		elseif ($ending)
		{
			$this->query->where($this->db->quoteName('a.' . $name) . ' <= ' . $this->db->quote($ending->toUnix()));
		}

		return $this;
	}

	/**
	 * @param   array  $ignore  ignore
	 *
	 * @return  $this
	 *
	 * @since   Kunena 6.0
	 */
	public function filterByConfiguration(array $ignore = [])
	{
		if ($this->config->userlist_count_users == '1')
		{
			$this->query->where('(a.block=0 OR a.activation="")');
		}
		elseif ($this->config->userlist_count_users == '2')
		{
			$this->query
				->where($this->db->quoteName('a.block') . ' = 0')
				->where($this->db->quoteName('a.activation') . '=""');
		}
		elseif ($this->config->userlist_count_users == '3')
		{
			$this->query->where($this->db->quoteName('a.block') . ' = 0');
		}

		// Hide super admins from the list
		if ($this->config->superadmin_userlist && $ignore)
		{
			$this->query->where($this->db->quoteName('a.id') . ' NOT IN (' . implode(',', $ignore) . ')');
		}

		return $this;
	}

	/**
	 * @param   string  $search  search
	 *
	 * @return  $this
	 *
	 * @since   Kunena 6.0
	 */
	public function filterByName($search)
	{
		if ($search)
		{
			if ($this->config->username)
			{
				$this->query->where($this->db->quoteName('a.username') . ' LIKE ' . $this->db->quote($search));
			}
			else
			{
				$this->query->where($this->db->quoteName('a.name') . ' LIKE ' . $this->db->quote($search));
			}
		}

		return $this;
	}

	/**
	 * Get users.
	 *
	 * @return  array|KunenaUser[]
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function find()
	{
		$results = parent::find();

		return Helper::loadUsers($results);
	}
}
