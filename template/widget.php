<?php 
   require('widget-style.php');
   ?>
<h1>Visitor</h1>
<div id="emgl-vis-anl">
   <?php if(get_option('emgl_show_active_visitor')):
	   /**	only show if widget is allowed **/
	   ?>
   <div id="emgl-active-visitor"><?php _e('Active Visitor');?>: <span class="content"><?php echo $result['active_visitor'][0]->active_visitor;?></span></div>
   <?php endif;?>

   <?php if(get_option('emgl_show_page_view')):
	   /**	only show if widget is allowed **/
	   ?>   
   <div id="emgl-total-page-view"><?php _e('Today Page View');?>: <span class="content"><?php echo $result['total_page_view'][0]->total_page_view;?></span></div>
   <?php endif;?>

   <?php if(get_option('emgl_show_visitor')):
	   /**	only show if widget is allowed **/
	   ?>   
   <div id="emgl-total-visitor"><?php _e('Today Visitor');?>: <span class="content"><?php echo $result['total_visitor'][0]->total_visitor;?></span></div>
    <?php endif;?>
  
   <?php if(get_option('emgl_show_real_time_feed')):
   	   /**	only show if widget is allowed **/
	   ?>
   <div id="emgl-real-time-feed-header"><?php _e('Real Time Access');?>:</div>
   <div id="emgl-real-time-feed">
      <table width="90%">
         <thead>
         </thead>
         <tbody>
            <?php 
               include('real-time-feed-row.php');
               ?>
         </tbody>
      </table>
   </div>
   <div id="emgl-last-update" class="emgl-meta"><?php _e('Update');?>: <span class="content"><?php echo date('Y-m-d H:i:s');?></span></div>
   
   <?php endif; ?>
   
   <div class="emgl-meta"><?php _e('Duration');?>: <?php echo number_format(1000*(microtime(true) - $start_time),2);?>ms</div>
   <input id="emgl-last-id" value="<?php echo $result['visitor_data'][0]->id;?>" type="hidden"/>
   <input id="emgl-max-row" value="<?php echo get_option('emgl_visitor_data_row', 5);?>" type="hidden"/>
   <input id="emgl-page-title" value="<?php echo $data['page_title'];?>" type="hidden"/>
   <input id="emgl-referer" value="<?php echo  $_SERVER['HTTP_REFERER'];?>" type="hidden"/>
   <input id="emgl-page-url" value="<?php echo $_SERVER['REQUEST_URI'];?>" type="hidden"/>
   <input id="emgl-update-interval" value="<?php echo get_option('emgl_update_interval',10);?>" type="hidden"/>

</div>
