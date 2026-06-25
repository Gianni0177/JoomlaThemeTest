<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_menu
 *
 * @copyright   (C) 2020 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\HTML\HTMLHelper;

$attributes = [];

$attributes['class'] = "";

if($item->level - $start + 1 > 1) {
	$attributes['class'] = " list-item ";
} else {
	$attributes['class'] = " nav-link ";
}

if ($showAll && $item->deeper) {
	$attributes['class'] .= " dropdown-toggle ";
	$attributes['data-toggle'] = "dropdown";
	$attributes['aria-expanded'] = "false";
}

if ($item->anchor_title)
{
	$attributes['title'] = $item->anchor_title;
}

if ($item->anchor_css)
{
	$attributes['class'] .= $item->anchor_css;
}

if ($item->anchor_rel)
{
	$attributes['rel'] = $item->anchor_rel;
}

if ($item->id == $active_id)
{
	$attributes['aria-current'] = 'location';

	if ($item->current)
	{
		$attributes['aria-current'] = 'page';
	}
}

$linktype =  "<span>".$item->title."</span>";

if ($item->menu_image)
{
	$linktype = HTMLHelper::image($item->menu_image, $item->title);

	if ($item->menu_image_css)
	{
		$image_attributes['class'] = $item->menu_image_css;
		$linktype                  = HTMLHelper::image($item->menu_image, $item->title, $image_attributes);
	}

	if ($itemParams->get('menu_text', 1))
	{
		$linktype .= '<span class="image-title">' . $item->title . '</span>';
	}
}

if ($item->browserNav == 1)
{
	$attributes['target'] = '_blank';
}
elseif ($item->browserNav == 2)
{
	$options = 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes';

	$attributes['onclick'] = "window.open(this.href, 'targetWindow', '" . $options . "'); return false;";
}

if (in_array($item->id, $path))
	{
		$attributes['class'] .= ' active ';
	}
	elseif ($item->type === 'alias')
	{
		$aliasToId = $itemParams->get('aliasoptions');

		if (count($path) > 0 && $aliasToId == $path[count($path) - 1])
		{
			$attributes['class'] .= ' active ';
		}
		elseif (in_array($aliasToId, $path))
		{
			$attributes['class'] .= ' alias-parent-active ';
		}
	}

echo HTMLHelper::link(OutputFilter::ampReplace(htmlspecialchars($item->flink, ENT_COMPAT, 'UTF-8', false)), $linktype, $attributes);

if ($showAll && $item->deeper)
{
	echo '<svg class="icon icon-xs"><use xlink:href="/bootstrap-italia/dist/svg/sprite.svg#it-expand"></use></svg>';
}
