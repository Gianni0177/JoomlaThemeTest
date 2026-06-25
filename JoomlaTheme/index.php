<?php

/**
 * @package     Joomla.Site
 * @subpackage  Templates.cassiopeia
 *
 * @copyright   (C) 2017 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

$themeDir = dirname(__FILE__);
require_once $themeDir . DIRECTORY_SEPARATOR . 'functions.php';

/** @var Joomla\CMS\Document\HtmlDocument $this */

$app   = Factory::getApplication();
$input = $app->getInput();
$wa    = $this->getWebAssetManager();
$user = Factory::getUser();
$sitemenu = $app->getMenu();

// Template path
$template   = $app->getTemplate();
$templatePath = JUri::base() . '/templates/' . $template;
$iconsPath = $templatePath . "/svg/sprites.svg";

$headerTopLight = $this->params->get('headerTopLight');
$headerLight = $this->params->get('headerLight');
$headerNavLight = $this->params->get('headerNavLight');

$sanitizeExternalUrl = static function ($url): string {
  $url = trim((string) $url);

  if ($url === '') {
    return '';
  }

  if (str_starts_with($url, '/') && !str_starts_with($url, '//')) {
    return htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
  }

  $validatedUrl = filter_var($url, FILTER_VALIDATE_URL);

  if ($validatedUrl === false) {
    return '';
  }

  $scheme = strtolower((string) parse_url($validatedUrl, PHP_URL_SCHEME));

  if (!in_array($scheme, ['http', 'https'], true)) {
    return '';
  }

  return htmlspecialchars($validatedUrl, ENT_QUOTES, 'UTF-8');
};

$sanitizeCssClasses = static function ($classes): string {
  $classes = preg_replace('/[^a-zA-Z0-9 _-]/', '', (string) $classes);

  return trim(preg_replace('/\s+/', ' ', $classes ?? ''));
};

$parentCorporationUrl = $sanitizeExternalUrl($this->params->get('parentcorporationUrl'));
$socialLinks = [
  'facebook' => $sanitizeExternalUrl($this->params->get('social-facebook')),
  'instagram' => $sanitizeExternalUrl($this->params->get('social-instagram')),
  'twitter' => $sanitizeExternalUrl($this->params->get('social-twitter')),
  'youtube' => $sanitizeExternalUrl($this->params->get('social-youtube')),
  'telegram' => $sanitizeExternalUrl($this->params->get('social-telegram')),
  'whatsapp' => $sanitizeExternalUrl($this->params->get('social-whatsapp')),
  'linkedin' => $sanitizeExternalUrl($this->params->get('social-linkedin')),
  'newsletter' => $sanitizeExternalUrl($this->params->get('social-newsletter')),
];
$siteFaIconClass = $sanitizeCssClasses($this->params->get('siteFaIconClass', ''));

// Browsers support SVG favicons
if($this->params->get('faviconFile')) {
  $faviconfile = explode("#", $this->params->get('faviconFile'))[0];
  
  if(pathinfo($faviconfile, PATHINFO_EXTENSION) == "svg"){
    $this->addHeadLink(HTMLHelper::_('image', JUri::base() . $faviconfile, '', [], true, 1), 'icon', 'rel', ['type' => 'image/svg+xml']);
  } else {
    $this->addHeadLink(HTMLHelper::_('image', JUri::base() . $faviconfile, '', [], true, 1), 'icon', 'rel', ['type' => 'image/x-icon']);
  }
}


// Detecting Active Variables
$option   = $input->getCmd('option', '');
$view     = $input->getCmd('view', '');
$layout   = $input->getCmd('layout', '');
$task     = $input->getCmd('task', '');
$itemid   = $input->getCmd('Itemid', '');
$sitename = htmlspecialchars($app->get('sitename'), ENT_QUOTES, 'UTF-8');
$menu     = $app->getMenu()->getActive();
$pageclass = $menu !== null ? $menu->getParams()->get('pageclass_sfx', '') : '';


// Enable assets
$wa->usePreset('rbpat');

$this->setMetaData('viewport', 'width=device-width, initial-scale=1');

