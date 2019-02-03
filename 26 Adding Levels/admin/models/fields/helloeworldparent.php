<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 *
 * Class associated with displaying an input field to capture the parent of a helloworld record
 */

defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

class JFormFieldHelloworldParent extends JFormFieldList
{
	protected $type = 'HelloworldParent';

	/**
	 * Method to return the field options for the parent
	 *
	 */
	protected function getOptions()
	{
		$options = array();

		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('DISTINCT(a.id) AS value, a.greeting AS text, a.level, a.lft')
			->from('#__helloworld AS a');
		
		// Prevent parenting to children of this record, or to itself
		// If this record has lft = x and rgt = y, then its children have lft > x and rgt < y
		if ($id = $this->form->getValue('id'))
		{
			$query->join('LEFT', $db->quoteName('#__helloworld') . ' AS h ON h.id = ' . (int) $id)
				->where('NOT(a.lft >= h.lft AND a.rgt <= h.rgt)');
		}
		
		$query->order('a.lft ASC');
		
		$db->setQuery($query);

		try
		{
			$options = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}
		
		// Pad the option text with spaces using depth level as a multiplier.
		for ($i = 0; $i < count($options); $i++)
		{
			$options[$i]->text = str_repeat('- ', $options[$i]->level) . $options[$i]->text;
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;

	}
}