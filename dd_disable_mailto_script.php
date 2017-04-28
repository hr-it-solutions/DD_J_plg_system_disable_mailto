<?php
/**
 * @package    DD_Disable_MailTo
 *
 * @author     HR IT-Solutions Florian Häusler <info@hr-it-solutions.com>
 * @copyright  Copyright (C) 2011 - 2017 Didldu e.K. | HR IT-Solutions
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 **/

defined('_JEXEC') or die();

class plgSystemDD_Disable_MailToInstallerScript
{
	/**
	 * Reset Joomla default settings
	 *
	 * @since Version 1.0.0.0
	 */
	public function uninstall()
	{
		$db  = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->update('#__extensions')
			->set($db->qn('enabled') . ' = 1')
			->where($db->qn('element') . ' = ' . $db->q('com_mailto'))
			->where($db->qn('type') . ' = ' . $db->q('component'));
		$db->setQuery($query);
		$db->execute();
	}
}
