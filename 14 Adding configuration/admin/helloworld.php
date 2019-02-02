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

// Set some global property
$document = JFactory::getDocument();
$document->addStyleDeclaration('.icon-helloworld {background-image: url(../media/com_helloworld/images/Tux-16x16.png);}');

// Require helper file
JLoader::register('HelloWorldHelper', JPATH_COMPONENT . '/helpers/helloworld.php');

// Get an instance of the controller prefixed by HelloWorld
$controller = JControllerLegacy::getInstance('HelloWorld');

// Perform the Request task
$controller->execute(JFactory::getApplication()->input->get('task'));

// Redirect if set by the controller
$controller->redirect();
