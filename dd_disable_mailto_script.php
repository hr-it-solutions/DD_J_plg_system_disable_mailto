<?php
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
