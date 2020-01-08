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

namespace Joomla\Component\Kunena\Site\Controller\Topic\Item\Rating;

defined('_JEXEC') or die();

use Exception;
use Joomla\Component\Kunena\Libraries\Controller\Display;
use function defined;

/**
 * Class ComponentKunenaControllerTopicItemRatingDisplay
 *
 * @since   Kunena 5.0
 */
class ComponentKunenaControllerTopicItemRatingDisplay extends Display
{
	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $name = 'Topic/Item/Rating';

	/**
	 * @var     KunenaForumTopic
	 * @since   Kunena 6.0
	 */
	public $topic;

	/**
	 * Prepare topic actions display.
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
	}
}
