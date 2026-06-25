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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Content\Administrator\Extension\ContentComponent;
use Joomla\Component\Content\Site\Helper\RouteHelper;


JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php'); 

// Create shortcuts to some parameters.
$params  = $this->item->params;
$canEdit = $params->get('access-edit');
$user    = Factory::getUser();
$info    = $params->get('info_block_position', 0);
$htag    = $this->params->get('show_page_heading') ? 'h2' : 'h1';

// Check if associations are implemented. If they are, define the parameter.
$assocParam        = (Associations::isEnabled() && $params->get('show_associations'));
$currentDate       = Factory::getDate()->format('Y-m-d H:i:s');
$isNotPublishedYet = $this->item->publish_up > $currentDate;
$isExpired         = !is_null($this->item->publish_down) && $this->item->publish_down < $currentDate;


// Template path
$app = Factory::getApplication();
$template   = $app->getTemplate();
$templatePath = JUri::base() . 'templates/' . $template;
$iconsPath = $templatePath . "/svg/sprites.svg";

require_once JPATH_THEMES . '/' . $template . '/functions.php';

$summary = findObjectValueByName($this->item->jcfields, "summary");

$reading_time = findObjectRawValueByName($this->item->jcfields, "reading-time");

if($reading_time && $reading_time == "hide") {
	$reading_time = false;
} else {
	$reading_time = true;
}


$actual_link = Uri::getInstance()->toString();
$website = Uri::getInstance()->getHost();

$upperlevel = 1;


$contenttext = $this->item->text;

for ($i = 6; $i >= 1; $i--) {
	$hstart = '<h'. $i;
	$hstartpos = strpos($contenttext, $hstart);

	$hend = '</h'. $i .'>';
	$hendpos = strpos($contenttext, $hend);

	if ($hstartpos !== false && $hendpos !== false)
    	$upperlevel++;
}

$newtext = $contenttext;

for ($i = 6; $i >= 1; $i--) {
  
  	//hstart
	$hstart = '<h'. $i;
	$lastPos = 0;
	$hstartpositions = array();
  	$replacement = '<h'. ($upperlevel);
  
    while (($lastPos = strpos($contenttext, $hstart, $lastPos))!== false) {
       $hstartpositions[] = $lastPos;
       $newtext = substr_replace($newtext, $replacement, $lastPos, strlen($hstart));
      
       $lastPos = $lastPos + strlen($hstart);
    }
  	
	$hend = '</h'. $i .'>';
	$lastPos = 0;
	$hendpositions = array();
  	$replacement = '</h'. ($upperlevel) .'>';
  
    while (($lastPos = strpos($contenttext, $hend, $lastPos))!== false) {
       $hendpositions[] = $lastPos;
       $newtext = substr_replace($newtext, $replacement, $lastPos, strlen($hend));
       $lastPos = $lastPos + strlen($hend);
    }
  
  	if(!empty($hstartpositions) || !empty($hendpositions)) {
    	$upperlevel--;
    }
}

$contenttext = $newtext;


/// PARAGRAPHS AND HEADINGS
//
//
//Create a new DOMDocument object.
$htmlDom = new DOMDocument;

//Load the HTML string into our DOMDocument object.
$htmlDom->loadHTML('<?xml encoding="utf-8" ?>' . $contenttext);

$containsForms = $htmlDom->getElementsByTagName('form')->length > 0;

$extractedHTags = [];
for ($i = 1; $i <= 6; $i++) { 
  $hTags = $htmlDom->getElementsByTagName('h'.$i);
  $extracted = [];
  
  foreach($hTags as $hTag){
    $hValue = trim($hTag->nodeValue);
    $extracted[] = $hValue;
  }
  
  $extractedHTags['h'.$i] = $extracted;
}


/// END PARAGRAPHS AND HEADINGS
?>

