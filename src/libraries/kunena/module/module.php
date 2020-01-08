<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Module
 *
 * @copyright     Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Module;

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;
use stdClass;
use function defined;

/**
 * Class KunenaModule
 *
 * @since   Kunena 6.0
 */
abstract class Module
{
	/**
	 * CSS file to be loaded.
	 *
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected static $css = null;

	/**
	 * @var     stdClass
	 * @since   Kunena 6.0
	 */
	protected $module = null;

	/**
	 * @var     Registry
	 * @since   Kunena 6.0
	 */
	protected $params = null;

	/**
	 * @param   stdClass  $module  module
	 * @param   Registry  $params  params
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  \Exception
	 */
	public function __construct($module, $params)
	{
		$this->module   = $module;
		$this->params   = $params;
		$this->document = Factory::getApplication()->getDocument();
	}

	/**
	 * Display module contents.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  \Exception
	 */
	final public function display()
	{
		// Load CSS only once
		if (static::$css)
		{
			$this->document->addStyleSheet(Uri::root(true) . static::$css);
			static::$css = null;
		}

		// Use caching also for registered users if enabled.
		if ($this->params->get('owncache', 0))
		{
			$cache = Factory::getCache('com_kunena', 'output');

			$me = Factory::getApplication()->getIdentity();
			$cache->setLifeTime($this->params->get('cache_time', 180));
			$hash = md5(serialize($this->params));

			// Disable Cache for now: FIXME
			/*
			if ($cache->start("display.{$me->userid}.{$hash}", 'mod_kunenalatest'))
			{
				return;
			}*/
		}

		// Initialize Kunena.
		\Kunena\Forum\Libraries\Forum\KunenaForum::setup();

		// Display module.
		$this->_display();

		// Disable Cache for now: FIXME
		/*
		// Store cached page.
		if (isset($cache))
		{
			$cache->end();
		}*/
	}

	/**
	 * Internal module function to display module contents.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	abstract protected function _display();
}
