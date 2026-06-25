<?php

/**
 * @package     Joomla.Site
 * @subpackage  Templates.cassiopeia
 *
 * @copyright   (C) 2020 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;

$module  = $displayData['module'];
$params  = $displayData['params'];
$attribs = $displayData['attribs'];

if ($module->content === null || $module->content === '') {
    return;
}

$moduleTag              = $params->get('module_tag', 'div');
$moduleAttribs          = [];
$moduleAttribs['class'] = $module->position . htmlspecialchars($params->get('moduleclass_sfx', ''), ENT_QUOTES, 'UTF-8');
$headerTag              = htmlspecialchars($params->get('header_tag', 'h3'), ENT_QUOTES, 'UTF-8');
$bootstrapSize              = htmlspecialchars($params->get('bootstrap_size', '12'), ENT_QUOTES, 'UTF-8');
$headerClass            = htmlspecialchars($params->get('header_class', ''), ENT_QUOTES, 'UTF-8');
$headerAttribs          = [];
$headerAttribs['class'] = $headerClass;
$backgroundImage = $params->get('backgroundimage');

// Only add aria if the moduleTag is not a div
if ($moduleTag !== 'div') {
    if ($module->showtitle) :
        $moduleAttribs['aria-labelledby'] = 'mod-' . $module->id;
        $headerAttribs['id']              = 'mod-' . $module->id;
    else :
        $moduleAttribs['aria-label'] = $module->title;
    endif;
}

$header = '<' . $headerTag . ' ' . ArrayHelper::toString($headerAttribs) . '>' . $module->title . '</' . $headerTag . '>';

?>
<section class="section section-image bg-dark text-center  <?php echo $moduleAttribs['class']; ?>"
<?php if($params->get('backgroundimage')) { echo ' style="background-image:url('. HTMLHelper::_('cleanImageURL', $params->get('backgroundimage'))->url . ')"'; }?> aria-label="<?php echo $module->title; ?>" id="simage-<?php echo $module->id; ?>">
	<div class="section-content">
    	<div class="container white-color">
			<<?php echo $moduleTag; ?>>
				<?php if ($module->showtitle) : ?>
					<?php echo $header; ?>
				<?php endif; ?>
				<div>
					<?php echo $module->content; ?>
				</div>
			</<?php echo $moduleTag; ?>>
		</div>
    </div>
</section>