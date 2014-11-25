<?php
/**	load the default configuration	**/
require(EMGL_HOME_DIR.'/config/config.php');

if($_POST['action'] == 'update')
{
	foreach($default_value as $key => $content)
	{
		update_option($key, $_POST[$key]);
	}
}

?>

<div class="wrap">
  <form method="post">
    <input type="hidden" name="action" value="update">
  <h2>General Settings</h2>
    <table class="form-table">
      <tbody>
        <tr valign="top">
          <th scope="row"><label for="blogname">Bounce Interval</label></th>
          <td><input name="emgl_bounce_interval" type="text" id="emgl_bounce_interval" value="<?php echo get_option('emgl_bounce_interval');?>" size="5" maxlength="5">Seconds
          <p class="description">Minimum interval between visit</p></td>
        </tr>
        <tr valign="top">
          <th scope="row"><label for="blogdescription">Active User Interval</label></th>
          <td><input name="emgl_active_visitor_interval" type="text" id="emgl_active_visitor_interval" value="<?php echo get_option('emgl_active_visitor_interval');?>" size="5" maxlength="5">Minutes
            <p class="description">Interval for Active User Calculation</p></td>
        </tr>
        <tr valign="top">
          <th scope="row"><label for="blogdescription">Spammer Threshold</label></th>
          <td><input name="emgl_spammer_threshold" type="text" id="emgl_spammer_threshold" value="<?php echo get_option('emgl_spammer_threshold');?>" size="3" maxlength="3">Percent
            <p class="description">Spammer Detection Threshold</p></td>
        </tr>
        <tr valign="top">
          <th scope="row"><label for="blogdescription">Maximum Data Period</label></th>
          <td><input name="emgl_max_data_keep" type="text" id="emgl_max_data_keep" value="<?php echo get_option('emgl_max_data_keep');?>" size="5" maxlength="5">Days
            <p class="description">Maximum time to keep visitor data</p></td>
        </tr>
         <tr valign="top">
          <th scope="row"><label for="blogname">Keep Request Content</label></th>
          <td><input name="emgl_visitor_meta" type="checkbox" id="emgl_visitor_meta" value="true" <?php echo (get_option('emgl_visitor_meta'))?'checked=\"checked\"':"";?>>Yes
          <p class="description">Store complete request content</p></td>
        </tr>
        
      </tbody>
    </table>
    <p class="submit">
      <input type="submit" name="submit" class="button button-primary" value="Save Changes">
    </p>
    
      <h2>Dashboard Settings</h2>
    <table class="form-table">
      <tbody>
        <tr valign="top">
          <th scope="row"><label for="blogname">Summary Duration</label></th>
          <td><input name="emgl_summary_duration" type="text" id="emgl_summary_duration" value="<?php echo get_option('emgl_summary_duration');?>" size="5" maxlength="5">Days
          <p class="description">duration for summary analysis</p></td>
        </tr>
        <tr valign="top">
          <th scope="row"><label for="blogname">Visitor Data Row</label></th>
          <td><input name="emgl_dashboard_visitor_row" type="text" id="emgl_dashboard_visitor_row" value="<?php echo get_option('emgl_dashboard_visitor_row', $default_value['emgl_dashboard_visitor_row']);?>" size="5" maxlength="5">Rows
          <p class="description">number of rows to be shown in visitor data</p></td>
        </tr>
      </tbody>
    </table>
    <p class="submit">
      <input type="submit" name="submit" class="button button-primary" value="Save Changes">
    </p>
  <h2>Widget Settings</h2>
    <table class="form-table">
      <tbody>
        <tr valign="top">
          <th scope="row"><label for="blogdescription">Visitor Data Row</label></th>
          <td><input name="emgl_visitor_data_row" type="text" id="emgl_visitor_data_row" value="<?php echo get_option('emgl_visitor_data_row', $default_value['emgl_visitor_data_row']);?>" size="5" maxlength="5">Rows
            <p class="description">Minimum number of real-time access row</p></td>
        </tr>
         <tr valign="top">
          <th scope="row"><label for="blogname">Show Real Time Feed</label></th>
          <td><input name="emgl_show_real_time_feed" type="checkbox" id="emgl_show_real_time_feed" value="true" <?php echo (get_option('emgl_show_real_time_feed'))?'checked=\"checked\"':"";?>>Show
          <p class="description">Show Real Time Feed on Widget</p></td>
        </tr>
        <tr valign="top">
          <th scope="row"><label for="blogdescription">Real-Time Feed Interval</label></th>
          <td><input name="emgl_update_interval" type="text" id="emgl_active_visitor_interval" value="<?php echo get_option('emgl_update_interval');?>" size="5" maxlength="5">Seconds
            <p class="description">Update Interval</p></td>
        </tr>
        <tr valign="top">
          <th scope="row"><label for="blogname">Show Active Visitor</label></th>
          <td><input name="emgl_show_active_visitor" type="checkbox" id="emgl_show_active_visitor" value="true" <?php echo (get_option('emgl_show_active_visitor'))?'checked=\"checked\"':"";?>>Show
          <p class="description">Show Visitor on Widget</p></td>
        </tr>
        <tr valign="top">
          <th scope="row"><label for="blogname">Show Page View</label></th>
          <td><input name="emgl_show_page_view" type="checkbox" id="emgl_show_page_view" value="true" <?php echo (get_option('emgl_show_page_view'))?'checked=\"checked\"':"";?>>Show
          <p class="description">Show Page View on Widget</p></td>
        </tr>
        <tr valign="top">
          <th scope="row"><label for="blogname">Show Visitor</label></th>
          <td><input name="emgl_show_visitor" type="checkbox" id="emgl_show_visitor" value="true" <?php echo (get_option('emgl_show_visitor'))?'checked=\"checked\"':"";?>>Show
          <p class="description">Show Visitor on Widget</p></td>
        </tr>
      </tbody>
    </table>
    <p class="submit">
      <input type="submit" name="submit" class="button button-primary" value="Save Changes">
    </p>
          <h2>Spammer Block Settings</h2>
    <table class="form-table">
      <tbody>
        <tr valign="top">
          <th scope="row"><label for="blogname">Spammer Detection Duration</label></th>
          <td><input name="emgl_spammer_analysis_duration" type="text" id="emgl_spammer_analysis_duration" value="<?php echo get_option('emgl_spammer_analysis_duration');?>" size="5" maxlength="5">Days
          <p class="description">duration to calculate list of spammer</p></td>
        </tr>
        <tr valign="top">
          <th scope="row"><label for="blogname">Spammer List Percentile</label></th>
          <td><input name="emgl_spammer_analysis_percentile" type="text" id="emgl_spammer_analysis_percentile" value="<?php echo get_option('emgl_spammer_analysis_percentile');?>" size="3" maxlength="3">Percent
          <p class="description">Higher percentile rank may increase false alarm</p></td>
        </tr>
        <tr valign="top">
          <th scope="row"><label for="blogname">Spammer Block Action</label></th>
          <td>
          <input name="emgl_spammer_block_action" type="radio" id="emgl_spammer_block_action" value="redirect" <?php if(get_option('emgl_spammer_block_action','redirect') == 'redirect') echo "checked";?>>Redirect
          <input name="emgl_spammer_block_action" type="radio" id="emgl_spammer_block_action" value="block" <?php if(get_option('emgl_spammer_block_action') == 'block') echo "checked";?>>Block
          <p class="description">Action against the spammer</p></td>
        </tr>
        <tr valign="top">
          <th scope="row"><label for="blogname">Spammer Redirection</label></th>
          <td><input name="emgl_spammer_redirection" type="text" id="emgl_spammer_redirection" value="<?php echo get_option('emgl_spammer_redirection', $default_value['emgl_spammer_redirection']);?>" class="regular-text">
          <p class="description">Redirect spammer to specific URL</p></td>
        </tr>
      </tbody>
    </table>
    <p class="submit">
      <input type="submit" name="submit" class="button button-primary" value="Save Changes">
    </p>
    
    <h2>Uninstall Settings</h2>
    <table class="form-table">
      <tbody>
        <tr valign="top">
          <th scope="row"><label for="blogname">Drop Table</label></th>
          <td><input name="emgl_uninstall_drop_table" type="checkbox" id="emgl_uninstall_drop_table" value="true" <?php echo (get_option('emgl_uninstall_drop_table'))?'checked=\"checked\"':"";?>>Yes
          <p class="description">Drop Guest List Tables</p></td>
        </tr>
        <tr valign="top">
          <th scope="row"><label for="blogname">Delete Options</label></th>
          <td><input name="emgl_uninstall_delete_option" type="checkbox" id="emgl_uninstall_delete_option" value="true" <?php echo (get_option('emgl_uninstall_delete_option'))?'checked=\"checked\"':"";?>>Yes
          <p class="description">Delete Guest List Options</p></td>
        </tr>
      </tbody>
    </table>
    <p class="submit">
      <input type="submit" name="submit" class="button button-primary" value="Save Changes">
    </p>
    <h2>Spammer Analysis</h2>
    <table class="form-table">
      <tbody>
        <tr valign="top">
          <th scope="row"><label for="blogname">Store Request Content</label></th>
          <td><input name="emgl_spammer_store_full" type="checkbox" id="emgl_spammer_store_full" value="true" <?php echo (get_option('emgl_spammer_store_full'))?'checked=\"checked\"':"";?>>Yes
          <p class="description">Store Full Spammer Request Content</p></td>
        </tr>
      </tbody>
    </table>
    <p class="submit">
      <input type="submit" name="submit" class="button button-primary" value="Save Changes">
    </p>

  </form>
</div>
