<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Helloworld Component Helper file for generating the URL Routes
 *
 */
class HelloworldHelperRoute
{
	/**
	 * When the Helloworld message is displayed then there is also shown a map with a Search Here button.
	 * This function generates the URL which the Ajax call will use to perform the search. 
	 * 
	 */
	public static function getAjaxURL()
	{
		$app  = JFactory::getApplication();
		$sitemenu= $app->getMenu();
		$thismenuitem = $sitemenu->getActive();

		// if we haven't got an active menuitem, or we're currently on the messages menuitem then just stay there
		if (!$thismenuitem || $thismenuitem->alias == "messages")
		{
			return null;
		}

		$mainmenuitems = $sitemenu->getItems('menutype', 'mainmenu');
		foreach ($mainmenuitems as $menuitem)
		{
			if ($menuitem->alias == "messages")
			{
				$itemid = $menuitem->id; 
				$url = JRoute::_("index.php?Itemid=$itemid&view=helloworld&format=json");
				return $url;
			}
		}
		return null; 
	}
}