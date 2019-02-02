<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

class HelloworldCategories extends JCategories
{

	public function __construct($options = array())
	{
		$options['table'] = '#__helloworld';
		$options['extension'] = 'com_helloworld';

		parent::__construct($options);
	}
}