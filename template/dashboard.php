<div class="wrap">
<h2>Summary</h2>
<?php
	include('dashboard-style.php');
   global $wpdb;
   
	/**	get the duration for summary analysis	**/
   $summary_duration = get_option('emgl_summary_duration');
   
   /**	get the percentage of human visitor since d days back	**/
   $human_visit_result = $wpdb->get_results($wpdb->prepare("SELECT 
   DATE(trigger_timestamp) AS date, 
   100*SUM(human_flag)/(SUM(spammer_flag)+SUM(human_flag)+SUM(crawler_flag)) AS human_percentage, 
   SUM(human_flag) AS human_visitor 
   FROM `".EMGL_TABLE_VISITOR_LOG."` 
   WHERE DATE(trigger_timestamp) > DATE_SUB(CURDATE(), INTERVAL %d DAY) 
   GROUP BY DATE(trigger_timestamp) 
   ORDER BY DATE(trigger_timestamp) DESC ", $summary_duration));
   
   /**	select most visited page by human	**/
   $page_visit_human_result = $wpdb->get_results($wpdb->prepare("SELECT 
   page_title,
   COUNT(page_title) AS page_visit 
   FROM `".EMGL_TABLE_VISITOR_LOG."` 
   WHERE DATE(trigger_timestamp) > DATE_SUB(CURDATE(), INTERVAL %d DAY)
   AND human_flag = 1
   GROUP BY page_title 
   ORDER BY page_visit DESC
   LIMIT 5", $summary_duration));
   
   /**	select most visited page by spammer	**/
   $page_visit_spammer_result = $wpdb->get_results($wpdb->prepare("SELECT 
   page_title,
   COUNT(page_title) AS page_visit 
   FROM `".EMGL_TABLE_VISITOR_LOG."` 
   WHERE DATE(trigger_timestamp) > DATE_SUB(CURDATE(), INTERVAL %d DAY) 
   AND spammer_flag = 1
   GROUP BY page_title 
   ORDER BY page_visit DESC
   LIMIT 5", $summary_duration));
   
   /**	select most visited page by crawler	**/
   $page_visit_crawler_result = $wpdb->get_results($wpdb->prepare("SELECT 
   page_title,
   COUNT(page_title) AS page_visit 
   FROM `".EMGL_TABLE_VISITOR_LOG."` 
   WHERE DATE(trigger_timestamp) > DATE_SUB(CURDATE(), INTERVAL %d DAY)
   AND crawler_flag = 1
   GROUP BY page_title 
   ORDER BY page_visit DESC
   LIMIT 5", $summary_duration));
   ?>
   <div>
   <h3>Rejection Ratio</h3>
   <div id="emgl-rejection-ratio-chart-container" style="width:90%">
   
   </div>
      <p>Aggregate By:
      <select name="emgl-rejection-ratio-interval" id="emgl-rejection-ratio-interval">
      <option value="3600">Hourly</option>
      <option value="86400" selected="selected">Daily</option>
      </select>
      </p>
      <p>
      <input type="submit" id="emgl-rejection-ratio-chart-refresh" name="submit" class="button button-primary" value="REFRESH CHART"></p>
      </div>
   
<div style="float:left">
   <h3>Human Visitor</h3>
   <table class="em-table">
      <thead>
         <tr>
            <th>No</th>
            <th>Date</th>
            <th>Visit</th>
            <th>Percentage</th>
         </tr>
      </thead>
      <tbody>
         <?php 
            $row_count = 1;
  			/**	iterate all human visit data	**/
            foreach ($human_visit_result as $key => $content):?>
         <tr>
            <td style="text-align:center"><?php echo $row_count++;?></td>
            <td><?php echo $content->date;?></td>
            <td><?php echo $content->human_visitor;?></td>
            <td><?php echo number_format($content->human_percentage,2);?>%</td>
         </tr>
         <?php
            endforeach;
            ?>
      </tbody>
   </table>
</div>
<div style="clear:both"/>
<div style="float:left">
   <h3>Favourite by Human</h3>
   <table class="em-table">
      <thead>
         <tr>
            <th>Rank</th>
            <th>Page Title</th>
            <th>Visit</th>
         </tr>
      </thead>
      <tbody>
         <?php 
            $row_count = 1;
  			/**	iterate all human data	**/
          foreach ($page_visit_human_result as $key => $content):?>
         <tr>
            <td style="text-align:center"><?php echo $row_count++;?></td>
            <td><?php echo $content->page_title;?></td>
            <td><?php echo $content->page_visit;?></td>
         </tr>
         <?php
            endforeach;
            ?>
      </tbody>
   </table>
</div>
<div style="float:left">
   <h3>Favourite by Spammer</h3>
   <table class="em-table">
      <thead>
         <tr>
            <th>Rank</th>
            <th>Page Title</th>
            <th>Visit</th>
         </tr>
      </thead>
      <tbody>
         <?php 
            $row_count = 1;
			/**	iterate all spammer data	**/
            foreach ($page_visit_spammer_result as $key => $content):?>
         <tr>
            <td style="text-align:center"><?php echo $row_count++;?></td>
            <td><?php echo $content->page_title;?></td>
            <td><?php echo $content->page_visit;?></td>
         </tr>
         <?php
            endforeach;
            ?>
      </tbody>
   </table>
</div>
<div style="float:left">
   <h3>Favourite by Crawler</h3>
   <table class="em-table">
      <thead>
         <tr>
            <th>Rank</th>
            <th>Page Title</th>
            <th>Visit</th>
         </tr>
      </thead>
      <tbody>
         <?php 
            $row_count = 1;
            foreach ($page_visit_crawler_result as $key => $content):?>
         <tr>
            <td style="text-align:center"><?php echo $row_count++;?></td>
            <td><?php echo $content->page_title;?></td>
            <td><?php echo $content->page_visit;?></td>
         </tr>
         <?php
            endforeach;
            ?>
      </tbody>
   </table>
</div>
<div style="clear:both"/>
   <h2>Visitor Data</h2>
   <?php
      
      $admin_max_row = get_option('emgl_dashboard_visitor_row', 20);
      
      /**	get initial records	**/
      $query_result = $wpdb->get_results($wpdb->prepare("SELECT * FROM `".EMGL_TABLE_VISITOR_LOG."` ORDER BY trigger_timestamp DESC LIMIT 0, %d", $admin_max_row));
      
      /**	get total record count	**/
      	$sql = sprintf("SELECT COUNT(*) AS total_row FROM `%s`", EMGL_TABLE_VISITOR_LOG); 
      $query_count_result = $wpdb->get_results($sql);
      $query_count_result = $query_count_result[0]->total_row;
      
      /**	get the latest date record	**/
      	$sql = sprintf("SELECT trigger_timestamp FROM `%s` ORDER BY trigger_timestamp ASC LIMIT 1", EMGL_TABLE_VISITOR_LOG); 
      $query_oldest_date_result = $wpdb->get_results($sql);
      $query_oldest_date_result = $query_oldest_date_result[0]->trigger_timestamp;
      ?>
   <p>Total Records <?php echo number_format($query_count_result,0);?> rows, since <?php echo $query_oldest_date_result;?></p>
   <p class="submit"><input value="CLEAN UP" id="clean-visitor-data" class="button button-primary" name="clean-visitor-data" type="button" /></p>
   <div class="em-table-wrapper">
   <table class="em-table">
      <thead>
         <tr>
            <th>No</th>
            <th>ID</th>
            <th>Location</th>
            <th>Visitor</th>
            <th>Page</th>
            <th>Visit Time</th>
         </tr>
      </thead>
      <tbody>
         <?php 
            $row_count = 1;	//	initiate the number of row count
            
            /**	iterate all the records	**/
            foreach($query_result as $key => $content): 
            
               $city = @($content->city) ? $content->city .", " : "" ; 
               $country_name = @($content->country) ? $content->country : ""; 
               $location = $city.$country_name;
            
            ?>
         <tr>
            <td><?php echo $row_count++;?></td>
            <td><?php echo $content->id;?></td>
            <td><?php echo $location;?></td>
            <td><?php echo emgl_get_visitor_type($content);?></td>
            <td><?php echo $content->page_url;?></td>
            <td><?php echo $content->trigger_timestamp;?></td>
         </tr>
         <?php endforeach;?>
      </tbody>
   </table>
   </div>
   <h2>Blocked Visitor Data</h2>
   <?php
      
      $admin_max_row = get_option('emgl_dashboard_visitor_row', 20);
      
      /**	get initial records	**/
      $query_result = $wpdb->get_results($wpdb->prepare("SELECT * FROM `".EMGL_TABLE_BLOCK_VISITOR_LOG."` ORDER BY trigger_timestamp DESC LIMIT 0, %d", $admin_max_row));
      
      /**	get total record count	**/
      	$sql = sprintf("SELECT COUNT(*) AS total_row FROM `%s`", EMGL_TABLE_BLOCK_VISITOR_LOG); 
      $query_count_result = $wpdb->get_results($sql);
      $query_count_result = $query_count_result[0]->total_row;
      
      /**	get the latest date record	**/
      	$sql = sprintf("SELECT trigger_timestamp FROM `%s` ORDER BY trigger_timestamp ASC LIMIT 1", EMGL_TABLE_BLOCK_VISITOR_LOG); 
      $query_oldest_date_result = $wpdb->get_results($sql);
      $query_oldest_date_result = $query_oldest_date_result[0]->trigger_timestamp;
      ?>
   <p>Total Records <?php echo number_format($query_count_result,0);?> rows, since <?php echo $query_oldest_date_result;?></p>
   <div class="em-table-wrapper">
   <table class="em-table">
      <thead>
         <tr>
            <th>No</th>
            <th>ID</th>
            <th>Location</th>
            <th>Visitor</th>
            <th>Page</th>
            <th>Visit Time</th>
         </tr>
      </thead>
      <tbody>
         <?php 
            $row_count = 1;	//	initiate the number of row count
            
            /**	iterate all the records	**/
            foreach($query_result as $key => $content): 
            
               $city = @($content->city) ? $content->city .", " : "" ; 
               $country_name = @($content->country) ? $content->country : ""; 
               $location = $city.$country_name;
            
            ?>
         <tr>
            <td><?php echo $row_count++;?></td>
            <td><?php echo $content->id;?></td>
            <td><?php echo $location;?></td>
            <td><?php echo emgl_get_visitor_type($content);?></td>
            <td><?php echo $content->page_url;?></td>
            <td><?php echo $content->trigger_timestamp;?></td>
         </tr>
         <?php endforeach;?>
      </tbody>
   </table>
   </div>
      <h2>Top 10 Spammer</h2>
   <?php
      
      
      /**	get initial records	**/
      $sql = sprintf("SELECT 
	A.*, 
	COUNT(B.ip_address) AS visit_count, 
	B.country 
	FROM `%s` A LEFT JOIN `%s` B 
	ON A.ip_address = B.ip_address 
   GROUP BY A.ip_address ORDER BY A.id LIMIT 10", EMGL_TABLE_SPAMMER_LIST, EMGL_TABLE_VISITOR_LOG); 
      $query_result = $wpdb->get_results($sql);
      
      /**	get total record count	**/
      	$sql = sprintf("SELECT COUNT(*) AS total_row FROM `%s`", EMGL_TABLE_SPAMMER_LIST); 
      $query_count_result = $wpdb->get_results($sql);
      $query_count_result = $query_count_result[0]->total_row;
      
      ?>
   <p>Total Records <?php echo number_format($query_count_result,0);?> spammer</p>
   <p class="submit"><input value="ANALYZE SPAM" id="execute-spammer-analysis" class="button button-primary" name="execute-spammer-analysis" type="button" /></p>
   <div class="em-table-wrapper">
   <table class="em-table">
      <thead>
         <tr>
            <th>No</th>
            <th>IP Address</th>
            <th>Visit Count</th>
            <th>Country</th>
         </tr>
      </thead>
      <tbody>
         <?php 
            $row_count = 1;	//	initiate the number of row count
            
            /**	iterate all the records	**/
            foreach($query_result as $key => $content): 
                        
            ?>
         <tr>
            <td><?php echo $row_count++;?></td>
            <td><?php echo $content->ip_address;?></td>
            <td style="text-align:center"><?php echo $content->visit_count;?></td>
            <td><?php echo $content->country;?></td>
         </tr>
         <?php endforeach;?>
      </tbody>
   </table>
   </div>
</div>

<script>

jQuery(document).ready(function($)
{
	$('#clean-visitor-data').click(function()
	{
    var data = {
        'action': 'emgl_cleanup_visitor_data'
    }
    ajaxUrl = '/wp-admin/admin-ajax.php';

    //	trigger store visitor data
	$.post(ajaxUrl, data, function(response) {
		response = response.match(/{.*}/);
		response = JSON.parse(response);
		alert(response.message);	
	});
	});
	
	$('#execute-spammer-analysis').click(function()
	{
    var data = {
        'action': 'emgl_spammer_analysis'
    }
    ajaxUrl = '/wp-admin/admin-ajax.php';

    //	trigger store visitor data
	$.post(ajaxUrl, data, function(response) {
		response = response.match(/{.*}/);
		response = JSON.parse(response);
		alert(response.message);	
	});
	});

});
</script>
