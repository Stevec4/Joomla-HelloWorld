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

		if (!JLanguageMultilang::isEnabled() || !isset($query['view']))
		{
			return $segments;
		}

		$lang = JFactory::getLanguage()->getTag();
		$app  = JFactory::getApplication();
        
		// get the menu item that this call to build() relates to
		if (!isset($query['Itemid']))
		{
			return $segments;
		}
		$sitemenu = $app->getMenu();
		$thisMenuitem = $sitemenu->getItem($query['Itemid']);

		if ($thisMenuitem->language != $lang)
		{
			return $segments;
		}
        
		if ($thisMenuitem->note == "Ajax")
		{   
			// We're on the /message menuitem. 
			// Check we've got the right parameters then set url segment = id : alias
			if ($query['view'] == "helloworld" && isset($query['id']))
			{
				// we'll support the passed id being in the form id:alias
				$segments[] = $query['id'];

				unset($query['id']);
				unset($query['catid']);
			}
		}
		else
		{
			// assume we're on the /messages menuitem
			if (($query['view'] == "category") && isset($query['id']))
			{
				// set this part of the url to be of the form /subcat1/subcat2/...
				$pathSegments = $this->getCategorySegments($query['id']);
				if ($pathSegments)
				{
					$segments = $pathSegments;
					unset($query['id']);
				}
			}
			elseif ($query['view'] == "helloworld" && isset($query['catid']) && isset($query['id']))
			{
				// set this part of the url to be of the form /subcat1/subcat2/.../hello-world 
				$pathSegments = $this->getCategorySegments($query['catid']);
				if ($pathSegments)
				{
					$segments = $pathSegments;
				}

				$segments[] = $query['id'];

				unset($query['id']);
				unset($query['catid']);
			}
		}

		unset($query['view']);
		return $segments;
	}

	/*
	 * This function take a category id and finds the path from that category to the root of the category tree
	 * The path returned from getPath() is an associative array of key = category id, value = id:alias
	 * If no valid category is found from the passed-in category id then null is returned. 
	 */
     
	private function getCategorySegments($catid)
	{
		$categories = JCategories::getInstance('Helloworld', array());
		$categoryNode = $categories->get($catid);
		if ($categoryNode)
		{
			$path = $categoryNode->getPath();

			return $path;
		}
		else
		{
			return null;
		}
	}

	public function parse(&$segments)
	{
		$vars = array();
		$nSegments = count($segments);
        
		$app  = JFactory::getApplication();
		$sitemenu = $app->getMenu();
		$activeMenuitem = $sitemenu->getActive();
		if (!$activeMenuitem)
		{
			return $vars;
		}
        
		if ($activeMenuitem->note == "Ajax")
		{
			// Expect 1 segment of the form id:alias for the helloworld record
			if ($nSegments == 1)
			{
				$vars['id'] = $segments[0];
				$vars['view'] = 'helloworld';
			}
		}
		else
		{
			// Try to match the categories in the segments, starting at the root
			$categories = JCategories::getInstance('Helloworld', array());
			$matchingCategory = $categories->get('root');
            
			// Go through the category tree, try to get a match between each segment
			// and the id:alias of one of the children
			// The last segment may be a category id:alias or a helloworld record id:alias
			for ($i=0; $i < $nSegments; $i++)
			{
				$children = $matchingCategory->getChildren();
				$matchingCategory = $this->match($children, $segments[$i]);
				if ($matchingCategory)
				{
					$catid = $matchingCategory->id;
					if ($i == $nSegments - 1)    // we're done, all segments are categories
					{
						$vars['view'] = 'category';
						$vars['id'] = $catid;
					}
				}
				else
				{
					if ($i == $nSegments - 1)   // all but last segment are categories
					{
						$vars['id'] = $segments[$i];
						$vars['view'] = 'helloworld';
					}
					else   // something went wrong - didn't get a match at this level
					{
						break;
					}
				}
			}
		}

		return $vars;
	}

	/*
	 * This function takes an array of categoryNode elements and a url segment
	 * It goes through the categoryNodes looking for the one whose id:alias matches the passed-in segment
	 *   and returns the matching categoryNode, or null if not found
	 */
	private function match($categoryNodes, $segment)
	{
		foreach ($categoryNodes as $categoryNode)
		{
			if ($segment == $categoryNode->id . ':' . $categoryNode->alias)
			{
				return $categoryNode;
			}
		}
		return null;
	}

	public function preprocess($query)
	{
		return $query;
	}
}