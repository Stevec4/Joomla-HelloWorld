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
		$query->select('id, greeting, alias, catid')
			->from($db->quoteName('#__helloworld'))
			->where('catid = ' . $catid);

		if (JLanguageMultilang::isEnabled())
		{
			$lang = JFactory::getLanguage()->getTag();
			$query->where('language IN ("*","' . $lang . '")');
		}

		$orderCol	= $this->state->get('list.ordering', 'greeting');
		$orderDirn 	= $this->state->get('list.direction', 'asc');

		$query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

		return $query;	
	}
	public function getCategoryName()
	{
		$catid = $this->getState('category.id'); 
		$categories = JCategories::getInstance('Helloworld', array());
		$categoryNode = $categories->get($catid);   
		return $categoryNode->title; 
	}
    
	public function getSubcategories()
	{
		$catid = $this->getState('category.id'); 
		$categories = JCategories::getInstance('Helloworld', array());
		$categoryNode = $categories->get($catid);
		$subcats = $categoryNode->getChildren(); 
        
		$lang = JFactory::getLanguage()->getTag();
		if (JLanguageMultilang::isEnabled() && $lang)
		{
			$query_lang = "&lang={$lang}";
		}
		else
		{
			$query_lang = '';
		}
        
		foreach ($subcats as $subcat)
		{
			$subcat->url = JRoute::_("index.php?view=category&id=" . $subcat->id . $query_lang);
		}
		return $subcats;
	}
}