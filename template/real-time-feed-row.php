<?php 
   foreach($result['visitor_data'] as $key => $content)
   {           	
  
   $referer = @($content->referer) ? ("\"" . $content->referer ."\"") : ""; 
   $city = @($content->city) ? $content->city .", " : ""; 
   $country_name = @($content->country) ? $content->country : ""; 
   
   	?>
<tr>
   <td>
   
   <?php 
   
   //	create information based on who is acces, what page, where they access from
   
   $visitor_type = emgl_get_visitor_type($content);
   
   if($visitor_type == 'human')
   {
	   if(!empty($referer))
	   {
		   //	if referer is not empty then provide referer data

		   echo sprintf("<a href=\"%s\">%s: \"%s\" via %s from %s%s</a>",
		   htmlentities($content->page_url),
		   $visitor_type, 
		   $content->page_title,
		   $referer, 
		   $city, 
		   $country_name);

	   }
	   elseif(empty($referer))
	   {
		   //	if referer is empty then provide direct information
		   echo sprintf("<a href=\"%s\">%s: \"%s\" from %s%s</a>",
		   htmlentities($content->page_url), 
		   $visitor_type, 
		   $content->page_title, 
		   $city, 
		   $country_name);
		   
	   }
	}
   elseif($visitor_type == 'crawler')
   {
	   if(!empty($referer))
	   {
		   echo sprintf("<a href=\"%s\">%s: %s access \"%s\" from %s%s</a>",
		   htmlentities($content->page_url),
		   $visitor_type, 
		   $referer, 
		   $content->page_title,
		   $city, 
		   $country_name);
	   }
	   elseif(empty($referer))
	   {
		   echo sprintf("<a href=\"%s\">%s: \"Bot\" access \"%s\" from %s%s</a>",
		   htmlentities($content->page_url),
		   $visitor_type, 
		   $content->page_title,
		   $city, 
		   $country_name);			   
	   }
   }
   elseif($visitor_type == 'spammer')
   {
	   if(!empty($referer))
	   {
		   echo sprintf("<a href=\"%s\">%s: %s access \"%s\" from %s%s</a>",
		   htmlentities($content->page_url),
		   $visitor_type, 
		   $referer, 
		   $content->page_title,
		   $city, 
		   $country_name);
	   }
	   elseif(empty($referer))
	   {
		   echo sprintf("<a href=\"%s\">%s: \"Bot\" access \"%s\" from %s%s</a>",
		   htmlentities($content->page_url),
		   $visitor_type, 
		   $content->page_title,
		   $city, 
		   $country_name);			   
	   }
	   
   }
   
   ?></td>
</tr>
<?php
   }
   ?>