?>
<!doctype html>
<html lang="it" dir="<?php echo $this->direction; ?>">
  <head>
	<jdoc:include type="metas" />
	<jdoc:include type="styles" />
	<jdoc:include type="scripts" />
  
  </head>
  <body>

  <div class="skiplinks">
    <a class="visually-hidden-focusable" href="#maincontent">Vai al contenuto principale</a>
	  <?php if ($this->countModules('menu', true)) { ?><a class="visually-hidden-focusable" href="#header-nav-wrapper">Vai al menu di navigazione</a><?php } ?>
    <a class="visually-hidden-focusable" href="#footer">Vai al piede di pagina</a>
  </div>

  <header aria-label="Intestazione" class="it-header-wrapper it-header-sticky <?php if($headerLight) { echo "it-shadow"; } ?>"  data-bs-toggle="sticky"  data-bs-position-type="fixed" data-bs-sticky-class-name="is-sticky" data-bs-target="#header-nav-wrapper">
  <?php if ($this->params->get('parentcorporation', 1) || $this->params->get('headerasidearea', 1)) { ?>
    <div class="it-header-slim-wrapper <?php if($headerTopLight) { echo "theme-light"; } ?>">
      <div class="container">
        <div class="row">
          <div class="col-12">
            <div class="it-header-slim-wrapper-content">
              <?php if ($this->params->get('parentcorporation', 1) && $this->params->get('parentcorporationDescription') && $parentCorporationUrl) : ?>
                <a class="d-lg-block navbar-brand" href="<?php echo $parentCorporationUrl; ?>"><?php echo htmlspecialchars($this->params->get('parentcorporationDescription'), ENT_QUOTES, 'UTF-8'); ?></a>
              <?php endif; ?>
            
              <?php
                if ($user->id != 0) {
                  ?>
              			<div class="usermenu-dropdown text-center">
                            <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                           		 <svg class="icon" aria-hidden="true">
                              		<use href="<?php echo $iconsPath; ?>#it-user"></use>
                            		</svg>
                              
                              Area personale
                              
                           		 <svg class="icon-expand icon icon-sm" aria-hidden="true">
                              		<use href="<?php echo $iconsPath; ?>#it-expand"></use>
                            		</svg>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                              <div class="link-list-wrapper">
                                <?php if ($this->countModules('user-menu', true)) { ?>
                                	<jdoc:include type="modules" name="user-menu" style="none" />
                                <?php } else { ?>
                                  <ul class="link-list">
                                    <li><?php
                                          $userToken = JSession::getFormToken();
                                          echo '<a class="dropdown-item list-item" href="index.php?option=com_users&task=user.logout&' . $userToken . '=1" title="Effettua logout"><span>Esci</span></a>';
                                    ?></li>
                                  </ul>
                                <?php } ?>
                              </div>
                            </div>
                          </div>
              		<?php
                } else if ($this->params->get('headerasidearea', 1)) : ?>
                <div class="it-header-slim-right-zone">
                  <div class="it-access-top-wrapper">
                    <?php                                                                             
                      $asideareaitemid = $this->params->get('headerasideareaMenuItemId');

                      if($asideareaitemid) {
                        $menuitem = $sitemenu->getItem($asideareaitemid);
                        ?><a class="btn btn-primary btn-sm" href="<?php echo htmlspecialchars($menuitem->link, ENT_QUOTES, 'UTF-8');?>"><?php echo htmlspecialchars($this->params->get('headerasideareaLabel'), ENT_QUOTES, 'UTF-8'); ?></a><?php
                      }
                    ?>
                  </div>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
 <?php } ?>
  <div class="it-nav-wrapper">
    <div class="it-header-center-wrapper <?php if($headerLight) { echo "theme-light"; } ?>">
      <div class="container">
        <div class="row">
          <div class="col-12">
            <div class="it-header-center-content-wrapper">
              <div class="it-brand-wrapper">
                <a href="<?php echo $this->baseurl; ?>/">
                <?php
                  if($this->params->get('brand')) {
                      if($this->params->get('logoFile')) {
                          $logofile = explode("#", $this->params->get('logoFile'))[0];
                          
                          if(pathinfo($logofile, PATHINFO_EXTENSION) == "svg"){
                            ?>
                              <svg class="icon" aria-hidden="true" aria-label="Logo dell'Ente">
                                <image href="<?php echo JUri::base() . $logofile; ?>" x="0" y="0" height="100%" width="100%"></image>
                              </svg>
                            <?php
                          } else {
                            ?>
                                <img class="icon" src="<?php echo JUri::base() . $logofile; ?>" alt="Logo <?php echo $this->params->get('siteTitle')?htmlspecialchars($this->params->get('siteTitle'), ENT_COMPAT, 'UTF-8'):""; ?>" />
                            <?php
                          }
                      } else {
                          ?>
                            <svg class="icon" aria-hidden="true">
                              <use href="<?php echo $iconsPath; ?>#it-pa"></use>
                            </svg>
                          <?php
                      }
                  }
                ?>
                <div class="it-brand-text">
                  <div class="it-brand-title"><?php echo $this->params->get('siteTitle')?htmlspecialchars($this->params->get('siteTitle'), ENT_COMPAT, 'UTF-8'):"" ?></div>
                  <div class="it-brand-tagline d-none d-md-block"><?php echo $this->params->get('siteDescription')?htmlspecialchars($this->params->get('siteDescription')):""; ?></div>
                </div>
                </a>
              </div>
              <?php if ($this->params->get('headerSearchMenuItemId') || $this->params->get('socialarea')) { ?>
              <div class="it-right-zone">
                  <?php
                    if($this->params->get('socialarea')) {
                        if($socialLinks['facebook'] || $socialLinks['instagram'] || $socialLinks['twitter']  || $socialLinks['telegram'] || $socialLinks['whatsapp'] || $socialLinks['youtube'] || $socialLinks['linkedin'] || $socialLinks['newsletter']) {
                        ?>
                          <div class="it-socials d-none d-md-flex">
                            <span>Seguici su</span>
                            <ul>
                              <?php if($socialLinks['facebook']) {?>
                              <li>
                                <a href="<?php echo $socialLinks['facebook']; ?>" aria-label="Facebook" target="_blank" rel="noopener noreferrer">
                                  <svg class="icon" aria-hidden="true" aria-label="Icona Facebook">
                                    <use href="<?php echo $iconsPath; ?>#it-facebook"></use>
                                  </svg>
                                </a>
                              </li>
                              <?php
                              }
                              if($socialLinks['instagram']) {
                              ?>
                              <li>
                                <a href="<?php echo $socialLinks['instagram']; ?>" aria-label="Instagram" target="_blank" rel="noopener noreferrer">
                                  <svg class="icon" aria-hidden="true" aria-label="Icona Instagram">
                                    <use href="<?php echo $iconsPath; ?>#it-instagram"></use>
                                  </svg>
                                </a>
                              </li>
                              <?php
                              }
                              if($socialLinks['twitter']) {
                              ?>
                              <li>
                                <a href="<?php echo $socialLinks['twitter']; ?>" aria-label="Twitter" target="_blank" rel="noopener noreferrer">
                                  <svg class="icon" aria-hidden="true" aria-label="Icona Twitter">
                                    <use href="<?php echo $iconsPath; ?>#it-twitter"></use>
                                  </svg>
                                </a>
                              </li>
                              <?php
                              }
                              if($socialLinks['youtube']) {
                              ?>
                              <li>
                                <a href="<?php echo $socialLinks['youtube']; ?>" aria-label="YouTube" target="_blank" rel="noopener noreferrer">
                                  <svg class="icon" aria-hidden="true" aria-label="Icona YouTube">
                                    <use href="<?php echo $iconsPath; ?>#it-youtube"></use>
                                  </svg>
                                </a>
                              </li>
                              <?php
                              }
                              if($socialLinks['telegram']) {
                              ?>
                              <li>
                                <a href="<?php echo $socialLinks['telegram']; ?>" aria-label="YouTube" target="_blank" rel="noopener noreferrer">
                                  <svg class="icon" aria-hidden="true" aria-label="Icona Telegram">
                                    <use href="<?php echo $iconsPath; ?>#it-telegram"></use>
                                  </svg>
                                </a>
                              </li>
                              <?php
                              }
                              if($socialLinks['whatsapp']) {
                              ?>
                              <li>
                                <a href="<?php echo $socialLinks['whatsapp']; ?>" aria-label="YouTube" target="_blank" rel="noopener noreferrer">
                                  <svg class="icon" aria-hidden="true" aria-label="Icona Whatsapp">
                                    <use href="<?php echo $iconsPath; ?>#it-whatsapp"></use>
                                  </svg>
                                </a>
                              </li>
                              <?php
                              }
                              if($socialLinks['linkedin']) {
                              ?>
                              <li>
                                <a href="<?php echo $socialLinks['linkedin']; ?>" aria-label="Linkedin" target="_blank" rel="noopener noreferrer">
                                  <svg class="icon" aria-hidden="true" aria-label="Icona Linkedin">
                                    <use href="<?php echo $iconsPath; ?>#it-linkedin"></use>
                                  </svg>
                                </a>
                              </li>
                              <?php
                              }
							  if($socialLinks['newsletter']) {
                              ?>
                              <li>
                                <a href="<?php echo $socialLinks['newsletter']; ?>" aria-label="Newsletter" target="_blank" rel="noopener noreferrer">
                                  <svg class="icon" aria-hidden="true" aria-label="Icona Newsletter">
                                    <use href="<?php echo $iconsPath; ?>#it-mail"></use>
                                  </svg>
                                </a>
                              </li>
                              <?php
                              }
                              ?>
                            </ul>
                          </div>
                        <?php
                        }
                    }
                  ?>
                <?php 
                    $searchitemid = $this->params->get('headerSearchMenuItemId');

                    if($searchitemid) {
                      $searchmenuitem = $sitemenu->getItem($searchitemid);
                      ?>
                      <div class="it-search-wrapper">
                        <span class="d-none d-md-block">Cerca</span>
                        <a class="search-link rounded-icon" aria-label="Cerca nel sito" href="<?php echo htmlspecialchars($searchmenuitem->link, ENT_QUOTES, 'UTF-8');?>">
                          <svg class="icon" aria-hidden="true" aria-label="Lente di ingrandimento">
                            <use href="<?php echo $iconsPath; ?>#it-search"></use>
                          </svg>
                        </a>
                      </div>
                      <?php
                    }
                ?>
              </div>
              <?php }?>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php if ($this->countModules('menu', true)) : ?>
      <div class="it-header-navbar-wrapper <?php if($headerNavLight) { echo "theme-light-desk"; } ?>" id="header-nav-wrapper">
      <div class="container">
        <div class="row">
          <div class="col-12">
            <!--start nav-->
            <nav class="navbar navbar-expand-lg has-megamenu" aria-label="Navigazione principale">
              <button class="custom-navbar-toggler" type="button" aria-controls="nav4" aria-expanded="false" aria-label="Mostra/Nascondi la navigazione" data-bs-target="#nav4" data-bs-toggle="navbarcollapsible">
                <svg class="icon" aria-hidden="true" aria-label="Mostra/Nascondi la navigazione">
                  <use href="<?php echo $iconsPath; ?>#it-burger"></use>
                </svg>
              </button>
              <div class="navbar-collapsable" id="nav4" style="display: none">
                <div class="overlay" style="display: none"></div>
                <div class="close-div">
                  <button class="btn close-menu" type="button" title="Nascondi la navigazione">
                    <svg class="icon" aria-label="Chiudi">
                      <use href="<?php echo $iconsPath; ?>#it-close-big"></use>
                    </svg>
                  </button>
                </div>
                <div class="menu-wrapper">
                  <jdoc:include type="modules" name="menu" style="none" />
                </div>
              </div>
            </nav>
          </div>
        </div>
      </div>
    </div>
    <?php endif ?>
  </div>
