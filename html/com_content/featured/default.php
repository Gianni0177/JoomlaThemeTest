<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   (C) 2006 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport( 'joomla.application.module.helper' );
$modules = JModuleHelper::getModules('other-featured-content-bottom');

?>
<div class="blog-featured" itemscope itemtype="https://schema.org/Blog">
	<?php if ($this->params->get('show_page_heading') != 0) : ?>
	<div class="page-header <?php if(str_contains($this->params->get('pageclass_sfx'),"hide-pagetitle")) { ?>visually-hidden<?php } ?>">
    	<div class="section <?php if(empty($this->lead_items)) { echo 'section-muted'; } ?> pt-4 pb-2 px-0">
        	<div class="container">
            	<h1 class="h2">
        			<?php echo $this->escape($this->params->get('page_heading')); ?>
    			</h1>
    		</div>
    	</div>
	</div>
	<?php endif; ?>

	<?php $leadingcount = 0; ?>
	<?php if (!empty($this->lead_items)) : ?>
		<section id="head-section" class="blog-items items-leading <?php echo $this->params->get('blog_class_leading'); ?>">
        <div class="container">
			<?php foreach ($this->lead_items as &$item) : ?>
				<div class="blog-item"
					itemprop="blogPost" itemscope itemtype="https://schema.org/BlogPosting">
						<?php
						$this->item = & $item;
						echo $this->loadTemplate('leading_item');
						?>
				</div>
				<?php $leadingcount++; ?>
			<?php endforeach; ?>
        </div>
		</section>
	
	<?php endif; ?>
	<?php if (!empty($this->intro_items)) : ?>
		<?php $blogClass = $this->params->get('blog_class', ''); ?>
		<?php if ((int) $this->params->get('num_columns') > 1) : ?>
			<?php $blogClass .= (int) $this->params->get('multi_column_order', 0) === 0 ? ' masonry-' : ' columns-'; ?>
			<?php $blogClass .= (int) $this->params->get('num_columns'); ?>
		<?php endif; ?>

		<section id="other-featured-contents">
			<?php
				$sectionadditionalclasses = ($leadingcount>0) ? "pt-0" : "py-3";
			?>
        	<div class="section section-muted <?php echo $sectionadditionalclasses; ?> pb-90 pb-lg-50 px-lg-5 blog-items <?php echo $blogClass; ?>">
            	<div class="container">
					<div class="row mb-2">
						<?php
							$additionalclasses = ($leadingcount>0) ? " card-overlapping" : "";
						?>
                    	<div class="card-wrapper px-0 <?php echo $additionalclasses; ?> card-teaser-wrapper card-teaser-wrapper-equal card-teaser-block-<?php echo $this->params->get('num_columns') ?>">
        					<?php foreach ($this->intro_items as $key => &$item) : ?>
        					    <div class="blog-item card card-teaser card-teaser-image card-flex no-after rounded shadow-sm border border-light mb-0"
        		 			       itemprop="blogPost" itemscope itemtype="https://schema.org/BlogPosting">
          		 			         <?php
         					           $this->item = & $item;
          					          echo $this->loadTemplate('item');
          					          ?>
         					   </div>
        					<?php endforeach; ?>
                        </div>
                	</div>
        		</div> 
            			<?php
                            $attribs = array('style' => 'xhtml'); 
							
							if (count($modules)>0) { 
              					
                              foreach($modules as $module) {
                                ?><div class="other-featured-content-bottom mt-4">
       							<?php echo JModuleHelper::renderModule($module, $attribs); ?> 
                            </div><?php
                              }
              				?> 
                            
                            
						<?php } ?>
        	</div>
		</section>

	<?php endif; ?>

	<?php if (!empty($this->link_items)) : ?>
		<div class="items-more">
			<?php echo $this->loadTemplate('links'); ?>
		</div>
	<?php endif; ?>

	<?php if ($this->params->def('show_pagination', 2) == 1  || ($this->params->get('show_pagination') == 2 && $this->pagination->pagesTotal > 1)) : ?>
		<div class="w-100">
			<?php if ($this->params->def('show_pagination_results', 1)) : ?>
				<p class="counter float-end pt-3 pe-2">
					<?php echo $this->pagination->getPagesCounter(); ?>
				</p>
			<?php endif; ?>
			<?php echo $this->pagination->getPagesLinks(); ?>
		</div>
	<?php endif; ?>

</div>
