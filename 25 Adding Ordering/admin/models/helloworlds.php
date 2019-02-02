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

/**
 * HelloWorldList Model
 *
 * @since  0.0.1
 */
class HelloWorldModelHelloWorlds extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JController
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id',
				'greeting',
				'author',
				'created',
				'language',
				'ordering',
				'category_id',
				'association',
				'published'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   Elements order
	 * @param   string  $direction  Order direction
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();

		// Adjust the context to support modal layouts.
		if ($layout = $app->input->get('layout'))
		{
			$this->context .= '.' . $layout;
		}

		// Adjust the context to support forced languages.
		$forcedLanguage = $app->input->get('forcedLanguage', '', 'CMD');
		if ($forcedLanguage)
		{
			$this->context .= '.' . $forcedLanguage;
		}

		parent::populateState($ordering, $direction);
        
		// If there's a forced language then define that filter for the query where clause
		if (!empty($forcedLanguage))
		{
			$this->setState('filter.language', $forcedLanguage);
		}
	}


	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return      string  An SQL query
	 */
	protected function getListQuery()
	{
		// Initialize variables.
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Create the base select statement.
		$query->select('a.id as id, a.greeting as greeting, a.published as published, a.created as created, 
			  a.checked_out as checked_out, a.checked_out_time as checked_out_time, a.ordering as ordering, a.catid as catid,
			  a.image as imageInfo, a.latitude as latitude, a.longitude as longitude, a.alias as alias, a.language as language')
			  ->from($db->quoteName('#__helloworld', 'a'));

		// Join over the categories.
		$query->select($db->quoteName('c.title', 'category_title'))
			->join('LEFT', $db->quoteName('#__categories', 'c') . ' ON c.id = a.catid');

		// Join with users table to get the username of the author
		$query->select($db->quoteName('u.username', 'author'))
			->join('LEFT', $db->quoteName('#__users', 'u') . ' ON u.id = a.created_by');

		// Join with users table to get the username of the person who checked the record out
		$query->select($db->quoteName('u2.username', 'editor'))
			->join('LEFT', $db->quoteName('#__users', 'u2') . ' ON u2.id = a.checked_out');


		// Join with languages table to get the language title and image to display
		// Put these into fields called language_title and language_image so that 
		// we can use the little com_content layout to display the map symbol
		$query->select($db->quoteName('l.title', 'language_title') . "," .$db->quoteName('l.image', 'language_image'))
			->join('LEFT', $db->quoteName('#__languages', 'l') . ' ON l.lang_code = a.language');

		// Join over the associations - we just want to know if there are any, at this stage
		if (JLanguageAssociations::isEnabled())
		{
			$query->select('COUNT(asso2.id)>1 as association')
				->join('LEFT', '#__associations AS asso ON asso.id = a.id AND asso.context=' . $db->quote('com_helloworld.item'))
				->join('LEFT', '#__associations AS asso2 ON asso2.key = asso.key')
				->group('a.id');
		}

		// Filter: like / search
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			$like = $db->quote('%' . $search . '%');
			$query->where('greeting LIKE ' . $like);
		}

		// Filter by published state
		$published = $this->getState('filter.published');

		if (is_numeric($published))
		{
			$query->where('a.published = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(a.published IN (0, 1))');
		}

		// Filter by language, if the user has set that in the filter field
		$language = $this->getState('filter.language');
		if ($language)
		{
			$query->where('a.language = ' . $db->quote($language));
		}

		// Filter by categories
		$catid = $this->getState('filter.category_id');
		if ($catid)
		{
			$query->where("a.catid = " . $db->quote($db->escape($catid)));
		}

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering', 'greeting');
		$orderDirn 	= $this->state->get('list.direction', 'asc');

		$query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

		return $query;
	}
}