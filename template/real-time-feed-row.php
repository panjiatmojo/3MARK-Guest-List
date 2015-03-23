<?php 
   foreach($result['visitor_data'] as $key => $content)
   {           	
        
   
   	?>
<tr>
   <td><?php echo get_feed_word($content);?></td>
</tr>
<?php
   }
   ?>
