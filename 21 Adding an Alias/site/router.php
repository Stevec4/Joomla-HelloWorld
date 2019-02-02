<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die;

class HelloworldRouter implements JComponentRouterInterface
{

	public function build(&$query)
	{
		$segments = array();
		if (isset($query['id']))
		{
			$db = JFactory::getDbo();
			$qry = $db->getQuery(true);
			$qry->select('alias');
			$qry->from('#__helloworld');
			$qry->where('id = ' . $db->quote($query['id']));
			$db->setQuery($qry);
			$alias = $db->loadResult();
			$segments[] = $alias;
			unset($query['id']);
		}
		unset($query['view']);
		return $segments;
	}
  
	public function parse(&$segments)
	{
		$vars = array();
    
		$db = JFactory::getDbo();
		$qry = $db->getQuery(true);
		$qry->select('id');
		$qry->from('#__helloworld');
		$qry->where('alias = ' . $db->quote($segments[0]));
		$db->setQuery($qry);
		$id = $db->loadResult();
        
		if(!empty($id))
		{
			$vars['id'] = $id;
			$vars['view'] = 'helloworld';
		}

		return $vars;
	}
  
	public function preprocess($query)
	{
		return $query;
	}
}