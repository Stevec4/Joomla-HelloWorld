<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 *
 * Class for displaying the Ordering field in the helloworld edit layout
 */

defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

class JFormFieldHelloworldOrdering extends JFormFieldList
{
	protected $type = 'HelloworldOrdering';

	/**
	 * Method to return the options for ordering the helloworld record
	 * This is the list of siblings the record's siblings - ie those records with the same parent.
	 * The method requires that parent id be set.
	 */
	protected function getOptions()
	{
		$options = array();

		// Get the parent
		$parent_id = $this->form->getValue('parent_id', 0);

		if (empty($parent_id))
		{
			return false;
		}

		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('a.id AS value, a.greeting AS text')
			->from('#__helloworld AS a')
			->where('a.parent_id =' . (int) $parent_id);

		$query->order('a.lft ASC');

		// Get the options.
		$db->setQuery($query);

		try
		{
			$options = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}

		$options = array_merge(
			array(array('value' => '-1', 'text' => JText::_('COM_HELLOWORLD_ITEM_FIELD_ORDERING_VALUE_FIRST'))),
			$options,
			array(array('value' => '-2', 'text' => JText::_('COM_HELLOWORLD_ITEM_FIELD_ORDERING_VALUE_LAST')))
		);

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * This method returns the input element except if a new record is being created, in which case a text string is output
	 */
	protected function getInput()
	{
		if ($this->form->getValue('id', 0) == 0)
		{
			return '<span class="readonly">' . JText::_('COM_HELLOWORLD_ITEM_FIELD_ORDERING_TEXT') . '</span>';
		}
		else
		{
			return parent::getInput();
		}
	}
}