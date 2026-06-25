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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;

jimport('joomla.application.module.helper');

$app = Factory::getApplication();

$this->category->text = $this->category->description;
$app->triggerEvent('onContentPrepare', array($this->category->extension . '.categories', &$this->category, &$this->params, 0));
$this->category->description = $this->category->text;

$results = $app->triggerEvent('onContentAfterTitle', array($this->category->extension . '.categories', &$this->category, &$this->params, 0));
$afterDisplayTitle = trim(implode("\n", $results));

$results = $app->triggerEvent('onContentBeforeDisplay', array($this->category->extension . '.categories', &$this->category, &$this->params, 0));
$beforeDisplayContent = trim(implode("\n", $results));

$results = $app->triggerEvent('onContentAfterDisplay', array($this->category->extension . '.categories', &$this->category, &$this->params, 0));
$afterDisplayContent = trim(implode("\n", $results));

$htag    = $this->params->get('show_page_heading') ? 'h2' : 'h1';

$document   = Factory::getDocument();
$renderer   = $document->loadRenderer('module');

$innerRight = '';
   foreach (JModuleHelper::getModules('inner-right') as $mod)  {
      $innerRight .= $renderer->render($mod, array('style'=>'0'));
   }

?>
<div class="com-content-category-blog blog" itemscope itemtype="https://schema.org/Blog">
	<section id="intro" class="container px-4 my-4">
    <div class="row">
      <div class="col-lg-7 px-lg-4 py-lg-2">
        <?php if ($this->params->get('show_page_heading')) : ?>
		<div class="page-header">
			<h1> <?php echo $this->escape($this->params->get('page_heading')); ?> </h1>
		</div>
	<?php endif; ?>

	<?php if ($this->params->get('show_category_title', 1)) : ?>
	<?php echo '<'. $htag . ' class="text-primary">' . $this->category->title . '</'. $htag . '>'; ?>
	<?php endif; ?>
	<?php echo $afterDisplayTitle; ?>
      
      <?php if ($beforeDisplayContent || $afterDisplayContent || $this->params->get('show_description', 1) || $this->params->def('show_description_image', 1)) : ?>
		<div class="category-desc clearfix">
			<?php if ($this->params->get('show_description_image') && $this->category->getParams()->get('image')) : ?>
				<?php $alt = empty($this->category->getParams()->get('image_alt')) && empty($this->category->getParams()->get('image_alt_empty')) ? '' : 'alt="' . htmlspecialchars($this->category->getParams()->get('image_alt'), ENT_COMPAT, 'UTF-8') . '"'; ?>
				<img src="<?php echo $this->category->getParams()->get('image'); ?>" <?php echo $alt; ?>>
			<?php endif; ?>
			<?php echo $beforeDisplayContent; ?>
			<?php if ($this->params->get('show_description') && $this->category->description) : ?>
				<?php echo HTMLHelper::_('content.prepare', $this->category->description, '', 'com_content.category'); ?>
			<?php endif; ?>
			<?php echo $afterDisplayContent; ?>
		</div>
	<?php endif; ?>
      
    <?php if ($this->params->get('show_cat_tags', 1) && !empty($this->category->tags->itemTags)) : ?>
		<?php $this->category->tagLayout = new FileLayout('joomla.content.tags'); ?>
		<?php echo $this->category->tagLayout->render($this->category->tags->itemTags); ?>
	<?php endif; ?>
      
      
      </div>
      <div class="col-lg-4 offset-lg-1 pt-5 pt-lg-2">
        	<?php echo $innerRight; ?>
      </div>
    </div>
  </section>
	

	
<section id="in-evidenza">
    <div class="bg-light py-5">
      <div class="container px-4">
        <div class="row">
          <div class="col">
            <h2 class="mb-4">In evidenza</h2>
          </div>
        </div>
        <div class="row">
        
   <?php if (empty($this->lead_items) && empty($this->link_items) && empty($this->intro_items)) : ?>
        <?php if ($this->params->get('show_no_articles', 1)) : ?>
          <div class="col-12">  <div class="alert alert-info">
                <span class="icon-info-circle" aria-hidden="true"></span><span class="d-none"><?php echo Text::_('INFO'); ?></span>
                <?php echo Text::_('COM_CONTENT_NO_ARTICLES'); ?>
            </div></div>
        <?php endif; ?>
    <?php endif; ?>
        
   <?php $leadingcount = 0; ?>
	<?php if (!empty($this->lead_items)) : ?>
			<?php foreach ($this->lead_items as &$item) : ?>
						<?php
        				$this->leaditem = true;
						$this->item = & $item;
						echo $this->loadTemplate('item');
						?>
				<?php $leadingcount++; ?>
			<?php endforeach; ?>
	<?php endif; ?>

	<?php
	$introcount = count($this->intro_items);
	$counter = 0;
	?>
        
        <?php if (!empty($this->intro_items)) : ?>
		<?php foreach ($this->intro_items as $key => &$item) : ?>
        			<?php
					$this->item = & $item;
        				$this->leaditem = false;
					echo $this->loadTemplate('item');
					?>
		<?php endforeach; ?>
	<?php endif; ?>
        
	<?php if (!empty($this->link_items)) : ?>
		<div class="items-more">
			<?php echo $this->loadTemplate('links'); ?>
		</div>
	<?php endif; ?>
    
    
    <div class="text-center col-12">
	<?php if (($this->params->def('show_pagination', 1) == 1 || ($this->params->get('show_pagination') == 2)) && ($this->pagination->pagesTotal > 1)) : ?>
		<div class="com-content-category-blog__navigation w-100">
			<?php if ($this->params->def('show_pagination_results', 1)) : ?>
				<p class="com-content-category-blog__counter counter float-end pt-3 pe-2">
					<?php echo $this->pagination->getPagesCounter(); ?>
				</p>
			<?php endif; ?>
			<div class="com-content-category-blog__pagination">
				<?php echo $this->pagination->getPagesLinks(); ?>
			</div>
		</div>
	<?php endif; ?>
    </div>
        
        </div>
      </div>
    </div>
  </section>
	
	

	<?php if ($this->maxLevel != 0 && !empty($this->children[$this->category->id])) : ?>
<section>
    <div class="py-5">
      <div class="container px-4">
        <div class="row">
          <div class="col">
            <h2 class="mb-4">Tutte le aree di <?php echo $this->escape($this->params->get('page_heading')); ?></h2>
          </div>
        </div>
        <div class="row">
        
			<?php echo $this->loadTemplate('children'); ?>
        
        
        </div>
      </div>
    </div>
  </section>
	<?php endif; ?>

</div>