<div class="container px-4 item-article item-page-<?php echo $this->pageclass_sfx; ?>" itemscope itemtype="https://schema.org/Article">
<div class="row <?php if(!$params->get('show_publish_date') && !$params->get('show_create_date') && !$reading_time) { echo 'mb-5'; } ?>">
      <div class="col-lg-8 px-lg-4 py-lg-2">
        <meta itemprop="inLanguage" content="<?php echo ($this->item->language === '*') ? Factory::getApplication()->get('language') : $this->item->language; ?>">
			<?php if ($this->params->get('show_page_heading')) : ?>
				<h1> <?php echo $this->escape($this->params->get('page_heading')); ?> </h1>
			<?php endif;
			if (!empty($this->item->pagination) && !$this->item->paginationposition && $this->item->paginationrelative)
			{
				echo $this->item->pagination;
			}
			?>
      
      		
    		<?php if ($params->get('show_title')) : ?>
    		<div class="page-header">
               <?php echo '<' . $htag . ' itemprop="headline">' . $this->escape($this->item->title) . '</' . $htag . '>'; ?>
     		   
            <?php if ($this->item->state == ContentComponent::CONDITION_UNPUBLISHED) : ?>
      		      <span class="badge bg-warning text-light"><?php echo Text::_('JUNPUBLISHED'); ?></span>
     		   <?php endif; ?>
     		   <?php if ($isNotPublishedYet) : ?>
     		       <span class="badge bg-warning text-light"><?php echo Text::_('JNOTPUBLISHEDYET'); ?></span>
     		   <?php endif; ?>
     		   <?php if ($isExpired) : ?>
     		       <span class="badge bg-warning text-light"><?php echo Text::_('JEXPIRED'); ?></span>
      		  <?php endif; ?>
   			 </div>
    <?php endif; ?>
    <?php if ($canEdit) : ?>
        <?php echo LayoutHelper::render('joomla.content.icons', array('params' => $params, 'item' => $this->item)); ?>
    <?php endif; ?>

    <?php // Content is generated by content plugin event "onContentAfterTitle" ?>
    <?php echo $this->item->event->afterDisplayTitle; ?>
		<p><?php echo htmlspecialchars((string) $summary, ENT_QUOTES, 'UTF-8'); ?></p>
    
    	<?php if($params->get('show_publish_date') || $params->get('show_create_date') || $reading_time) { ?>
      <div class="row mt-5 mb-4">
        <?php if ($params->get('show_publish_date') || $params->get('show_create_date')) : ?>
          <div class="col-6">
      	  	<?php if ($params->get('show_publish_date')) : ?>
        	    <span class="data"></span>
       		   	<small>Data:</small>
          		<p class="font-weight-semibold text-monospace">
                <?php 
                	$dateToPrint = (new DateTime($this->item->publish_up));
                	echo printDateFmt($dateToPrint, "long");
					      ?>
              </p>
          	<?php endif; ?>
          	<?php if ($params->get('show_create_date')) : ?>
          		<small>Data:</small>
            	<p class="font-weight-semibold text-monospace">
                	<?php 
                		$dateToPrint = (new DateTime($this->item->created));
                		echo printDateFmt($dateToPrint, "long");
                	?>
              </p>
          	<?php endif; ?>
          </div>
        <?php endif; ?>
        <?php if ($reading_time) : ?>
          <div class="col-6">
            <small>Tempo di lettura:</small>
            <p class="font-weight-semibold"><?php echo estimateReadingTime($this->item->text) ?></p>
          </div>
        <?php endif; ?>
      </div>
      <?php }; ?>
    </div>
    <div class="col-lg-3 offset-lg-1 mb-4 mb-lg-0">
        
        <div class="dropdown d-inline">
              <button aria-label="condividi sui social" class="btn btn-dropdown btn-dropdown-social fw-normal dropdown-toggle d-inline-flex align-items-center fs-0" type="button" id="shareActions" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <svg class="icon" aria-hidden="true" aria-label="Condividi">
                  <use xlink:href="<?php echo $iconsPath; ?>#it-share"></use>
                  </svg>
                  <span>Condividi</span>
              </button>
              <div class="dropdown-menu shadow-lg" aria-labelledby="shareActions">
                <div class="link-list-wrapper">
                  <ul class="link-list" role="menu">
                    <li role="none">
                      <a class="list-item" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($actual_link); ?>" role="menuitem">
                        <svg class="icon" aria-hidden="true" aria-label="Icona Facebook">
                          <use xlink:href="<?php echo $iconsPath; ?>#it-facebook"></use>
                        </svg>
                        <span>Facebook</span>
                      </a>
                    </li>
                    <li role="none">
                      <a class="list-item" href="https://twitter.com/intent/tweet?text=<?php echo urlencode('Condivido questo URL'); ?>&url=<?php echo urlencode($actual_link); ?>" role="menuitem">
                        <svg class="icon" aria-hidden="true" aria-label="Icona Twitter">
                          <use xlink:href="<?php echo $iconsPath; ?>#it-twitter"></use>
                        </svg>
                        <span>Twitter</span>
                      </a>
                    </li>
                    <li role="none">
                      <a class="list-item" href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode($actual_link); ?>" role="menuitem">
                        <svg class="icon" aria-hidden="true" aria-label="Icona Linkedin">
                          <use xlink:href="<?php echo $iconsPath; ?>#it-linkedin"></use>
                        </svg>
                        <span>Linkedin</span>
                      </a>
                    </li>
                    <li role="none">
                      <a class="list-item" href="https://wa.me/?text=<?php echo urlencode($actual_link); ?>" role="menuitem">
                        <svg class="icon" aria-hidden="true" aria-label="Icona Whatsapp">
                          <use xlink:href="<?php echo $iconsPath; ?>#it-whatsapp"></use>
                        </svg>
                        <span>Whatsapp</span>
                      </a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="dropdown d-inline">
              <button aria-label="vedi azioni da compiere sulla pagina" class="btn btn-dropdown fw-normal dropdown-toggle d-inline-flex align-items-center fs-0 show" type="button" id="viewActions" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-focus-mouse="false">
                <svg class="icon" aria-hidden="true" aria-label="Icona altri elementi">
                  <use xlink:href="<?php echo $iconsPath; ?>#it-more-items"></use>
                </svg>
                <span>Vedi azioni</span>
              </button>
              <div class="dropdown-menu shadow-lg" aria-labelledby="viewActions" data-popper-placement="bottom-start" style="position: absolute; inset: 0px auto auto 0px; margin: 0px; transform: translate(0px, 34px);">
                <div class="link-list-wrapper">
                  <ul class="link-list" role="menu">
                    <li role="none">
                      <a class="list-item" href="#" onclick="window.print();return false;">
                        <svg class="icon" aria-hidden="true" aria-label="Icona stampante">
                          <use xlink:href="<?php echo $iconsPath; ?>#it-print"></use>
                        </svg>
                        <span>Stampa</span>
                      </a>
                    </li>
                    <li role="none">
                      <a class="list-item" role="menuitem" onclick="listenElements(this, '[data-audio]')">
                        <svg class="icon" aria-hidden="true" aria-label="Icona orecchio">
                          <use xlink:href="<?php echo $iconsPath; ?>#it-hearing"></use>
                        </svg>
                        <span>Ascolta</span>
                      </a>
                    </li>
                    <li role="none">
                      <a class="list-item" href="mailto:?subject=<?php echo urlencode($website); ?>&body=<?php echo urlencode($actual_link); ?>" role="menuitem">
                        <svg class="icon" aria-hidden="true" aria-label="Icona email">
                          <use xlink:href="<?php echo $iconsPath; ?>#it-mail"></use>
                        </svg>
                        <span>Invia</span>
                      </a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            
	<?php if ($params->get('show_tags', 1) && !empty($this->item->tags->itemTags)) : ?>
        <div class="mt-4 mb-4">
          <h2 class="h6"><small>Argomenti</small></h2>
			<?php $this->item->tagLayout = new FileLayout('joomla.content.tags'); ?>
			<?php echo $this->item->tagLayout->render($this->item->tags->itemTags); ?>
    	</div>
	<?php endif; ?>
        
        
      </div>
    </div>
    

