<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 *
 * Helper file for outputting html associated with the helloworld administrator functionality
 */

// No direct access to this file
defined('_JEXEC') or die;

JLoader::register('HelloworldHelper', JPATH_ADMINISTRATOR . '/components/com_helloworld/helpers/helloworld.php');

class JHtmlHelloworlds
{
	/**
	 * Render the list of associated items
	 *
	 * @param   integer  $id  The id of the helloworld record
	 *
	 * @return  string  The language HTML
	 *
	 * @throws  Exception
	 */
	public static function association($id)
	{
		// Defaults
		$html = '';

		// Get the associations
		if ($associations = JLanguageAssociations::getAssociations('com_helloworld', '#__helloworld', 'com_helloworld.item', (int)$id))
		{
			foreach ($associations as $tag => $associated)
			{
				$associations[$tag] = (int) $associated->id;
			}

			// get the relevant category titles and languages, for the tooltip
			$db = JFactory::getDbo();
			$query = $db->getQuery(true)
				->select('h.*')
				->select('l.sef as lang_sef')
				->select('l.lang_code')
				->from('#__helloworld as h')
				->select('cat.title as category_title')
				->join('LEFT', '#__categories as cat ON cat.id=h.catid')
				->where('h.id IN (' . implode(',', array_values($associations)) . ')')
				->join('LEFT', '#__languages as l ON h.language=l.lang_code')
				->select('l.image')
				->select('l.title as language_title');
			$db->setQuery($query);

			try
			{
				$items = $db->loadObjectList('id');
			}
			catch (RuntimeException $e)
			{
				throw new Exception($e->getMessage(), 500, $e);
			}

			if ($items)
			{
				foreach ($items as &$item)
				{
					$text    = $item->lang_sef ? strtoupper($item->lang_sef) : 'XX';
					$url     = JRoute::_('index.php?option=com_helloworld&task=helloworld.edit&id=' . (int) $item->id);

					$tooltip = htmlspecialchars($item->greeting, ENT_QUOTES, 'UTF-8') . '<br />' . JText::sprintf('JCATEGORY_SPRINTF', $item->category_title);
					$classes = 'hasPopover label label-association label-' . $item->lang_sef;

					$item->link = '<a href="' . $url . '" title="' . $item->language_title . '" class="' . $classes
						. '" data-content="' . $tooltip . '" data-placement="top">'
						. $text . '</a>';
				}
			}

			JHtml::_('bootstrap.popover');

			$html = JLayoutHelper::render('joomla.content.associations', $items);
		}

		return $html;
	}
}