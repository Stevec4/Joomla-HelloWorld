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
<h1><?php echo $this->item->greeting.(($this->item->category and $this->item->params->get('show_category'))
                                      ? (' ('.$this->item->category.')') : ''); ?>
</h1>
<?php
    $src = $this->item->imageDetails['image'];
    if ($src)
    {
        $html = '<figure>
                    <img src="%s" alt="%s" >
                    <figcaption>%s</figcaption>
                </figure>';
        $alt = $this->item->imageDetails['alt'];
        $caption = $this->item->imageDetails['caption'];
        echo sprintf($html, $src, $alt, $caption);
    } ?>

<?php if ($this->parentItem->id > 1) : ?>
	<h1><?php echo JText::_('COM_HELLOWORLD_PARENT') ?>
	</h1>
	<h3>
		<?php $url = JRoute::_('index.php?option=com_helloworld&view=helloworld&id=' . $this->parentItem->id . ':' . $this->parentItem->alias . '&catid=' . $this->parentItem->catid . $query_lang); ?>
		<a href="<?php echo $url; ?>"><?php echo $this->parentItem->greeting; ?></a>
	</h3>
<?php endif; ?>

<?php if ($this->children) : 
		$baseLevel = $this->item->level; ?>
		<h1><?php echo JText::_('COM_HELLOWORLD_CHILDREN') ?>
		</h1>
		<?php foreach ($this->children as $i => $child) : ?>
			<h3>
				<?php $prefix = JLayoutHelper::render('joomla.html.treeprefix', array('level' => $child->level - $baseLevel)); ?>
				<?php echo $prefix; ?>
				<?php $url = JRoute::_('index.php?option=com_helloworld&view=helloworld&id=' . $child->id . ':' . $child->alias . '&catid=' . $child->catid . $query_lang); ?>
				<a href="<?php echo $url; ?>"><?php echo $child->greeting; ?></a>
			</h3>
	<?php endforeach; ?>
<?php endif; ?>

<div id="map" class="map"></div>
<div class="map-callout map-callout-bottom" id="greeting-container"></div>
<div id="searchmap">
    <?php echo '<input id="token" type="hidden" name="' . JSession::getFormToken() . '" value="1" />'; ?>
    <button type="button" class="btn btn-primary" onclick="searchHere();">
        <?php echo JText::_('COM_HELLOWORLD_SEARCH_HERE_BUTTON') ?>
    </button>
    <div id="searchresults">
    </div>
</div>