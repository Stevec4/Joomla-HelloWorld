<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HTML View class for the HelloWorld Component
 *
 * @since  0.0.1
 */
class HelloWorldViewHelloWorld extends JViewLegacy
{
	/**
	 * Display the Hello World view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	function display($tpl = null)
	{
		// Assign data to the view
		$this->item = $this->get('Item');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');

			return false;
		}

		$this->addMap();

		// Display the view
		parent::display($tpl);
	}

	function addMap() 
	{
		$document = JFactory::getDocument();

		// everything's dependent upon JQuery
		JHtml::_('jquery.framework');

		// we need the Openlayers JS and CSS libraries
		$document->addScript("https://openlayers.org/en/v4.6.4/build/ol.js");
		$document->addStyleSheet("https://openlayers.org/en/v4.6.4/css/ol.css");

		// ... and our own JS and CSS
		$document->addScript(JURI::root() . "media/com_helloworld/js/openstreetmap.js");
		$document->addStyleSheet(JURI::root() . "media/com_helloworld/css/openstreetmap.css");

		// get the data to pass to our JS code
		$params = $this->get("mapParams");
		$document->addScriptOptions('params', $params);
	}
}