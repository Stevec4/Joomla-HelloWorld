<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Hello Table class
 *
 * @since  0.0.1
 */
class HelloWorldTableHelloWorld extends JTableNested
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  &$db  A database connector object
	 */
	function __construct(&$db)
	{
		parent::__construct('#__helloworld', 'id', $db);
	}
	/**
	 * Overloaded bind function
	 *
	 * @param       array           named array
	 * @return      null|string     null is operation was satisfactory, otherwise returns an error
	 * @see JTable:bind
	 * @since 1.5
	 */
	public function bind($array, $ignore = '')
	{
		if (isset($array['params']) && is_array($array['params']))
		{
			// Convert the params field to a string.
			$parameter = new JRegistry;
			$parameter->loadArray($array['params']);
			$array['params'] = (string)$parameter;
		}

		if (isset($array['imageinfo']) && is_array($array['imageinfo']))
		{
			// Convert the imageinfo array to a string.
			$parameter = new JRegistry;
			$parameter->loadArray($array['imageinfo']);
			$array['image'] = (string)$parameter;
		}

		// Bind the rules.
		if (isset($array['rules']) && is_array($array['rules']))
		{
			$rules = new JAccessRules($array['rules']);
			$this->setRules($rules);
		}

		if (isset($array['parent_id']))
		{
			if (!isset($array['id']) || $array['id'] == 0)
			{   // new record
				$this->setLocation($array['parent_id'], 'last-child');
			}
			elseif (isset($array['helloworldordering']))
			{
				// when saving a record load() is called before bind() so the table instance will have properties which are the existing field values
				if ($this->parent_id == $array['parent_id'])
				{
					// If first is chosen make the item the first child of the selected parent.
					if ($array['helloworldordering'] == -1)
					{
						$this->setLocation($array['parent_id'], 'first-child');
					}
					// If last is chosen make it the last child of the selected parent.
					elseif ($array['helloworldordering'] == -2)
					{
						$this->setLocation($array['parent_id'], 'last-child');
					}
					// Don't try to put an item after itself. All other ones put after the selected item.
					elseif ($array['helloworldordering'] && $this->id != $array['helloworldordering'])
					{
						$this->setLocation($array['helloworldordering'], 'after');
					}
					// Just leave it where it is if no change is made.
					elseif ($array['helloworldordering'] && $this->id == $array['helloworldordering'])
					{
						unset($array['helloworldordering']);
					}
				}
				// Set the new parent id if parent id not matched and put in last position
				else
				{
					$this->setLocation($array['parent_id'], 'last-child');
				}
			}
		}

		return parent::bind($array, $ignore);
	}

	/**
	 * Method to compute the default name of the asset.
	 * The default name is in the form `table_name.id`
	 * where id is the value of the primary key of the table.
	 *
	 * @return	string
	 * @since	2.5
	 */
	protected function _getAssetName()
	{
		$k = $this->_tbl_key;
		return 'com_helloworld.helloworld.'.(int) $this->$k;
	}
	/**
	 * Method to return the title to use for the asset table.
	 *
	 * @return	string
	 * @since	2.5
	 */
	protected function _getAssetTitle()
	{
		return $this->greeting;
	}
	/**
	 * Method to get the asset-parent-id of the item
	 *
	 * @return	int
	 */
	protected function _getAssetParentId(JTable $table = NULL, $id = NULL)
	{
		// We will retrieve the parent-asset from the Asset-table
		$assetParent = JTable::getInstance('Asset');
		// Default: if no asset-parent can be found we take the global asset
		$assetParentId = $assetParent->getRootId();

		// Find the parent-asset
		if (($this->catid)&& !empty($this->catid))
		{
			// The item has a category as asset-parent
			$assetParent->loadByName('com_helloworld.category.' . (int) $this->catid);
		}
		else
		{
			// The item has the component as asset-parent
			$assetParent->loadByName('com_helloworld');
		}

		// Return the found asset-parent-id
		if ($assetParent->id)
		{
			$assetParentId=$assetParent->id;
		}
		return $assetParentId;
	}

	public function check()
	{
		$this->alias = trim($this->alias);
		if (empty($this->alias))
		{
			$this->alias = $this->greeting;
		}
		$this->alias = JFilterOutput::stringURLSafe($this->alias);
		return true;
	}

	public function delete($pk = null, $children = false)
	{
		return parent::delete($pk, $children);
	}
}