<table class="em-table">
      <thead>
         <tr>
            <th>No</th>
            <th>ID</th>
            <th>Location</th>
            <th>Visitor</th>
            <th>Page</th>
            <th>Visit Time</th>
            <th>Action</th>
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
			   
			   $visitor_type = emgl_get_visitor_type($content);
			   
			   /**	added functionality to block / unblock specific visitor	**/
			   $visitor_action = ($visitor_type == 'spammer') ? 'unblock' : 'block';
			   $visitor_button = ($visitor_type == 'spammer') ? 'UNBLOCK' : 'BLOCK';
            
            ?>
         <tr>
            <td><?php echo $row_count++;?></td>
            <td><?php echo $content->id;?></td>
            <td><?php echo $location;?></td>
            <td class="emgl-visitor-type"><?php echo emgl_get_visitor_type($content);?></td>
            <td><?php echo htmlentities($content->page_url);?></td>
            <td><?php echo $content->trigger_timestamp;?>            
            <textarea style="display:none" name="em-blocked-detail"><?php echo emgl_convert_array_to_list(json_decode($content->request_content, TRUE));?></textarea></td>
            <td><input type="button" class="emgl-block-action-button" value="<?php echo $visitor_button;?>"/>
			<input type="hidden" value="<?php echo $content->ip_address;?>" class="emgl-ip-address" />															
            <input type="hidden" value="<?php echo $visitor_action;?>" class="emgl-block-action" /></td>
         </tr>
         <?php endforeach;?>
      </tbody>
   </table>
