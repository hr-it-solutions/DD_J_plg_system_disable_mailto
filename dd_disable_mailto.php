<?php
/**
 * @version    1-2-1-0 // Y-m-d 2016-10-04
 * @author     HR IT-Solutions Florian Häusler https://www.hr-it-solutions.com
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
	 * Construct Events
	 * @since Version 3.6.2
	 */
	function __construct( $subject )
	{

		parent::__construct($subject);

		// Load plugin parameters
		$this->plugin = JPluginHelper::getPlugin(self::TYPE, self::NAME);
		$this->params = new JRegistry($this->plugin->params);

		$app = JFactory::getApplication();

		if ($app->isAdmin()){ // Trigger Events only in Backend

			$option = $app->input->get->get("option",0,"STR");

			if($option === "com_plugins" ){ // And only on plugin page, to save performance

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
	 * onAfterRoute Event
	 * - redirectOldMailtoLinks
	 * @since Version 3.6.2
	 */
	public function onAfterRoute(){

		// Redirect old mailTo otion
		if ($this->params->get('redirect_mailto',0))
		{
			$this->redirectOldMailtoLinks(); // Redirect
		}

	}

	/**
	 * onAfterRender Event
	 * - remveMailtoLink
	 * @since Version 3.6.2
	 */
	public function onAfterRender()
	{
		$app = JFactory::getApplication();
		// Front end
		if ($app instanceof JApplicationSite)
		{

			if ($this->params->get('remove_mailto_link',0)) // Remove mailTo link Option
			{
				$html = $app->getBody();
				$html = $this->remveMailtoLink($html);
				$app->setBody($html);
			}

		}
	}


	/**
	 * Remove bootstrap mailto list link (like protostar, etc...) method
	 * @return string without list elemenent email-icon
	 * @since Version 3.6.2
	 */
	private function remveMailtoLink($content)
	{
		return preg_replace('#<li class="email-icon">(.*?)</li>#', ' ', $content);
	}

	/**
	 * Redirect old mailto links to home by HTTP STATUS 301 method
	 * @return string without list elemenent email-icon
	 * @since Version 3.6.2
	 */
	private function redirectOldMailtoLinks()
	{
		if(strpos(JUri::current(), 'component/mailto') !== false){
			$alternativeURL = $this->params->get('redirect_mailto_url');
			JFactory::getApplication()->redirect(JURI::base() . $alternativeURL,301);
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