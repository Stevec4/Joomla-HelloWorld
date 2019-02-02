<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 *
 * The Helloworld helper file for Multilingual Associations
 */

// No direct access to this file
defined('_JEXEC') or die;

JTable::addIncludePath(__DIR__ . '/../tables');

class HelloworldAssociationsHelper extends JAssociationExtensionHelper
{
	/**
	 * The extension name
	 */
	protected $extension = 'com_helloworld';

	/**
	 * Array of item types which have associations
	 */
	protected $itemTypes = array('helloworld', 'category');

	/**
	 * Has the extension association support
	 */
	protected $associationsSupport = true;

	/**
	 * Get the associated items for an item
	 *
	 * @param   string  $typeName  The item type, either 'helloworld' or 'category'
	 * @param   int     $id        The id of item for which we need the associated items
	 *
	 */
	public function getAssociations($typeName, $id)
	{
		$type = $this->getType($typeName);

		$context    = $this->extension . '.item';
		$catidField = 'catid';

		if ($typeName === 'helloworld')
		{
			$context    = 'com_helloworld.item';
			$catidField = 'catid';
		}
        elseif ($typeName === 'category')
		{
			$context    = 'com_categories.item';
			$catidField = '';
		}
        else
        {
            return null;
        }

		// Get the associations.
		$associations = JLanguageAssociations::getAssociations(
			$this->extension,
			$type['tables']['a'],
			$context,
			$id,
			'id',
			'alias',
			$catidField
		);

		return $associations;
	}

	/**
	 * Get item information
	 *
	 * @param   string  $typeName  The item type
	 * @param   int     $id        The id of item for which we need the associated items
	 *
	 * @return  JTable object associated with the record id passed in
	 */
	public function getItem($typeName, $id)
	{
		if (empty($id))
		{
			return null;
		}

		$table = null;

		switch ($typeName)
		{
			case 'helloworld':
				$table = JTable::getInstance('Helloworld', 'HelloworldTable');
				break;

			case 'category':
				$table = JTable::getInstance('Category');
				break;
		}

		if (empty($table))
		{
			return null;
		}

		$table->load($id);

		return $table;
	}

	/**
	 * Get information about the type
	 *
	 * @param   string  $typeName  The item type
	 *
	 * @return  array  Array of item types
	 */
	public function getType($typeName = '')
	{
		$fields  = $this->getFieldsTemplate();
		$tables  = array();
		$joins   = array();
		$support = $this->getSupportTemplate();
		$title   = '';

		if (in_array($typeName, $this->itemTypes))
		{
			switch ($typeName)
			{
				case 'helloworld':
					$fields['title'] = 'a.greeting';
                    $fields['ordering'] = '';
                    $fields['access'] = '';
                    $fields['state'] = 'a.published';
                    $fields['created_user_id'] = '';
                    $fields['checked_out'] = '';
                    $fields['checked_out_time'] = '';

					$support['state'] = true;
					$support['acl'] = false;
					$support['category'] = true;

					$tables = array(
						'a' => '#__helloworld'
					);

					$title = 'helloworld';
					break;

				case 'category':
					$fields['created_user_id'] = 'a.created_user_id';
					$fields['ordering'] = 'a.lft';
					$fields['level'] = 'a.level';
					$fields['catid'] = '';
					$fields['state'] = 'a.published';

					$support['state'] = true;
					$support['acl'] = true;
					$support['checkout'] = true;
					$support['level'] = true;

					$tables = array(
						'a' => '#__categories'
					);

					$title = 'category';
					break;
			}
		}

		return array(
			'fields'  => $fields,
			'support' => $support,
			'tables'  => $tables,
			'joins'   => $joins,
			'title'   => $title
		);
	}
}
