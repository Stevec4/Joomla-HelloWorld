<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 *
 * View file for the view which displays a list of helloworld messages in a given category
 */

defined('_JEXEC') or die;

class HelloworldViewCategory extends JViewLegacy
{
	public function display($tpl = null)
	{
		$this->categoryName = $this->get("CategoryName");

		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
		$this->filterForm    	= $this->get('FilterForm');
		$this->activeFilters 	= $this->get('ActiveFilters');
		
		$this->subcategories = $this->get('Subcategories');

		parent::display($tpl);
	}
}
