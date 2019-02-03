<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Script file of HelloWorld component.
 *
 * The name of this class is dependent on the component being installed.
 * The class name should have the component's name, directly followed by
 * the text InstallerScript (ex:. com_helloWorldInstallerScript).
 *
 * This class will be called by Joomla!'s installer, if specified in your component's
 * manifest file, and is used for custom automation actions in its installation process.
 *
 * In order to use this automation script, you should reference it in your component's
 * manifest file as follows:
 * <scriptfile>script.php</scriptfile>
 *
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
class com_helloWorldInstallerScript
{
    /**
     * This method is called after a component is installed.
     *
     * @param  \stdClass $parent - Parent object calling this method.
     *
     * @return void
     */
    public function install($parent) 
    {
        $parent->getParent()->setRedirectURL('index.php?option=com_helloworld');
    }

    /**
     * This method is called after a component is uninstalled.
     *
     * @param  \stdClass $parent - Parent object calling this method.
     *
     * @return void
     */
    public function uninstall($parent) 
    {
        echo '<p>' . JText::_('COM_HELLOWORLD_UNINSTALL_TEXT') . '</p>';
    }

    /**
     * This method is called after a component is updated.
     *
     * @param  \stdClass $parent - Parent object calling object.
     *
     * @return void
     */
    public function update($parent) 
    {
        echo '<p>' . JText::sprintf('COM_HELLOWORLD_UPDATE_TEXT', $parent->get('manifest')->version) . '</p>';
    }

    /**
     * Runs just before any installation action is preformed on the component.
     * Verifications and pre-requisites should run in this function.
     *
     * @param  string    $type   - Type of PreFlight action. Possible values are:
     *                           - * install
     *                           - * update
     *                           - * discover_install
     * @param  \stdClass $parent - Parent object calling object.
     *
     * @return void
     */
    public function preflight($type, $parent) 
    {
        echo '<p>' . JText::_('COM_HELLOWORLD_PREFLIGHT_' . $type . '_TEXT') . '</p>';
    }

    /**
     * Runs right after any installation action is preformed on the component.
     *
     * @param  string    $type   - Type of PostFlight action. Possible values are:
     *                           - * install
     *                           - * update
     *                           - * discover_install
     * @param  \stdClass $parent - Parent object calling object.
     *
     * @return void
     */
    function postflight($type, $parent) 
    {
		$db = JFactory::getDbo();
		
		echo '<p>Checking if the root record is already present ...</p>';
		
		$query = $db->getQuery(true);
		$query->select('id');
		$query->from('#__helloworld');
		$query->where('id = 1');
		$query->where('alias = "helloworld-root-alias"');
		$db->setQuery($query);
		$id = $db->loadResult();
		
		if ($id == '1')
		{   // assume tree structure already built
			echo '<p>Root record already present, install program exiting ...</p>';
			return;
		}

		echo '<p>Checking if there is a record with id = 1 ...</p>';
		
		$query = $db->getQuery(true);
		$query->select('id');
		$query->from('#__helloworld');
		$query->where('id = 1');
		$db->setQuery($query);
		$id = $db->loadResult();
			
		if ($id)
		{
			echo '<p>Record with id = 1 found</p>';
			
			// get new id
			$query = $db->getQuery(true)
				->select('max(id) + 1')
				->from('#__helloworld');
			$db->setQuery($query);
			$newid = $db->loadResult(); 
			echo "<p>Changing id to $newid</p>";
			
			// update id in helloworld table
			$query = $db->getQuery(true)
				->update('#__helloworld')
				->set("id = $newid")
				->where("id = $id");
			$db->setQuery($query);
			$result = $db->execute();
			if ($result)
			{
				$nrows = $db->getAffectedRows();
				echo "<p>Id in helloworld table changed, records updated: $nrows</p>";
			}
			else
			{
				echo "<p>Error: Id in helloworld table not changed</p>";
				var_dump($result);
			}
			
			// update id in the associations table
			$query = $db->getQuery(true)
				->update('#__associations')
				->set("id = $newid")
				->where("id = $id")
				->where('context = "com_helloworld.item"');
			$db->setQuery($query);
			$result = $db->execute();
			if ($result)
			{
				$nrows = $db->getAffectedRows();
				echo "<p>Id in associations table changed, records updated: $nrows</p>";
			}
			else
			{
				echo "<p>Error: Id in associations table not changed</p>";
				var_dump($result);
			}
			
			// update id in the assets table
			$query = $db->getQuery(true)
				->update('#__assets')
				->set('name = "com_helloworld.helloworld.' . $newid . '"')
				->where('name = "com_helloworld.helloworld.' . $id . '"');
			$db->setQuery($query);
			$result = $db->execute();
			if ($result)
			{
				$nrows = $db->getAffectedRows();
				echo "<p>Id in assets table changed, records updated: $nrows</p>";
			}
			else
			{
				echo "<p>Error: Id in assets table not changed</p>";
				var_dump($result);
			}
		}
		else 
		{
			echo '<p>No record with id = 1 found</p>';
		}
		
		// find number of records in helloworld table
		$query = $db->getQuery(true)
			->select('count(*)')
			->from('#__helloworld');
		$db->setQuery($query);
		$total = $db->loadResult(); 
		
		// insert root record
		$columns = array('id','greeting','alias','parent_id','rgt');
		$values = array(1, 'helloworld root','helloworld-root-alias',0, 2 * (int)$total + 1);

		$query = $db->getQuery(true)
			->insert('#__helloworld')
			->columns($db->quoteName($columns))
			->values(implode(',', $db->quote($values)));
		$db->setQuery($query);
		$result = $db->execute();
		if ($result)
		{
			$nrows = $db->getAffectedRows();
			echo "<p>$nrows inserted into helloworld table</p>";
		}
		else
		{
			echo "<p>Error creating root record</p>";
			var_dump($result);
		}
		
		// update lft and rgt for each of the other records (ie not root)
		$query = $db->getQuery(true)
			->select('id')
			->from('#__helloworld')
			->where('id > 1');
		$db->setQuery($query);
		$ids = $db->loadColumn(); 
		for ($i = 0; $i < $total; $i++)
		{
			$lft = 2 * (int)$i + 1;
			$rgt = 2 * (int)$i + 2;
			$query = $db->getQuery(true)
				->update('#__helloworld')
				->set("lft = {$lft}")
				->set("rgt = {$rgt}")
				->where("id = {$ids[$i]}");
			$db->setQuery($query);
			$result = $db->execute();
			if ($result)
			{
				$nrows = $db->getAffectedRows();
				echo "<p>$nrows updated in helloworld table, for id = {$ids[$i]}</p>";
			}
			else
			{
				echo "<p>Error updating record</p>";
				var_dump($result);
			}
		}
    }
}