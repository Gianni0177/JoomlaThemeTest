<?php
defined('_JEXEC') or die('Restricted access');
echo '<div class="container">';
$this->_header();
$this->_showNavTableBar();

echo $this->loadTemplate("body");

$this->_viewNavAdminPanel();

$this->_footer();
echo '</div>';
