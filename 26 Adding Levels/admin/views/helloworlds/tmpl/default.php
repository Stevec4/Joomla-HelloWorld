<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */


// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

use Joomla\Registry\Registry;

JHtml::_('formbehavior.chosen', 'select');

$listOrder     = $this->escape($this->state->get('list.ordering'));
$listDirn      = $this->escape($this->state->get('list.direction'));
$user = JFactory::getUser();
$userId = $user->get('id');
$saveOrder = ($listOrder == 'lft' && strtolower($listDirn) == 'asc');
if ($saveOrder)
	{
	$saveOrderingUrl = 'index.php?option=com_helloworld&task=helloworlds.saveOrderAjax&tmpl=component';
	// pass true as parameter 7 to indicate that we have a nested set
    JHtml::_('sortablelist.sortable', 'helloworldList', 'adminForm', strtolower($listDirn), $saveOrderingUrl, false, true);
	}
$assoc = JLanguageAssociations::isEnabled();
$authorFieldwidth = $assoc ? "10%" : "25%";
JLoader::register('JHtmlHelloworlds', JPATH_ADMINISTRATOR . '/components/com_helloworld/helpers/html/helloworlds.php');
?>
<form action="index.php?option=com_helloworld&view=helloworlds" method="post" id="adminForm" name="adminForm">
	<div id="j-sidebar-container" class="span2">
		<?php echo JHtmlSidebar::render(); ?>
	</div>
	<div id="j-main-container" class="span15">
        <div class="row-fluid">
            <div class="span15">
                <?php echo JText::_('COM_HELLOWORLD_HELLOWORLDS_FILTER'); ?>
                <?php
                    echo JLayoutHelper::render(
                        'joomla.searchtools.default',
                        array('view' => $this)
                    );
                ?>
            </div>
        </div>
        <table class="table table-striped table-hover" id="helloworldList">
            <thead>
            <tr>
                <th width="1%">
                    <?php echo JHtml::_('searchtools.sort', '', 'lft', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
                </th>
                <th width="1%"><?php echo JText::_('COM_HELLOWORLD_NUM'); ?></th>
                <th width="1%">
                    <?php echo JHtml::_('grid.checkall'); ?>
                </th>
                <th width="10%">
                    <?php echo JHtml::_('searchtools.sort', 'COM_HELLOWORLD_HELLOWORLDS_NAME', 'greeting', $listDirn, $listOrder); ?>
                </th>
                <th width="10%">
                    <?php echo JText::_('COM_HELLOWORLD_HELLOWORLDS_POSITION'); ?>
                </th>
                <th width="10%">
                    <?php echo JText::_('COM_HELLOWORLD_HELLOWORLDS_IMAGE'); ?>
                </th>
                <th width="5%">
                    <?php echo "lft"; ?>
                </th>
                <th width="5%">
                    <?php echo "rgt"; ?>
                </th>
                <th width="5%">
                    <?php echo "level"; ?>
                </th>
                <th width="5%">
                    <?php echo "parent"; ?>
                </th>
                <?php if ($assoc) : ?>
                    <th width="10%">
                        <?php echo JHtml::_('searchtools.sort', 'COM_HELLOWORLD_HELLOWORLDS_ASSOCIATIONS', 'association', $listDirn, $listOrder); ?>
                    </th>
                <?php endif; ?>
                <th width="<?php echo $authorFieldwidth; ?>">
                    <?php echo JHtml::_('searchtools.sort', 'COM_HELLOWORLD_AUTHOR', 'author', $listDirn, $listOrder); ?>
                </th>
                <th width="10%">
                    <?php echo JHtml::_('searchtools.sort', 'COM_HELLOWORLD_LANGUAGE', 'language', $listDirn, $listOrder); ?>
                </th>
                <th width="10%">
                    <?php echo JHtml::_('searchtools.sort', 'COM_HELLOWORLD_CREATED_DATE', 'created', $listDirn, $listOrder); ?>
                </th>
                <th width="5%">
                    <?php echo JHtml::_('searchtools.sort', 'COM_HELLOWORLD_PUBLISHED', 'published', $listDirn, $listOrder); ?>
                </th>
                <th width="2%">
                    <?php echo JHtml::_('searchtools.sort', 'COM_HELLOWORLD_ID', 'id', $listDirn, $listOrder); ?>
                </th>
            </tr>
            </thead>

            <tfoot>
                <tr>
                    <td colspan="15">
                        <?php echo $this->pagination->getListFooter(); ?>
                    </td>
                </tr>
            </tfoot>
            <tbody>
                <?php if (!empty($this->items)) : ?>
                    <?php foreach ($this->items as $i => $row) :
                        $link = JRoute::_('index.php?option=com_helloworld&task=helloworld.edit&id=' . $row->id);
                        $row->image = new Registry;
                        $row->image->loadString($row->imageInfo);
                        // create a list of the parents up the hierarchy to the root 
                        if ($row->level > 1)
                        {
                            $parentsStr = '';
                            $_currentParentId = $row->parent_id;
                            $parentsStr = ' ' . $_currentParentId;
                            for ($j = 0; $j < $row->level; $j++)
                            {
                                foreach ($this->ordering as $k => $v)
                                {
                                    $v = implode('-', $v);
                                    $v = '-' . $v . '-';
                                    if (strpos($v, '-' . $_currentParentId . '-') !== false)
                                    {
                                        $parentsStr .= ' ' . $k;
                                        $_currentParentId = $k;
                                        break;
                                    }
                                }
                            }
                        }
                        else
                        {
                            $parentsStr = '';
                        }
                    ?>
                        <tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $row->parent_id; ?>" item-id="<?php echo $row->id; ?>" parents="<?php echo $parentsStr; ?>" level="<?php echo $row->level; ?>">
                            <td><?php
                                $iconClass = '';
                                $canReorder  = $user->authorise('core.edit.state', 'com_helloworld.helloworld.' . $row->id);
                                if (!$canReorder)
                                {
                                    $iconClass = ' inactive';
                                }
                                elseif (!$saveOrder)
                                {
                                    $iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::_('tooltipText', 'JORDERINGDISABLED');
                                }
                                ?>
                                <span class="sortable-handler<?php echo $iconClass ?>">
                                    <span class="icon-menu" aria-hidden="true"></span>
                                </span>
                                <?php if ($canReorder && $saveOrder) : ?>
                                    <input type="text" style="display:none" name="order[]" size="5" value="<?php echo $row->lft; ?>" class="width-20 text-area-order" />
                                <?php endif; ?>
                            </td>
                            <td><?php echo $this->pagination->getRowOffset($i); ?></td>
                            <td>
                                <?php echo JHtml::_('grid.id', $i, $row->id); ?>
                            </td>
                            <td>
                                <?php $prefix = JLayoutHelper::render('joomla.html.treeprefix', array('level' => $row->level)); ?>
                                <?php echo $prefix; ?>
                                <?php if ($row->checked_out) : ?>
                                    <?php $canCheckin = $user->authorise('core.manage', 'com_checkin') || $row->checked_out == $userId; ?>
                                    <?php echo JHtml::_('jgrid.checkedout', $i, $row->editor, $row->checked_out_time, 'helloworlds.', $canCheckin); ?>
                                <?php endif; ?>
                                <a href="<?php echo $link; ?>" title="<?php echo JText::_('COM_HELLOWORLD_EDIT_HELLOWORLD'); ?>">
                                    <?php echo $row->greeting; ?>
                                </a>
                                <span class="small break-word">
                                        <?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($row->alias)); ?>
                                </span>
                                <div class="small">
                                    <?php echo JText::_('JCATEGORY') . ': ' . $this->escape($row->category_title); ?>
                                </div>
                                <div class="small">
                                    <?php echo 'Path: ' . $this->escape($row->path); ?>
                                </div>
                            </td>
                            <td align="center">
                                <?php echo "[" . $row->latitude . ", " . $row->longitude . "]"; ?>
                            </td>
                            <td align="center">
                                <?php
                                    $caption = $row->image->get('caption') ? : '' ;
                                    $src = JURI::root() . ($row->image->get('image') ? : '' );
                                    $html = '<p class="hasTooltip" style="display: inline-block" data-html="true" data-toggle="tooltip" data-placement="right" title="<img width=\'100px\' height=\'100px\' src=\'%s\'>">%s</p>';
                                    echo sprintf($html, $src, $caption);  ?>
                            </td>
                            <td align="center">
                                <?php echo $row->lft; ?>
                            </td>
                            <td align="center">
                                <?php echo $row->rgt; ?>
                            </td>
                            <td align="center">
                                <?php echo $row->level; ?>
                            </td>
                            <td align="center">
                                <?php echo $row->parent_id; ?>
                            </td>
                            <?php if ($assoc) : ?>
                                <td align="center">
                                    <?php if ($row->association) : ?>
                                        <?php echo JHtml::_('helloworlds.association', $row->id); ?>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                            <td align="center">
                                <?php echo $row->author; ?>
                            </td>
                            <td align="center">
                                <?php echo JLayoutHelper::render('joomla.content.language', $row); ?>
                            </td>
                            <td align="center">
                                <?php echo substr($row->created, 0, 10); ?>
                            </td>
                            <td align="center">
                                <?php echo JHtml::_('jgrid.published', $row->published, $i, 'helloworlds.', true, 'cb'); ?>
                            </td>
                            <td align="center">
                                <?php echo $row->id; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <input type="hidden" name="task" value=""/>
        <input type="hidden" name="boxchecked" value="0"/>
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>