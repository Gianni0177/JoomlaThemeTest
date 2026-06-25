<?php

/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   (C) 2016 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

$app = Factory::getApplication();
$template   = $app->getTemplate();
$templatePath = JUri::base() . '/templates/' . $template;
$iconsPath = $templatePath . "/svg/sprites.svg";


$article = $displayData['article'];
$tooltip = $displayData['tooltip'];
$nowDate = strtotime(Factory::getDate());

$icon = $article->state ? 'it-pencil' : 'it-close-circle';
$currentDate   = Factory::getDate()->format('Y-m-d H:i:s');
$isUnpublished = ($article->publish_up > $currentDate)
    || !is_null($article->publish_down) && ($article->publish_down < $currentDate);

if ($isUnpublished) {
    $icon = 'it-close-circle';
}
$aria_described = 'editarticle-' . (int) $article->id;

?>
<svg class="icon">
            <use xlink:href="<?php echo $iconsPath; ?>#<?php echo $icon; ?>"></use>
</svg>
<span title="<?php echo $tooltip; ?>"><?php echo Text::_('JGLOBAL_EDIT'); ?></span>
<div role="tooltip" class="visually-hidden" id="<?php echo $aria_described; ?>">
    <?php echo $tooltip; ?>
</div>
