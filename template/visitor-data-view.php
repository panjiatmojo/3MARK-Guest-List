<div id="emgl-visitor-container">
   <h2>Visitor Data</h2>
   <?php
           
      /**	get the latest date record	**/
      $sql = sprintf("SELECT trigger_timestamp FROM `%s` ORDER BY trigger_timestamp ASC LIMIT 1", EMGL_TABLE_VISITOR_LOG); 
      $query_oldest_date_result = $wpdb->get_results($sql);
      $query_oldest_date_result = $query_oldest_date_result[0]->trigger_timestamp;

	/**	get total record count	**/
	$sql = sprintf("SELECT COUNT(*) AS total_row FROM `%s`", EMGL_TABLE_VISITOR_LOG); 
	$query_count_result = $wpdb->get_results($sql);
	$parameter['total_row'] = $query_count_result[0]->total_row;  
	  
	  $parameter = emgl_calculate_pagination_data($parameter);
	  
      ?>
   <p>Total Records <?php echo number_format($parameter['total_row'],0);?> rows, since <?php echo $query_oldest_date_result;?></p>
   <p class="submit"><input value="CLEAN UP" id="clean-visitor-data" class="button button-primary" name="clean-visitor-data" type="button" />
   <input value="REFRESH" id="refresh-visitor-data" class="button button-primary" name="refresh-visitor-data" type="button" /></p>
   <input value="<?php echo $parameter['total_row'];?>" name="emgl-visitor-data-total-row" id="emgl-visitor-data-total-row" type="hidden"/>
      <div id="emgl-visitor-data-pagination"><?php emgl_show_pagination($parameter);?></div>
   <div class="em-table-wrapper" id="emgl-visitor-data-table">
      <?php 
	  emgl_show_visitor_data($parameter);
	  ?>
   </div>
   </div>