<?php 
   foreach($result['visitor_data'] as $key => $content)
   {           	
  
   $referer = @($content->referer) ? ("via \"" . $content->referer ."\" ") : ""; 
   $city = @($content->city) ? $content->city .", " : ""; 
   $country_name = @($content->country) ? $content->country : ""; 
   
   	?>
<tr>
   <td><?php echo sprintf("<a href=\"%s\">%s: \"%s\" %s from %s%s</a>",htmlentities($content->page_url), emgl_get_visitor_type($content), $content->page_title,$referer, $city, $country_name);?></td>
</tr>
<?php
   }
   ?>
