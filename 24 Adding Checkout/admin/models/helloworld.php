<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\Registry\Registry;

/**
 * HelloWorld Model
 *
 * @since  0.0.1
 */
class HelloWorldModelHelloWorld extends JModelAdmin
{
	// JModelAdmin needs to know this for storing the associations 
	protected $associationsContext = 'com_helloworld.item';

	/**
	 * Method to override getItem to allow us to convert the JSON-encoded image information
	 * in the database record into an array for subsequent prefilling of the edit form
	 * We also use this method to prefill the associations
	 */
	public function getItem($pk = null)
	{
		$item = parent::getItem($pk);
		if ($item AND property_exists($item, 'image'))
		{
			$registry = new Registry($item->image);
			$item->imageinfo = $registry->toArray();
		}

		// Load associated items
		if (JLanguageAssociations::isEnabled())
		{
			$item->associations = array();

			if ($item->id != null)
			{
				$associations = JLanguageAssociations::getAssociations('com_helloworld', '#__helloworld', 'com_helloworld.item', (int)$item->id);

				foreach ($associations as $tag => $association)
				{
					$item->associations[$tag] = $association->id;
				}
			}
		}
		return $item; 
	}
	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $type    The table name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable  A JTable object
	 *
	 * @since   1.6
	 */
	public function getTable($type = 'HelloWorld', $prefix = 'HelloWorldTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed    A JForm object on success, false on failure
	 *
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm(
			'com_helloworld.helloworld',
			'helloworld',
			array(
				'control' => 'jform',
				'load_data' => $loadData
			)
		);

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to preprocess the form to add the association fields dynamically
	 *
	 * @return     none
	 */
	protected function preprocessForm(JForm $form, $data, $group = 'helloworld')
	{
		// Association content items
		if (JLanguageAssociations::isEnabled())
		{
			$languages = JLanguageHelper::getContentLanguages(false, true, null, 'ordering', 'asc');

			if (count($languages) > 1)
			{
				$addform = new SimpleXMLElement('<form />');
				$fields = $addform->addChild('fields');
				$fields->addAttribute('name', 'associations');
				$fieldset = $fields->addChild('fieldset');
				$fieldset->addAttribute('name', 'item_associations');

				foreach ($languages as $language)
				{
					$field = $fieldset->addChild('field');
					$field->addAttribute('name', $language->lang_code);
					$field->addAttribute('type', 'modal_helloworld');
					$field->addAttribute('language', $language->lang_code);
					$field->addAttribute('label', $language->title);
					$field->addAttribute('translate_label', 'false');
				}

				$form->load($addform, false);
			}
		}
		parent::preprocessForm($form, $data, $group);
	}

	/**
	 * Method to get the script that have to be included on the form
	 *
	 * @return string	Script files
	 */
	public function getScript() 
	{
		return 'administrator/components/com_helloworld/models/forms/helloworld.js';
	}
	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState(
			'com_helloworld.edit.helloworld.data',
			array()
		);

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}
	/**
	 * Method to override the JModelAdmin save() function to handle Save as Copy correctly
	 *
	 * @param   The helloworld record data submitted from the form.
	 *
	 * @return  parent::save() return value
	 */
	public function save($data)
	{
		$input = JFactory::getApplication()->input;

		JLoader::register('CategoriesHelper', JPATH_ADMINISTRATOR . '/components/com_categories/helpers/categories.php');

		// Validate the category id
		// validateCategoryId() returns 0 if the catid can't be found
		if ((int) $data['catid'] > 0)
		{
			$data['catid'] = CategoriesHelper::validateCategoryId($data['catid'], 'com_helloworld');
		}

		// Alter the greeting and alias for save as copy
		if ($input->get('task') == 'save2copy')
		{
			$origTable = clone $this->getTable();
			$origTable->load($input->getInt('id'));

			if ($data['greeting'] == $origTable->greeting)
			{
				list($greeting, $alias) = $this->generateNewTitle($data['catid'], $data['alias'], $data['greeting']);
				$data['greeting'] = $greeting;
				$data['alias'] = $alias;
			}
			else
			{
				if ($data['alias'] == $origTable->alias)
				{
					$data['alias'] = '';
				}
			}
			// standard Joomla practice is to set the new record as unpublished
			$data['published'] = 0;
		}

		return parent::save($data);
	}

	/**
	 * Method to check if it's OK to delete a message. Overrides JModelAdmin::canDelete
	 */
	protected function canDelete($record)
	{
		if( !empty( $record->id ) )
		{
			return JFactory::getUser()->authorise( "core.delete", "com_helloworld.helloworld." . $record->id );
		}
	}
}