<div class="pagination-wrapper">
   <?php
   /**	calculate the segment size - left, center, right	**/
      $segment_size = floor($visible_page/3);
      
      if($max_page < $visible_page)
      {
      	/**	show all the pages	**/
      	for($i = 0;$i < $max_page; $i++)
      	{
      		$shown_page[$i] = $i; 
      	}
      	
      }
      elseif($current_page < (2 * $segment_size) - 1)
      {
      	/**	distribute pages on the left side	**/
      
      	for($i = 0;$i < (2*$segment_size); $i++)
      	{
      		$shown_page[$i] = $i; 
      	}
      	
      	$shown_page[$i++] = '...';
      	
      	$j = 0;
		/**	add offset +1 to compensate '...' empty page	**/
      	for($i = $i; $i < ($segment_size*3)+1; $i++)
      	{
      		$shown_page[$i] = ($max_page) - ($segment_size - $j); 
      		$j++;
      	}
      		
      }
      elseif($current_page > ($max_page - (2 * $segment_size)))
      {
      	/**	distribute pages on the right side	**/
      	for($i = 0;$i < $segment_size; $i++)
      	{
      		$shown_page[$i] = $i; 
      	}
      	
      	$shown_page[$i++] = '...';
      	
      	$j = 0;
		/**	add offset +1 to compensate '...' empty page	**/
      	for($i = $i; $i < ($segment_size*3)+1; $i++)
      	{
      		$shown_page[$i] = ($max_page) - (2*$segment_size - $j); 
      		$j++;
      	}
      	
      }
      else
      {
      	/** distribute evenly	**/
      	for($i = 0;$i < ($segment_size); $i++)
      	{
      		$shown_page[$i] = $i; 
      	}
      	
      	$shown_page[$i++] = '...';
      
      	$j = -1*floor($segment_size/2);
		/**	add offset +1 to compensate '...' empty page	**/
      	for($i = $i;$i < (2*$segment_size)+1; $i++)
      	{
      		$shown_page[$i] = $current_page + $j;
      		$j++; 
      	}
      
      	$shown_page[$i++] = '...';
      	
      	$j = 0;
		/**	add offset +2 to compensate two '...' empty page	**/
      	for($i = $i; $i < ($segment_size*3)+2; $i++)
      	{
      		$shown_page[$i] = ($max_page) - ($segment_size - $j); 
      		$j++;
      	}
      }
      
      foreach($shown_page as $key => $content):
      ?>
   <span class="pagination-page<?php if($content == $current_page && is_numeric($content)){echo " active-page";} ?>" data-page="<?php echo $content;?>"><?php echo (is_numeric($content)) ? $content+1 : $content;?></span>
   <?php endforeach;?>
</div>
<div style="clear:both;"></div>
