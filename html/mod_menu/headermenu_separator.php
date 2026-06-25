<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_menu
 *
 * @copyright   (C) 2020 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

$app   = Factory::getApplication();
$template   = $app->getTemplate();
$templatePath = JUri::base() . '/templates/' . $template;
$iconsPath = $templatePath . "/svg/sprites.svg";

$attributes = [];

if ($item->anchor_title)
{
	$attributes['title'] = $item->anchor_title;
}

$attributes['class'] = "separator ";
$attributes['class'] .= $item->anchor_css ? ' ' . $item->anchor_css : null;

if($item->level - $start + 1 > 1) {
	$attributes['class'] = " dropdown-item list-item ";
} else {
	$attributes['class'] = " nav-link ";
}

$linktype = "<span>". $item->title."</span>";

if ($showAll && $item->deeper)
{
	$linktype = $linktype . '<svg class="icon icon-xs"><use xlink:href="'. $iconsPath . '#it-expand"></use></svg>';
}


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


if (in_array($item->id, $path))
	{
		$attributes['class'] .= ' active ';
		$attributes['aria-current'] = 'page';
	}
	elseif ($item->type === 'alias')
	{
		$aliasToId = $itemParams->get('aliasoptions');

		if (count($path) > 0 && $aliasToId == $path[count($path) - 1])
		{
			$attributes['class'] .= ' active ';
			$attributes['aria-current'] = 'page';
		}
		elseif (in_array($aliasToId, $path))
		{
			$attributes['class'] .= ' alias-parent-active ';
		}
	}
	
if ($showAll && $item->deeper) {
	$attributes['id'] .= "nav_item_link_" . $item->id;
	$attributes['class'] .= " dropdown-toggle ";
	$attributes['role'] = "button";
	$attributes['data-bs-toggle'] = "dropdown";
	$attributes['aria-expanded'] = "false";
}


echo HTMLHelper::link("#", $linktype, $attributes);
