<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 *
 * Layout file for the admin modal display of helloworld records
 */


// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

use Joomla\Registry\Registry;

JHtml::_('behavior.core');
JHtml::_('script', 'com_helloworld/admin-helloworlds-modal.js', array('version' => 'auto', 'relative' => true));

$listOrder     = $this->escape($this->state->get('list.ordering'));
$listDirn      = $this->escape($this->state->get('list.direction'));

$app = JFactory::getApplication();
$function  = $app->input->getCmd('function', 'jSelectHelloworld');
$onclick   = $this->escape($function);
?>
<div class="container-popup">
    
<form action="<?php echo JRoute::_('index.php?option=com_helloworld&view=helloworlds&layout=modal&tmpl=component&function=' . $function . '&' . JSession::getFormToken() . '=1'); ?>" method="post" name="adminForm" id="adminForm" class="form-inline">

	<?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
    
    <div class="clearfix"></div>

        <table class="table table-striped table-hover">
            <thead>
            <tr>
                <th width="3%"><?php echo JText::_('COM_HELLOWORLD_NUM'); ?></th>
                <th width="15%">
                    <?php echo JHtml::_('searchtools.sort', 'COM_HELLOWORLD_HELLOWORLDS_NAME', 'greeting', $listDirn, $listOrder); ?>
                </th>
                <th width="15%">
                    <?php echo JText::_('COM_HELLOWORLD_HELLOWORLDS_POSITION'); ?>
                </th>
                <th width="15%">
                    <?php echo JText::_('COM_HELLOWORLD_HELLOWORLDS_IMAGE'); ?>
                </th>
                <th width="15%">
                    <?php echo JHtml::_('searchtools.sort', 'COM_HELLOWORLD_AUTHOR', 'author', $listDirn, $listOrder); ?>
                </th>
                <th width="15%">
                    <?php echo JHtml::_('searchtools.sort', 'COM_HELLOWORLD_LANGUAGE', 'language', $listDirn, $listOrder); ?>
                </th>
                <th width="15%">
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
                    <td colspan="5">
                        <?php echo $this->pagination->getListFooter(); ?>
                    </td>
                </tr>
            </tfoot>
            <tbody>
                <?php if (!empty($this->items)) : ?>
                    <?php foreach ($this->items as $i => $row) :
                        $row->image = new Registry;
                        $row->image->loadString($row->imageInfo);
                        if ($row->language && JLanguageMultilang::isEnabled())
                        {
                            $tag = strlen($row->language);
                            if ($tag == 5)
                            {
                                $lang = substr($row->language, 0, 2);
                            }
                            elseif ($tag == 6)
                            {
                                $lang = substr($row->language, 0, 3);
                            }
                            else {
                                $lang = '';
                            }
                        }
                        elseif (!JLanguageMultilang::isEnabled())
                        {
                            $lang = '';
                        }
                    ?>
                        <tr>
                            <td><?php echo $this->pagination->getRowOffset($i); ?></td>
                            <td>
                                <?php 
                                $link = 'index.php?option=com_helloworld&view=helloworld&id=' . $row->id;
                                $attribs = 'data-function="' . $this->escape($onclick) . '"'
								. ' data-id="' . $row->id . '"'
								. ' data-title="' . $this->escape(addslashes($row->greeting)) . '"'
								. ' data-uri="' . $link . '"'
								. ' data-language="' . $this->escape($lang) . '"'
                                ;
                                ?>
                                <a class="select-link" href="javascript:void(0)" <?php echo $attribs; ?>>
                                    <?php echo $this->escape($row->greeting); ?>
                                </a>
                                <span class="small break-word">
                                	<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($row->alias)); ?>
                                </span>
                                <div class="small">
									<?php echo JText::_('JCATEGORY') . ': ' . $this->escape($row->category_title); ?>
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
</form>
</div>