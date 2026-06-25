<?php 
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;


$cfg	 = JEVConfig::getInstance();

if ($cfg->get("tooltiptype",'joomla')=='overlib'){
	JEVHelper::loadOverlib();
}

$view = $this->getViewName();

echo $this->loadTemplate('cell' );
$eventCellClass = "EventCalendarCell_".$view;


echo "<div id='cal_title'>".$this->data['fieldsetText']."</div>\n";
    ?>
        <table class="cal_table w-100">
            <tr>
            	<th scope="col" class="cal_td_daysnames th_weeklinks"><span class="d-none">Settimana</span></th>
                <?php foreach ($this->data["daynames"] as $dayname) { ?>
                    <th class="cal_td_daysnames text-center ">
                        <?php 
                        echo $dayname;?>
                    </th>
                    <?php
                } ?>
            </tr>
            <?php
            $datacount = count($this->data["dates"]);
            $dn=0;
	    foreach ($this->data['weeks'] AS $wkn => $week) {
            ?>
			<tr class="vtop h80px">
                <?php
                echo "<th scope='row' class='cal_td_weeklink'>";
				echo "<a href='".$week."'>$wkn</a></th>\n";
                for ($d=0;$d<7 && $dn<$datacount;$d++){
                	$currentDay = $this->data["dates"][$dn];
                	switch ($currentDay["monthType"]){
                		case "prior":
                		case "following":
                		?>
                    <td class="cal_td_daysoutofmonth">
                        <?php echo $currentDay["d"]; ?>
                    </td>
                    	<?php
                    	break;
                		case "current":
                			$cellclass = $currentDay["today"]?'class="cal_td_today"':(count($currentDay["events"])>0?'class="cal_td_dayshasevents"':'class="cal_td_daysnoevents"');
                			//$cellclass = $currentDay["today"]?'class="cal_td_today"':'class="cal_td_daysnoevents"';

						?>
                    <td <?php echo $cellclass;?>>
                     <?php   $this->_datecellAddEvent($this->year, $this->month, $currentDay["d"]);?>
                    	<a class="cal_daylink" href="<?php echo $currentDay["link"]; ?>" title="<?php echo Text::_('JEV_CLICK_TOSWITCH_DAY'); ?>"><?php echo $currentDay['d']; ?></a>
                        <?php

                        if (count($currentDay["events"])>0){
                        	foreach ($currentDay["events"] as $key=>$val){
                        		if( $currentDay['countDisplay'] < $cfg->get('com_calMaxDisplay',5)) {
                        			echo '<div class="b0 w100">';
                        		} else {
                        			// float small icons left
                        			echo '<div class="b0 fleft">';
                        		}
                        		echo "\n";
                        		$ecc = new $eventCellClass($val,$this->datamodel, $this);
                        		echo $ecc->calendarCell($currentDay,$this->year,$this->month,$key);
                        		echo '</div>' . "\n";
                        		$currentDay['countDisplay']++;
                        	}
                        }
                        echo "</td>\n";
                        break;
                	}
                	$dn++;
                }
                echo "</tr>\n";
            }
            echo "</table>\n";
            $this->eventsLegend();

