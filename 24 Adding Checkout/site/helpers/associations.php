<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 *
 * The Helloworld helper file for Multilingual Associations - For the SITE
 */

// No direct access to this file
defined('_JEXEC') or die;

JLoader::register('CategoryHelperAssociation', JPATH_ADMINISTRATOR . '/components/com_categories/helpers/association.php');

/**
 * Helloworld Component Association Helper
 *
 */
abstract class HelloworldHelperAssociation extends CategoryHelperAssociation
{
	/**
	 * Method to get the associations for a given item
	 *
	 * @param   integer  $id    Id of the item (helloworld id or catid, depending on view)
	 * @param   string   $view  Name of the view ('helloworld' or 'category')
	 *
	 * @return  array   Array of associations for the item
	 */
	public static function getAssociations($id = 0, $view = null)
	{
		$input = JFactory::getApplication()->input;
		$view = $view === null ? $input->get('view') : $view;
		$id = empty($id) ? $input->getInt('id') : $id;

		if ($view === 'helloworld')
		{
			if ($id)
			{
				$associations = JLanguageAssociations::getAssociations('com_helloworld', '#__helloworld', 'com_helloworld.item', $id);

				$return = array();

				foreach ($associations as $tag => $item)
				{
					$link = 'index.php?option=com_helloworld&view=helloworld&id=' . $item->id . '&catid=' . $item->catid;
					if ($item->language && $item->language !== '*' && JLanguageMultilang::isEnabled())
					{
						$link .= '&lang=' . $item->language;
					}
					$return[$tag] = $link;
				}

				return $return;
			}
		}

		if ($view === 'category' || $view === 'categories')
		{
			return self::getCategoryAssociations($id, 'com_helloworld');
		}

		return array();
	}
}