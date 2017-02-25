<?php
/**
 * @version    1-1-0-0 // Y-m-d 2016-10-01
 * @author     HR IT-Solutions Florian Häusler https://www.hr-it-solutions.com
 * @copyright  Copyright (C) 2011 - 2016 Didldu e.K. | HR IT-Solutions
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 **/

defined('_JEXEC') or die();

class plgSystemDD_Disable_MailToInstallerScript
{
	/**
	 * Reset Joomla default settings
	 * @since Version 3.6.2
	 */
	public function uninstall()
	{
		$db  = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->update('#__extensions')
			->set($db->quoteName('enabled') . ' = 1')
			->where($db->quoteName('element') . ' = ' . $db->quote('com_mailto'))
			->where($db->quoteName('type') . ' = ' . $db->quote('component'));
		$db->setQuery($query);
		$db->execute();
	}
}