<?php // Content is generated by content plugin event "onContentBeforeDisplay" ?>
<?php echo $this->item->event->beforeDisplayContent; ?>
<?php

if(json_decode($this->item->images)->image_fulltext) {
    ?>
	<div class="row row-full-width my-3">
  <?php echo LayoutHelper::render('joomla.content.full_image', $this->item); ?>
    </div>
	<?php
}

?>

<?php if ($params->get('access-view')) : ?>

<div class="row border-top row-column-border row-column-menu-left">
      <aside class="col-lg-4" id="indice-pagina">
        <div class="sticky-wrapper navbar-wrapper">


<nav class="navbar navbar-pagetoc it-navscroll-wrapper navbar-expand-lg it-top-navscroll">
  <button class="custom-navbar-toggler" type="button" aria-controls="navbarNavPageToc" aria-expanded="false" aria-label="<?php if(!empty($attachmentsFilesUrls)) { echo "Descrizione"; } else { echo "Contenuto";} ?>" data-target="#navbarNavPageToc">
	<span class="it-list fa-light fa-list"></span> <?php if(!empty($attachmentsFilesUrls)) { echo "Descrizione"; } else { echo "Contenuto";} ?>
  </button>
  <div class="navbar-collapsable" id="navbarNavPageToc">
    <div class="overlay"></div>
    <div class="close-div sr-only">
      <button class="btn close-menu" type="button">
		<span class="it-close"></span> Chiudi
      </button>
    </div>
    <a class="it-back-button" href="#">
        <svg class="icon icon-sm icon-primary align-top" aria-hidden="true" aria-label="Icona freccia a sinistra">
            <use xlink:href="<?php echo $iconsPath; ?>#it-chevron-left"></use>
        </svg>
        <span>Torna indietro</span>
	</a>
    <div class="menu-wrapper">
      <div class="link-list-wrapper menu-link-list">
        <h2 class="no_toc h3">Indice della pagina</h2>
        <ul class="link-list">
            
                 <?php

                //removed && !$containsForms
            		if(!empty($extractedHTags['h'.($upperlevel+1)]) ) {
                    	$firstt = true;
                    	//echo '<ul>';
                    		foreach($extractedHTags['h'.($upperlevel+1)] as $extractedHTag) {
                            	echo '<li class="nav-item '. ($firstt?'active':'') .'"><a class="nav-link '. ($firstt?'active':'') .'" href="#' . strClean($extractedHTag) . '"><span>' . htmlspecialchars((string) $extractedHTag, ENT_QUOTES, 'UTF-8') . '</span></a></li>';
                              $firstt=false;
                            }
                    	//echo '</ul>';
                    } else {
                    	echo '<li class="nav-item active"><a class="nav-link active" href="#contenuto"><span>';
                    		echo "Contenuto";
                        echo '</span></a></li>';
                    }
            	?>
             
             <li class="nav-item">
                  <a class="nav-link" href="#ulteriori-informazioni"><span>Ulteriori informazioni</span></a>
             </li>
        </ul>
      </div>
	<?php if (isset ($this->item->toc)) :
            echo $this->item->toc;
            endif; ?>
    </div>
  </div>