</header>
  
		<?php if ($this->countModules('breadcrumbs', true)) : ?>
  		<div class="container px-4 mt-4">
			  <jdoc:include type="modules" name="breadcrumbs" style="none" />
  		</div>
		<?php endif; ?>
  
		<?php if ($this->countModules('banner', true)) : ?>
			<div class="container-banner full-width">
				<jdoc:include type="modules" name="banner" style="none" />
			</div>
		<?php endif; ?>

		<?php if ($this->countModules('top-a', true)) : ?>
		<div class="grid-child container-top-a">
			<jdoc:include type="modules" name="top-a" style="card" />
		</div>
		<?php endif; ?>

		<?php if ($this->countModules('top-b', true)) : ?>
		<div class="grid-child container-top-b">
			<jdoc:include type="modules" name="top-b" style="card" />
		</div>
		<?php endif; ?>

		<?php if ($this->countModules('sidebar-left', true)) : ?>
		<div class="grid-child container-sidebar-left">
			<jdoc:include type="modules" name="sidebar-left" style="card" />
		</div>
		<?php endif; ?>

		<div class="grid-child container-component" id="maincontent">
			<jdoc:include type="modules" name="main-top" style="card" />
			<jdoc:include type="message" />
			<main role="main" aria-label="Contenuto principale">
			<jdoc:include type="component" />
			</main>
			<jdoc:include type="modules" name="main-bottom" style="card" />
		</div>

		<?php if ($this->countModules('sidebar-right', true)) : ?>
		<div class="grid-child container-sidebar-right">
			<jdoc:include type="modules" name="sidebar-right" style="card" />
		</div>
		<?php endif; ?>

    <section id="other-contents">
      
			<?php if ($this->countModules('thematic-area-1', true)) : ?>
			  <jdoc:include type="modules" name="thematic-area-1" style="card" />
			<?php endif; ?>

			<?php if ($this->countModules('thematic-area-2', true)) : ?>
				<jdoc:include type="modules" name="thematic-area-2" style="card" />
			<?php endif; ?>

      
  		<?php if ($this->countModules('container-thematic-area', true) || $this->countModules('container-thematic-area-row', true)) : ?>
        <div class="container">
          <?php if ($this->countModules('container-thematic-area', true)) : ?>
            <jdoc:include type="modules" name="container-thematic-area" style="card" />
          <?php endif; ?>

          <?php if ($this->countModules('container-thematic-area-row', true)) : ?> 
            <div class="row row-cols-2 mt-5">
              <jdoc:include type="modules" name="container-thematic-area-row" style="card" />
            </div>
          <?php endif; ?>
        </div>   
      <?php endif; ?>

    </section>

    <?php if ($this->countModules('bottom-a', true)) : ?>
			<div class="grid-child container-bottom-a">
				<jdoc:include type="modules" name="bottom-a" style="card" />
			</div>
		<?php endif; ?>
  
  	<div class="section section-muted pb-5 pt-0">
			<?php if ($this->countModules('bottom-b', true)) : ?>
			  <div class="grid-child container-bottom-b">
				  <jdoc:include type="modules" name="bottom-b" style="card" />
			  </div>
			<?php endif; ?>
  	</div>

    <?php if ($this->countModules('feedback', true)) : ?>
      <section id="feedback" class="mt-3 bg-dark py-3 text-white">
			  <div class="container">
				  <jdoc:include type="modules" name="feedback" />
			  </div>
      </section>
			<?php endif; ?>
    
