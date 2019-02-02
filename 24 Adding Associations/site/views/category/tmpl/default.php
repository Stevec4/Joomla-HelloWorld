<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 *
 * Layout file for displaying helloworld messages belonging to a given category
 */
 
defined('_JEXEC') or die;

JHtml::_('formbehavior.chosen', 'select');

$listOrder     = $this->escape($this->state->get('list.ordering'));
$listDirn      = $this->escape($this->state->get('list.direction'));
$lang = JFactory::getLanguage()->getTag();
if (JLanguageMultilang::isEnabled() && $lang)
{
    $query_lang = "&lang={$lang}";
}
else
{
    $query_lang = "";
}
?>

<form action="#" method="post" id="adminForm" name="adminForm">
<h1><?php echo $this->categoryName; ?></h1>
<div id="j-main-container" class="span10">
    <div class="row-fluid">
        <div class="span10">
            <?php
                echo JLayoutHelper::render(
                    'joomla.searchtools.default',
                    array('view' => $this, 'searchButton' => false)
                );
            ?>
        </div>
    </div>
<table class="table table-striped table-hover">
    <thead>
    <tr>
        <th width="5%"><?php echo JText::_('JGLOBAL_NUM'); ?></th>
        <th width="20%">
            <?php echo JHtml::_('searchtools.sort', 'COM_HELLOWORLD_HELLOWORLD_GREETING_LABEL', 'greeting', $listDirn, $listOrder); ?>
        </th>
        <th width="20%">
            <?php echo JHtml::_('searchtools.sort', 'COM_HELLOWORLD_HELLOWORLD_ALIAS_LABEL', 'alias', $listDirn, $listOrder); ?>
        </th>
        <th width="20%">
            <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_FIELD_URL_LABEL'); ?>
        </th>
        <th width="5%">
            <?php echo JHtml::_('searchtools.sort', 'JGLOBAL_FIELD_ID_LABEL', 'id', $listDirn, $listOrder); ?>
        </th>
    </tr>
    </thead>
    <tfoot>
        <tr>
            <td colspan="5">
                <?php echo $this->pagination->getListFooter(); ?>
            </td>
        </tr>
    </tfoot>
    <tbody>
        <?php if (!empty($this->items)) : ?>
            <?php foreach ($this->items as $i => $row) : 
                 $url = JRoute::_('index.php?option=com_helloworld&view=helloworld&id=' . $row->id . ':' . $row->alias . '&catid=' . $row->catid . $query_lang);
                ?>
                <tr>
                    <td align="center"><?php echo $this->pagination->getRowOffset($i); ?></td>
                    <td align="center"><?php echo $row->greeting; ?></td>
                    <td align="center"><?php echo $row->alias; ?></td>
                    <td align="center"><a href="<?php echo $url; ?>"><?php echo $url; ?></a></td>
                    <td align="center"><?php echo $row->id; ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
<h1><?php echo JText::_('COM_HELLOWORLD_HEADER_SUBCATEGORIES'); ?></h1>
	<?php foreach ($this->subcategories as $subcategory) : ?>
    <h3><a href="<?php echo $subcategory->url; ?>"> <?php echo $subcategory->title; ?> </a></h3>
    <p><?php echo $subcategory->description; ?></p>
	<?php endforeach; ?>

</div>
</form>