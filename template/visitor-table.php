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