</nav>


                
                
        </div>
      </aside>
      <section class="col-lg-8 it-page-sections-container">
      <?php
		if (!empty($this->item->pagination) && $this->item->pagination && !$this->item->paginationposition && !$this->item->paginationrelative) :
			echo $this->item->pagination;
		endif;
		?>
      

         <?php 
        

                //removed && !$containsForms
if((empty($extractedHTags['h'.($upperlevel+1)]) || count($extractedHTags['h'.($upperlevel+1)]) === 0)) {

?>
        <article id="contenuto" class="it-page-section anchor-offset">
          <?php echo '<h'.($upperlevel+1).'>Contenuto</h'.($upperlevel+1).'>' ?>
          <div class="text-serif">
			<?php echo $contenttext; ?>
          </div>
        </article>
<?php
} else {
	
	$hTags = $htmlDom->getElementsByTagName('h'.($upperlevel+1));
  
	foreach($hTags as $hTag){
		$hempty= $htmlDom->createElement('h'.($upperlevel+1));
    	$hTag->parentNode->replaceChild($hempty, $hTag);
	}

	$contenttext=$htmlDom->saveHTML();

	$contenttext=str_replace('<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<?xml encoding="utf-8" ?><html><body>', "", $contenttext);
	$contenttext=str_replace('</body></html>', "", $contenttext);

	$ctpieces = explode("<h".($upperlevel+1)."></h".($upperlevel+1).">", $contenttext);

	$index = 0;
	$hindex = 0;
  
	if(!str_starts_with($contenttext, '<h'.($upperlevel+1).'></h'.($upperlevel+1).'>')) {
    	?>
    	<article id="contenuto" class="it-page-section anchor-offset">
          <div class="text-serif">
			<?php echo $ctpieces[0]; ?>
          </div>
        </article>
        <?php
    };

        $index++;

	for (; $index <= count($ctpieces)-1; $index++) {
    	?>
        <article id="<?php echo strClean($extractedHTags['h'.($upperlevel+1)][$hindex]) ?>" class="it-page-section anchor-offset">
          
          <?php echo '<h'.($upperlevel+1).'>'.htmlspecialchars((string) $extractedHTags['h'.($upperlevel+1)][$hindex], ENT_QUOTES, 'UTF-8').'</h'.($upperlevel+1).'>' ?>
          
          <div class="text-serif">
			<?php echo $ctpieces[$index]; ?>
          </div>
        </article>
        <?php
        $hindex++;
    }
}