<footer id="footer" class="it-footer" aria-label="Piè di pagina">
  <div class="it-footer-main">
    <div class="container">
      <section>
        <div class="row clearfix">
          <div class="col-sm-12">
            <div class="it-brand-wrapper">
              <a href="<?php echo $this->baseurl; ?>/">
            	<?php if ($siteFaIconClass === '') { ?>
                	<svg class="icon me-4" aria-label="Edificio">
                  		<use xlink:href="<?php echo $iconsPath; ?>#it-pa"></use>
                	</svg>
                <?php } else { ?>
                	<span class="icon me-4 fa-3x <?php echo $siteFaIconClass; ?>"></span>
                <?php } ?>
                
                <div class="it-brand-text">
                  <h2 class="no_toc"><?php echo htmlspecialchars($this->params->get('siteTitle'), ENT_COMPAT, 'UTF-8') ?></h2>
                  <?php if($this->params->get('siteDescription')) { ?><h3 class="no_toc d-none d-md-block">
                    <?php echo htmlspecialchars($this->params->get('siteDescription')); ?>
                  </h3><?php }?>
                </div>
              </a>
            </div>
          </div>
        </div>
      </section>
                  
    <jdoc:include type="modules" name="footer" />

    <?php if ($this->countModules('footer-0', true)
        ||$this->countModules('footer-1', true)
        ||$this->countModules('footer-2', true)
        ||$this->countModules('footer-3', true)
        ||$this->countModules('footer-4', true)
        ||$this->countModules('footer-5', true)
        ||$this->countModules('footer-6', true)
        ) : ?>	
    
      <section>
        
        <?php if ($this->countModules('footer-0', true)) : ?>	
            <div class="row">
              <jdoc:include type="modules" name="footer-0" />
            </div>
        <?php endif; ?>  

        <?php if ($this->countModules('footer-1', true)
              ||$this->countModules('footer-2', true)
              ||$this->countModules('footer-3', true)) : ?>	
              <div class="row">
                <?php if ($this->countModules('footer-1', true)) : ?>	
                      <div class="col-md-6 col-lg-4">
                        <jdoc:include type="modules" name="footer-1" />
                      </div>
                <?php endif; ?>
                <?php if ($this->countModules('footer-2', true)) : ?>	
                      <div class="col-md-6 col-lg-4">
                        <jdoc:include type="modules" name="footer-2" />
                      </div>
                <?php endif; ?>
                <?php if ($this->countModules('footer-3', true)) : ?>	
                      <div class="col-md-6 col-lg-4">
                        <jdoc:include type="modules" name="footer-3" />
                      </div>
                <?php endif; ?>
              </div>
        <?php endif; ?>  

        <?php if ($this->countModules('footer-4', true)
              ||$this->countModules('footer-5', true)
              ||$this->countModules('footer-6', true)) : ?>	
              <div class="row">
                <?php if ($this->countModules('footer-4', true)) : ?>	
                      <div class="col-md-6 col-lg-4">
                        <jdoc:include type="modules" name="footer-4" />
                      </div>
                <?php endif; ?>
                <?php if ($this->countModules('footer-5', true)) : ?>	
                      <div class="col-md-6 col-lg-4">
                        <jdoc:include type="modules" name="footer-5" />
                      </div>
                <?php endif; ?>
                <?php if ($this->countModules('footer-6', true)) : ?>	
                      <div class="col-md-6 col-lg-4">
                        <jdoc:include type="modules" name="footer-6" />
                      </div>
                <?php endif; ?>
              </div>
        <?php endif; ?>  
      
      
        <?php if ($this->countModules('footer-7', true)
              ||$this->countModules('footer-8', true)
              ||$this->countModules('footer-9', true)) : ?>	
              <div class="row">
                <?php if ($this->countModules('footer-7', true)) : ?>	
                      <div class="col-md-6 col-lg-4">
                        <jdoc:include type="modules" name="footer-7" />
                      </div>
                <?php endif; ?>
                <?php if ($this->countModules('footer-8', true)) : ?>	
                      <div class="col-md-6 col-lg-4">
                        <jdoc:include type="modules" name="footer-8" />
                      </div>
                <?php endif; ?>
                <?php if ($this->countModules('footer-9', true)) : ?>	
                      <div class="col-md-6 col-lg-4">
                        <jdoc:include type="modules" name="footer-9" />
                      </div>
                <?php endif; ?>
              </div>
        <?php endif; ?>  
      </section>
	  <?php endif; ?>  
    
    <?php if ($this->countModules('footer-links-blocks', true)) : ?>	
      <section>
        <div class="row">
					<jdoc:include type="modules" name="footer-links-blocks" />
        </div>
      </section>
	  <?php endif; ?>
    
    <?php if ($this->countModules('footer-data-blocks', true) || $this->countModules('copyright', true)) : ?>	
      <section class="my-2 py-2 mt-5 border-white border-top">
        <div class="row">
            <div class="col-6">
              <jdoc:include type="modules" name="footer-data-blocks" />
            </div>
            <div class="col-6 text-end">
              <jdoc:include type="modules" name="copyright" />
            </div>
        </div>
      </section>
	  <?php endif; ?>
    
    <?php if ($this->countModules('footer-bottom-links-blocks', true)) : ?>	
      <div class="it-footer-small-prints clearfix">
        <div class="container">
          <h3 class="sr-only">Sezione Link Utili</h3>
          <jdoc:include type="modules" name="footer-bottom-links-blocks" />
        </div>
      </div>
    <?php endif; ?>

    </div>
  </div>
</footer>

<?php if($this->params->get('cookiebar')) { ?>
<section class="cookiebar fade" aria-label="Gestione dei cookies">
	<?php
	if($this->params->get('cookiebarDescription')) {
		echo "<p>".htmlspecialchars($this->params->get('cookiebarDescription'))."</p>";
	}
	?>
	<div class="cookiebar-buttons">
	<?php                                                                             
    $cookiepolicyitemid = $this->params->get('cookiebarPolicyMenuItemId');

    if($cookiepolicyitemid) {
        $cookiemenuitem = $sitemenu->getItem($cookiepolicyitemid);
        ?><a href="<?php echo $cookiemenuitem->link;?>" class="cookiebar-btn">Informativa <span class="visually-hidden">sui cookies</span></a>
	<?php
        }
			$cookiebarConfirmLabel = $this->params->get('cookiebarConfirmLabel')?htmlspecialchars($this->params->get('cookiebarConfirmLabel')):"Accetto"; ?>
	<button data-bs-accept="cookiebar" class="cookiebar-btn cookiebar-confirm"><?php echo $cookiebarConfirmLabel; ?></button>
	</div>
</section>
<?php } ?>

</body>
</html>