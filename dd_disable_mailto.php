<?php
/**
 * @version    1-1-0-0 // Y-m-d 2016-10-01
 * @author     Didldu e.K. Florian Häusler https://www.hr-it-solutions.com
 * @copyright  Copyright (C) 2011 - 2016 Didldu e.K. | HR IT-Solutions
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 **/

defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');
jimport('joomla.access.access');

/**
 * Joomla! system plugin to disable mailto extension
 */
class plgSystemDD_Disable_MailTo extends JPlugin
{

	// Plugin info constants
	const TYPE = 'system';
	const NAME = 'dd_disable_mailto';

	/**
	 * onUserAfterSave Events
	 * @since Version 3.6.2
	 */
	function __construct( $subject )
	{

		parent::__construct($subject);

		if (JFactory::getApplication()->isAdmin()){ // Trigger Events only in Backend

			$input = JFactory::getApplication()->input;
			$option = $input->get->get("option",0,"STR");

			if($option === "com_plugins" ){ // And only on plugin page, to save performance

				// Load plugin parameters
				$this->plugin = JPluginHelper::getPlugin(self::TYPE, self::NAME);
				$this->params = new JRegistry($this->plugin->params);

				$disable_mailTo = $this->params->get('disable_mailto',0);

				if(!$disable_mailTo){
					$this->set_mailTo(false);
				} else {
					$this->set_mailTo();
				}

			}

		}

	}

	/**
	 * Enable / Disable com_mailto extension method
	 * @param $enable boolean true = enabled
	 * @since Version 3.6.2
	 */
	private function set_mailTo($enable = true)
	{
		$enable = intval($enable);
		$db  = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->update('#__extensions')
			->set($db->quoteName('enabled') . ' = ' . $enable)
			->where($db->quoteName('element') . ' = ' . $db->quote('com_mailto'))
			->where($db->quoteName('type') . ' = ' . $db->quote('component'));
		$db->setQuery($query);
		$db->execute();
	}

}