?>
        <?php echo JHtml::_('content.prepare', '{loadposition inside-content}'); ?>

        <article id="ulteriori-informazioni" class="it-page-section anchor-offset mt-5">
          <?php echo '<h'.($upperlevel+1).' class="mb-3">Ulteriori informazioni</h'.($upperlevel+1).'>' ?>
        
        <?php if ($params->get('show_modify_date')) : ?>
          		<p class="text-serif mt-5">Ultimo aggiornamento</p>
            	<p class="font-weight-semibold text-monospace">
                	<?php 
                		$dateToPrint = (new DateTime($this->item->modified));
                		echo printDateFmt($dateToPrint, "long");
                	?>
                </p>
          	<?php endif; ?>
        </article>
      
      
	<?php
	if (!empty($this->item->pagination) && $this->item->paginationposition && !$this->item->paginationrelative) :
		echo $this->item->pagination;
	?>
	<?php endif; ?>
	<?php if ((int) $params->get('urls_position', 0) === 1) : ?>
	<?php echo $this->loadTemplate('links'); ?>
	<?php endif; ?>
      </section>
    </div>


	<?php // Optional teaser intro text for guests ?>
	<?php elseif ($params->get('show_noauth') == true && $user->get('guest')) : ?>
	<?php echo LayoutHelper::render('joomla.content.intro_image', $this->item); ?>
	<?php echo HTMLHelper::_('content.prepare', $this->item->introtext); ?>
	<?php // Optional link to let them register to see the whole article. ?>
	<?php if ($params->get('show_readmore') && $this->item->fulltext != null) : ?>
	<?php $menu = Factory::getApplication()->getMenu(); ?>
	<?php $active = $menu->getActive(); ?>
	<?php $itemId = $active->id; ?>
	<?php $link = new Uri(Route::_('index.php?option=com_users&view=login&Itemid=' . $itemId, false)); ?>
	<?php $link->setVar('return', base64_encode(RouteHelper::getArticleRoute($this->item->slug, $this->item->catid, $this->item->language))); ?>
	<?php echo LayoutHelper::render('joomla.content.readmore', array('item' => $this->item, 'params' => $params, 'link' => $link)); ?>
	<?php endif; ?>
	<?php endif; ?>
	<?php
	if (!empty($this->item->pagination) && $this->item->paginationposition && $this->item->paginationrelative) :
		echo $this->item->pagination;
	?>
	<?php endif; ?>
	<?php // Content is generated by content plugin event "onContentAfterDisplay" ?>
	<?php echo $this->item->event->afterDisplayContent; ?>


</div>
