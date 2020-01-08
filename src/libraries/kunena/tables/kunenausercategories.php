<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Tables
 *
 * @copyright     Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Tables;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseDriver;
use Kunena\Forum\Libraries\Forum\Category\Helper;
use RuntimeException;
use function defined;

require_once __DIR__ . '/kunena.php';

/**
 * Kunena User Categories Table
 * Provides access to the #__kunena_user_categories table
 *
 * @since   Kunena 6.0
 */
class KunenaUserCategories extends KunenaTable
{
	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $user_id = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $category_id = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $role = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $allreadtime = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $subscribed = null;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	public $params = null;

	/**
	 * @param   DatabaseDriver  $db  Database driver
	 *
	 * @since   Kunena 6.0
	 */
	public function __construct($db)
	{
		parent::__construct('#__kunena_user_categories', ['user_id', 'category_id'], $db);
	}

	/**
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function check()
	{
		$user = \Kunena\Forum\Libraries\User\Helper::get($this->user_id);

		if (!$user->exists())
		{
			throw new RuntimeException(Text::sprintf('COM_KUNENA_LIB_TABLE_USERCATEGORIES_ERROR_USER_INVALID', (int) $user->userid));
		}

		$category = Helper::get($this->category_id);

		if ($this->category_id && !$category->exists())
		{
			throw new RuntimeException(Text::sprintf('COM_KUNENA_LIB_TABLE_USERCATEGORIES_ERROR_CATEGORY_INVALID', (int) $category->id));
		}

		return true;
	}
}
