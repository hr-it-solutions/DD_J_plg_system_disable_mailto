<?php
/**
 * @package    DD_Disable_MailTo
 *
 * @author     HR IT-Solutions Florian Häusler <info@hr-it-solutions.com>
 * @copyright  Copyright (C) 2011 - 2017 Didldu e.K. | HR IT-Solutions
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
	protected $app;

	// Plugin info constants
	const TYPE = 'system';
	const NAME = 'dd_disable_mailto';

	/**
	 * Construct Events
	 *
	 * @since Version 1.2.2.1
	 */
	function __construct( $subject )
	{
		parent::__construct($subject);

		// Load plugin parameters
		$this->plugin = JPluginHelper::getPlugin(self::TYPE, self::NAME);
		$this->params = new JRegistry($this->plugin->params);

		if ($this->app->isAdmin()) // Trigger Events only in Backend
		{
			$option = $this->app->input->get->get("option", 0, "STR");

			if ($option === "com_plugins" ) // And only on plugin page, to save performance
			{
				$disable_mailTo = $this->params->get('disable_mailto', 0);

				if (!$disable_mailTo)
				{
					$this->set_mailTo(false);
				}
				else
				{
					$this->set_mailTo();
				}
			}
		}
	}


	/**
	 * onAfterRoute Event
	 * - redirectOldMailtoLinks
	 *
	 * @since Version 1.2.2.1
	 */
	public function onAfterRoute(){

		// Redirect old mailTo otion
		if ($this->params->get('redirect_mailto', 0))
		{
			$this->redirectOldMailtoLinks();
		}

	}

	/**
	 * onAfterRender Event
	 * remveMailtoLink
	 *
	 * @since Version 1.2.2.1
	 */
	public function onAfterRender()
	{
		// Front end
		if ($this->app instanceof JApplicationSite)
		{
			if ($this->params->get('remove_mailto_link', 0)) // Remove mailTo link Option
			{
				$html = $this->app->getBody();
				$html = $this->remveMailtoLink($html);
				$this->app->setBody($html);
			}
		}
	}


	/**
	 * Remove bootstrap mailto list link (like protostar, etc...) method
	 *
	 * @param   string  $content  the content to replace
	 *
	 * @return  string  without list elemenent email-icon
	 *
	 * @since Version 1.2.2.1
	 */
	private function remveMailtoLink($content)
	{
		return preg_replace('/<li class=\"email-icon\">(.*?)<\/li>/s', ' ', $content);
	}

	/**
	 * Redirect old mailto links to home by HTTP STATUS 301 method
	 *
	 * @since Version 1.2.0.0
	 */
	private function redirectOldMailtoLinks()
	{
		if (strpos(JUri::current(), 'component/mailto') !== false)
		{
			$alternativeURL = $this->params->get('redirect_mailto_url');

			$this->app->redirect(JURI::base() . $alternativeURL, 301);
		}

		return true;
	}

	/**
	 * Enable / Disable com_mailto extension method
	 *
	 * @param   boolean  $enable  true = enabled
	 *
	 * @since  Version 1.0.0.0
	 */
	private function set_mailTo($enable = true)
	{
		$enable = intval($enable);
		$db  = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->update('#__extensions')
			->set($db->qn('enabled') . ' = ' . $enable)
			->where($db->qn('element') . ' = ' . $db->q('com_mailto'))
			->where($db->qn('type') . ' = ' . $db->q('component'));
		$db->setQuery($query);
		$db->execute();
	}
}
