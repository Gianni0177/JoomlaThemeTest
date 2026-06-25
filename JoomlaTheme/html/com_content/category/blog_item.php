<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   (C) 2006 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Categories\Categories;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Content\Administrator\Extension\ContentComponent;
use Joomla\Component\Content\Site\Helper\RouteHelper;

JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');

// Template path
$app = Factory::getApplication();
$template   = $app->getTemplate();
$templatePath = JUri::base() . '/templates/' . $template;
$iconsPath = $templatePath . "/svg/sprites.svg";

$domainurl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/";
$themeDir = str_replace($domainurl, "", $templatePath);

include_once $_SERVER['DOCUMENT_ROOT']  . '/' .  $themeDir . '/functions.php';

// Create a shortcut for params.
$params = $this->item->params;
$canEdit = $this->item->params->get('access-edit');
$info    = $params->get('info_block_position', 0);

// Check if associations are implemented. If they are, define the parameter.
$assocParam = (Associations::isEnabled() && $params->get('show_associations'));

$currentDate   = Factory::getDate()->format('Y-m-d H:i:s');
$isUnpublished = ($this->item->state == ContentComponent::CONDITION_UNPUBLISHED || $this->item->publish_up > $currentDate)
	|| ($this->item->publish_down < $currentDate && $this->item->publish_down !== null);


$jcategories = Categories::getInstance('Content');
$category   = $jcategories->get($this->item->catid);
$currentCatFields = FieldsHelper::getFields('com_content.categories', $category, true);
$catIconId = findObjectValueByName($currentCatFields, "icon-class");


$summary = findObjectValueByName($this->item->jcfields, "summary");
$datestart = (findObjectByName($this->item->jcfields, "datestart")) ? findObjectByName($this->item->jcfields, "datestart")->value : null;
?>

<?php
	$articlecol =  ($this->leaditem && $this->pagination->pagesCurrent <= 1)
                ? "col-12 col-sm-6 col-lg-8 blog-item d-flex align-items-stretch card-wrapper card-space"
				: "col-12 col-sm-6 col-lg-4 blog-item d-flex align-items-stretch card-wrapper card-space";
?>
        
<article class="<?php echo $articlecol; ?>" itemprop="blogPost" itemscope itemtype="https://schema.org/BlogPosting">
	<div class="card card-bg card-big rounded shadow <?php if($isUnpublished) { ?>border border-warning bg-light<?php }?>">
			<?php if(json_decode($this->item->images, true)["image_intro"]) {?>
				<div class="img-responsive-wrapper">
                    <div class="img-responsive img-responsive-panoramic">
						<?php echo LayoutHelper::render('joomla.content.intro_image', $this->item); ?>
                    </div>
                  </div>
			<?php } ?>
         <?php
				if ( ($this->params->get('link_intro_image') || ($this->leaditem && $this->pagination->pagesCurrent <= 1)) && $introimg) { ?>
        		<div class="img-responsive-wrapper">
        	  <div class="img-responsive img-responsive-panoramic">
         	   <figure class="img-wrapper">
        			<?php echo $introimg ?>
        	    </figure>
                <?php
                if(!empty($datestart)) {
                	$dateToPrint = (new DateTime($datestart));
    				echo '<div class="card-calendar d-flex flex-column justify-content-center">';
    				echo '<span class="card-date">'. ($dateToPrint->format('d')) .'</span>';
    				echo '<span class="card-day">' . printDateFmt($dateToPrint, "month") .'</span>';
                	echo '</div>';
    			}
                ?>
        	  </div>
        	</div>
        <?php } ?>
        
		<div class="position-relative">
              <?php 
        		if(!($this->leaditem && $this->pagination->pagesCurrent <= 1)) {
               		if($this->item->featured) { echo '<div class="flag-icon"></div>'; } 
                	else if($this->params->get('show_category')) { echo '<div class="flag-icon invisible"></div>'; };
                } else if($this->params->get('show_category')) { echo '<div class="flag-icon invisible"></div>'; };
			  ?>  
                
              <?php if ($this->params->get('show_category')) {  ?>
              	<div class="etichetta">
              	   <?php if($catIconId !== "") {
              	 echo '<span class="icon fa-2x ' . $catIconId . '"></span>';
         		 }
        		  ?>
                
                  <span><?php echo $this->escape($this->item->category_title); ?></span>
                </div>
              <?php }?>
		</div>
              
        <div class="card-body">
			<h3 class="card-title h5"><?php echo $this->item->title; ?></h3>
			
            <?php if ($canEdit) : ?>
            	<?php echo LayoutHelper::render('joomla.content.icons', array('params' => $params, 'item' => $this->item)); ?>
            <?php endif; 
                
			if($summary !== "") { echo '<p class="card-text">' . $summary  . '</p>'; }
                
            if ($params->get('access-view')) :
				$link = Route::_(RouteHelper::getArticleRoute($this->item->slug, $this->item->catid, $this->item->language));
			else :
				$menu = Factory::getApplication()->getMenu();
				$active = $menu->getActive();
				$itemId = $active->id;
				$link = new Uri(Route::_('index.php?option=com_users&view=login&Itemid=' . $itemId, false));
				$link->setVar('return', base64_encode(RouteHelper::getArticleRoute($this->item->slug, $this->item->catid, $this->item->language)));
			endif;
            ?>
            
            <a class="read-more" href="<?php echo $link; ?>">
            	<span class="text">Leggi di pi&ugrave;</span>
            	<svg class="icon" aria-label="Freccia verso destra">
            		<use xlink:href="<?php echo $iconsPath; ?>#it-arrow-right"></use>
            	</svg>
            </a>
		</div>
            
		<?php if ($params->get('show_readmore') && $this->item->readmore) :
			if ($params->get('access-view')) :
				$link = Route::_(RouteHelper::getArticleRoute($this->item->slug, $this->item->catid, $this->item->language));
			else :
				$menu = Factory::getApplication()->getMenu();
				$active = $menu->getActive();
				$itemId = $active->id;
				$link = new Uri(Route::_('index.php?option=com_users&view=login&Itemid=' . $itemId, false));
				$link->setVar('return', base64_encode(RouteHelper::getArticleRoute($this->item->slug, $this->item->catid, $this->item->language)));
			endif; ?>

			<?php echo LayoutHelper::render('joomla.content.readmore', array('item' => $this->item, 'params' => $params, 'link' => $link)); ?>
		<?php endif; ?>
	</div>
</article>
	<?php // Content is generated by content plugin event "onContentAfterDisplay" ?>
	<?php echo $this->item->event->afterDisplayContent; ?>
