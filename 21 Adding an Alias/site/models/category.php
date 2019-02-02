<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 *
 * Model for displaying the helloworld messages in a given category
 */

defined('_JEXEC') or die;

class HelloworldModelCategory extends JModelList
{
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id',
				'greeting',
				'alias',
			);
		}

		parent::__construct($config);
	}
    
	protected function populateState($ordering = null, $direction = null)
	{
		parent::populateState($ordering, $direction);
        
		$app = JFactory::getApplication('site');
		$catid = $app->input->getInt('id');

		$this->setState('category.id', $catid);
	}
    
	protected function getListQuery()
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$catid = $this->getState('category.id'); 
		$query->select('id, greeting, alias')
			->from($db->quoteName('#__helloworld'))
			->where('catid = ' . $catid);

		$orderCol	= $this->state->get('list.ordering', 'greeting');
		$orderDirn 	= $this->state->get('list.direction', 'asc');

		$query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

		return $query;	
	}
}