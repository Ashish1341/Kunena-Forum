<?php
/**
 * Kunena Latest Json
 *
 * @package       Kunena.json_kunenalatest
 *
 * @copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena;

defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Factory;

use stdClass;
use function defined;

/**
 *
 * @since   Kunena 6.0
 */
class KunenaViewTopic extends KunenaView
{
	/**
	 * @param   null  $tpl tmpl
	 *
	 * @return  mixed|void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function display($tpl = null)
	{
		$id                        = Factory::getApplication()->input->getInt('id');
		$topic                     = KunenaForumTopicHelper::get($id);
		$topic->subject            = KunenaHtmlParser::parseText($topic->subject);
		$topic->first_post_message = KunenaHtmlParser::stripBBCode($topic->first_post_message);
		$topic->last_post_message  = KunenaHtmlParser::stripBBCode($topic->last_post_message);
		$messages                  = KunenaForumMessageHelper::getMessagesByTopic($topic, 0, $topic->posts);

		$list     = [];
		$template = KunenaFactory::getTemplate();

		foreach ($messages as $message)
		{
			$user              = KunenaUserHelper::get($message->userid);
			$response          = new stdClass;
			$response->id      = $message->id;
			$response->message = KunenaHtmlParser::stripBBCode(KunenaForumMessageHelper::get($message->id)->message);
			$response->author  = $user->username;
			$response->avatar  = $user->getAvatarImage($template->params->get('avatarType'), 'thumb');
			$response->rank    = $user->getRank($topic->getCategory()->id, 'title');
			$response->time    = KunenaDate::getInstance($message->time)->toKunena('config_post_dateformat');

			$list[] = $response;
		}

		$json2 = [
			'Count'    => $topic,
			'Messages' => $list,
		];

		$json = json_encode($json2, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

		echo $json;
	}
}
