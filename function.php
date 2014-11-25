<?php

/**	attach block spammer function into init	**/
add_action('init', 'emgl_block_spammer_action');
add_action('shutdown', 'emgl_store_visitor_data_async');


/**	attach script load every time wp initialize	**/
add_action('wp_head', 'emgl_load_script');


/**	add ajax handler	**/
add_action('wp_ajax_emgl_get_visitor_data', 'emgl_get_visitor_data_ajax');
add_action('wp_ajax_nopriv_emgl_get_visitor_data', 'emgl_get_visitor_data_ajax');
add_action('wp_ajax_emgl_store_visitor_data', 'emgl_store_visitor_data_ajax');
add_action('wp_ajax_nopriv_emgl_store_visitor_data', 'emgl_store_visitor_data_ajax');
add_action('wp_ajax_emgl_get_rejection_ratio', 'emgl_get_rejection_ratio_ajax');
add_action('wp_ajax_emgl_cleanup_visitor_data', 'emgl_cleanup_visitor_data_ajax');
add_action('wp_ajax_emgl_spammer_analysis', 'emgl_spammer_analysis_hourly_ajax');


/**	add cron job to update spammer table hourly	**/
add_action('wp', 'emgl_scheduler');

/**	add function to attach to specific event	**/
add_action('spammer_analysis_event', 'emgl_spammer_analysis_hourly');
add_action('cleanup_visitor_data_event', 'emgl_cleanup_visitor_data_daily');
add_action('emgl_store_visitor_data_event', 'emgl_store_visitor_data');



function emgl_load_script()
{
	/**	load java script for widget	**/
    wp_enqueue_script('emgl-visitor-analytics', plugin_dir_url(__FILE__) . "/lib/js/widget.js", array(
        'jquery'
    ), '1.0', false);
}

function emgl_get_visitor_data_ajax()
{
	/**	ajax wrapper for get visitor data function	**/
    global $_POST;
    $parameter                = $_POST;
    $result                   = emgl_get_visitor_data($parameter);
    $result['last_id']        = $result['visitor_data'][0]->id;
    $result['last_timestamp'] = $result['visitor_data'][0]->trigger_timestamp;
    ob_start();
    include('template/real-time-feed-row.php');
    $result['visitor_data'] = ob_get_contents();
    ob_end_clean();
    
    echo json_encode($result);
}


function emgl_store_visitor_data_ajax()
{
	/**	ajax wrapper for store visitor data	**/
    global $_POST, $_SERVER;
    $parameter = $_POST;
    echo json_encode(emgl_store_visitor_data($parameter));
}

function emgl_store_visitor_data_async($data = array())
{
	$data['trigger_timestamp']   = date('Y-m-d H:i:s'); // Getting current Page Title
	$data['ip_address'] = $_SERVER['REMOTE_ADDR']; // Getting the user's computer IP
	$data['referer']    = $_SERVER['HTTP_REFERER']; // Getting the Referer
	$data['user_agent'] = $_SERVER['HTTP_USER_AGENT']; // Getting the Referer
	$data['page_url']   = $_SERVER['REQUEST_URI']; // Getting the Requested URI
	$data['page_title'] = emgl_get_page_title(); // Getting current Page Title
	
	if(get_option('emgl_visitor_meta') == true || (true == $data['block'] && true == get_option('emgl_spammer_store_full')))
	{
		/**	store the server variable if visitor meta option is true or if spammer is detected and store full content is enabled	**/
		$temp_data['server'] = $_SERVER;	//	get the server parameter
		$temp_data['post']  = $_POST;	//	get the post parameter
		
		$data['request_content'] = json_encode($temp_data); // store the parameter as string	
		
	}
	else
	{
		$data['request_content']   = ""; // Provide empty string parameter
	}
		

	if(true == $data['block'])
	{
		$data['block'] = true;
	}

	if(!current_user_can('edit_post') || true == $data['block'])
	{
		/**	do not store admin visit data	**/
		/**	always store block visitor	**/
		
		/**	execute store data asynchronously **/
		wp_schedule_single_event(time(), 'emgl_store_visitor_data_event', array(
			$data
		));

	}	
}

function emgl_store_visitor_data($data = array())
{
    //	get the visitor location
    try {
		/**	get geolocation using ip-api.com service	**/
        $ch = curl_init();
        //curl_setopt($ch, CURLOPT_URL, sprintf('ipinfo.io/%s/geo', $data['ip_address']));
        curl_setopt($ch, CURLOPT_URL, sprintf('http://ip-api.com/json/%s', $data['ip_address']));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $geolocation = curl_exec($ch);
        curl_close($ch);
        
        $geolocation = json_decode($geolocation, true);
    }
    catch (e $exception) {
        $geolocation = array();
    }
    
    $data['country'] = @($geolocation['country']) ? $geolocation['country'] : '';
    $data['city']    = @($geolocation['city']) ? $geolocation['city'] : '';
    $data['region']  = @($geolocation['region']) ? $geolocation['region'] : '';
    
    $data = emgl_check_spammer($data);
    $data = emgl_check_crawler($data);
    $data = emgl_check_human($data);
    
    global $wpdb;
    
    /**	insert visitor information into table **/
    
    $option['bounce_interval'] = get_option('emgl_bounce_interval', 30);
	
	if($data['block'] == true)
	{
		$wpdb->query($wpdb->prepare("INSERT INTO `" . EMGL_TABLE_BLOCK_VISITOR_LOG . "` (ip_address, country, city, region, referer, user_agent, page_url, page_title, trigger_timestamp, spammer_flag, human_flag, crawler_flag, request_content) SELECT %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s FROM DUAL WHERE NOT EXISTS (SELECT * FROM `" . EMGL_TABLE_BLOCK_VISITOR_LOG . "` WHERE ip_address =%s AND page_url = %s AND referer = %s AND trigger_timestamp >= DATE_SUB(NOW(), INTERVAL %d SECOND))", $data['ip_address'], $data['country'], $data['city'], $data['region'], $data['referer'], $data['user_agent'], $data['page_url'], $data['page_title'], $data['trigger_timestamp'], $data['spammer_flag'], $data['human_flag'], $data['crawler_flag'], $data['request_content'], $data['ip_address'], $data['page_url'], $data['referer'], $option['bounce_interval']));

	}
	else
	{
    $wpdb->query($wpdb->prepare("INSERT INTO `" . EMGL_TABLE_VISITOR_LOG . "` (ip_address, country, city, region, referer, user_agent, page_url, page_title, trigger_timestamp, spammer_flag, human_flag, crawler_flag, request_content) SELECT %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s FROM DUAL WHERE NOT EXISTS (SELECT * FROM `" . EMGL_TABLE_VISITOR_LOG . "` WHERE ip_address =%s AND page_url = %s AND referer = %s AND trigger_timestamp >= DATE_SUB(NOW(), INTERVAL %d SECOND))", $data['ip_address'], $data['country'], $data['city'], $data['region'], $data['referer'], $data['user_agent'], $data['page_url'], $data['page_title'], $data['trigger_timestamp'], $data['spammer_flag'], $data['human_flag'], $data['crawler_flag'], $data['request_content'], $data['ip_address'], $data['page_url'], $data['referer'], $option['bounce_interval']));
	}   
	
	/**	insert spammer data to spammer list table	**/	
	if($data['spammer_flag'] == 1)
	{
		emgl_insert_spammer_list($data);	
	}

    return $data;
}


function emgl_get_visitor_data($parameter = array())
{
    
    $last_index = @$parameter['last_index'] ? $parameter['last_index'] : 0;
    
    /**	retrieve last visitor access data **/
    global $wpdb;
    
    $option['access_data_row'] = get_option('emgl_visitor_data_row', 5);
    if ($last_index > 0) {
        $result['visitor_data'] = $wpdb->get_results($wpdb->prepare("SELECT * FROM `" . EMGL_TABLE_VISITOR_LOG . "` WHERE id > %d ORDER BY id DESC LIMIT %d ", $last_index, $option['access_data_row']));
    } else {
        $result['visitor_data'] = $wpdb->get_results($wpdb->prepare("SELECT * FROM `" . EMGL_TABLE_VISITOR_LOG . "` ORDER BY id DESC LIMIT %d ", $option['access_data_row']));
    }
    
    foreach ($result['visitor_data'] as $key1 => $content1) {
        $result['visitor_data'][$key1] = emgl_get_referer_type($content1);
    }
    
    /**	retrieve active visitor data **/    
    $option['active_visitor_interval'] = get_option('emgl_active_visitor_interval', 10);
    $result['active_visitor']          = $wpdb->get_results($wpdb->prepare("SELECT COUNT(DISTINCT(ip_address)) AS active_visitor FROM `" . EMGL_TABLE_VISITOR_LOG . "` WHERE trigger_timestamp >= DATE_SUB(NOW(), INTERVAL %d MINUTE)", $option['active_visitor_interval']));
    
    /**	retrieve total page view today **/   
    $result['total_page_view'] = $wpdb->get_results(sprintf("SELECT COUNT(ip_address) AS total_page_view FROM `%s` WHERE DATE(trigger_timestamp) = DATE(NOW())", EMGL_TABLE_VISITOR_LOG));
    
    /**	retrieve total visitor today **/    
    $result['total_visitor'] = $wpdb->get_results(sprintf("SELECT COUNT(DISTINCT(ip_address)) AS total_visitor FROM `%s` WHERE DATE(trigger_timestamp) = DATE(NOW())", EMGL_TABLE_VISITOR_LOG));
    
    return $result;
    
}

function emgl_get_referer_type($parameter)
{
    $user_agent = @$parameter->user_agent ? $parameter->user_agent : "";
    $ip_address = @$parameter->ip_address ? $parameter->ip_address : "";
    $referer    = @$parameter->referer ? $parameter->referer : "";
    
    //	check for the referer
    if ($referer !== "") {
        //	if referer is exist then extract the domain only
        preg_match_all('/http[s]*:\/\/([A-Za-z0-9.]+?)\//', $referer, $match);
        $referer = (!is_null($match[1][0])) ? $match[1][0] : "";
        
        $parameter->referer = $referer;
    }
    //	check for the user agent
    elseif ($user_agent !== "") {
        if (preg_match_all('/([\w]*?(bot|crawler|spider)[\w]*?)/i', $user_agent, $match)) {
            //	check for user agent that contain bot-like identity
            $parameter->referer = $match[1][0];
        } else {
            //	if no bot-like identity matched then set as human
            $parameter->referer = "";
        }
    }
    //	default action is set ip address as referer
    else {
        $parameter->referer = $ip_address;
    }
    
    return $parameter;
    
}

function emgl_check_crawler($data = array())
{
    $user_agent = @($data['user_agent']) ? $data['user_agent'] : "";
    try {
        if (preg_match_all('/([\w]*?(bot|crawler|spider)[\w]*?)/i', $user_agent, $match)) {
            //	check for user agent that contain bot-like identity
            $data['crawler_flag'] = 1;
        } else {
            //	if no bot-like identity matched then set as human
            $data['crawler_flag'] = 0;
        }
    }
    catch (e $exception) {
        $data['crawler_flag'] = 0;
    }
    
    return $data;
    
}

function emgl_check_spammer($data = array())
{
	/**	check if visitor are listed on online spammer blacklist	**/
    $ip_address = @($data['ip_address']) ? $data['ip_address'] : "";
    try {
        /**	create curl to specific API to check spammer	**/
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://api.stopforumspam.org/api?ip=' . $ip_address . '&f=json');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $geolocation = curl_exec($ch);
        curl_close($ch);
        
        /**	check the confidence level of spammer data	**/
        $spammer = json_decode($geolocation, true);
        if ($spammer['ip']['confidence'] > (float) get_option('emgl_spammer_threshold')) {
            $data['spammer_flag'] = 1;
        } else {
            $data['spammer_flag'] = 0;
        }
    }
    catch (e $exception) {
        $data['spammer_flag'] = 0;
    }
    
    return $data;
    
}

function emgl_check_human($data = array())
{
    if ($data['spammer_flag'] == 1) {
        $data['human_flag'] = 0;
    } elseif ($data['crawler_flag'] == 1) {
        $data['human_flag'] = 0;
    } else {
        $data['human_flag'] = 1;
    }
    return $data;
}

function emgl_get_visitor_type($data = stdClass)
{
    if ($data->spammer_flag == 1) {
        return 'spammer';
    } elseif ($data->crawler_flag == 1) {
        return 'crawler';
    } else {
        return 'human';
    }
    
    return;
}

function emgl_cleanup_visitor_data_ajax()
{
    $cleanup_result = emgl_cleanup_visitor_data();
    if ($cleanup_result !== false) {
        $result['status']  = 'success';
        $result['message'] = sprintf("success cleanup %d data", $cleanup_result);
    } else {
        $result['status']  = 'error';
        $result['message'] = "error cleanup data";
    }
    
    echo json_encode($result);
}


function emgl_cleanup_visitor_data()
{
    global $wpdb;
    
    $visitor_data_period = get_option('emgl_max_data_keep', 15);
    $sql                 = sprintf("DELETE FROM `%s` WHERE DATE(trigger_timestamp) < DATE_SUB(CURDATE(), INTERVAL %d DAY)", EMGL_TABLE_VISITOR_LOG, $visitor_data_period);
    $cleanup_result      = $wpdb->query($sql);
    
    if ($cleanup_result === false) {
        return false;
    } else {
        return $cleanup_result;
    }
}

function emgl_cleanup_visitor_data_daily()
{
	emgl_cleanup_visitor_data();
}


/**	register all scheduled event	**/
function emgl_scheduler()
{
    if (!wp_next_scheduled('spammer_analysis_event')) {
        wp_schedule_event(time(), 'hourly', 'spammer_analysis_event');
    }
    if (!wp_next_scheduled('cleanup_visitor_data_event')) {
        wp_schedule_event(time(), 'daily', 'cleanup_visitor_data_event');
    }
}

function emgl_spammer_analysis_hourly()
{
    /**	function to create spammer analysis	**/
    
    global $wpdb;
    /**	select count of spammer ip address	**/
    $spammer_analysis_duration   = get_option('emgl_spammer_analysis_duration', 30);
    $spammer_analysis_percentile = get_option('emgl_spammer_analysis_percentile', 90);
    
    /**	create temporary table	**/
    if ($wpdb->get_var('SHOW TABLES LIKE "' . EMGL_TABLE_SPAMMER_LIST_TEMP . '"') != EMGL_TABLE_SPAMMER_LIST_TEMP) {
        
        $sql = sprintf(file_get_contents(__DIR__."/template/sql/spammer-list.sql"),EMGL_TABLE_SPAMMER_LIST_TEMP); 
        $wpdb->query($sql);
    }
    
    /**	get the spammer data list	**/
    $spammer_count = $wpdb->get_results($wpdb->prepare("SELECT 
   ip_address,
   COUNT(ip_address) AS visit_count
   FROM `" . EMGL_TABLE_VISITOR_LOG . "` 
   WHERE DATE(trigger_timestamp) > DATE_SUB(CURDATE(), INTERVAL %d DAY)
   AND spammer_flag = 1
   GROUP BY ip_address 
   ORDER BY visit_count DESC", $spammer_analysis_duration));
    
	
	/**	get threshold visit count based on percentile rank	**/
	$threshold_rank = ceil($spammer_analysis_percentile * sizeof($spammer_count) / 100) - 1;
	$threshold_visit = $spammer_count[$threshold_rank]->visit_count;
    
    $value_sql = "";
	
	/**	create the values sql query	**/
    
    for ($i = 0; $i < sizeof($spammer_count); $i++) {
		if ($spammer_count[$i]->visit_count >= $threshold_visit)
		{
			/**	if visit count larger than minimum threshold then insert into database	**/
        	$value_sql .= sprintf("('%s'),", $spammer_count[$i]->ip_address);
		}
		else
		{
			/**	if visit count less than minimum then break the loop	**/
			break;	
		}
    }
    
    $value_sql = rtrim($value_sql, ",");
    
    /**	insert spammer list	**/
    $sql            = sprintf("INSERT INTO `" . EMGL_TABLE_SPAMMER_LIST_TEMP . "` (ip_address) VALUES %s", $value_sql);
    $spammer_result = $wpdb->query($sql);
    
    /**	switch temporary table into active	**/
    $sql          = sprintf("RENAME TABLE `%s` TO `%s`,
   `%s` TO `%s`", EMGL_TABLE_SPAMMER_LIST, EMGL_TABLE_SWITCH_TEMP, EMGL_TABLE_SPAMMER_LIST_TEMP, EMGL_TABLE_SPAMMER_LIST);
    $query_result = $wpdb->query($sql);
    
    
    /**	drop swich temp table	**/
    $sql          = sprintf("DROP TABLE `%s`", EMGL_TABLE_SWITCH_TEMP);
    $query_result = $wpdb->query($sql);
    
    return $query_result;
}

/**	create ajax handler for spammer analysis	**/
function emgl_spammer_analysis_hourly_ajax()
{
    $spammer_analysis = emgl_spammer_analysis_hourly();
    if ($spammer_analysis !== false) {
        $result['status']  = 'success';
        $result['message'] = "success execute spammer analysis";
    } else {
        $result['status']  = 'error';
        $result['message'] = "error execute spammer analysis";
    }
    
    echo json_encode($result);
}


/**	function to implement action against spammer	**/
function emgl_block_spammer_action()
{
	if(is_admin())
	{
		return;	
	}
	
	global $wpdb;
	
	$ip_address = $_SERVER['REMOTE_ADDR'];
	
	/**	check if ip address listed as spammer	**/
	$sql = sprintf("SELECT * FROM `".EMGL_TABLE_SPAMMER_LIST."` WHERE ip_address = '%s'", $ip_address);
	$query_result = $wpdb->query($sql);
	
	if($query_result == 0)
	{
		/**	if not found then skip process	**/
		return;	
	}
	
		$data = array();
		$data['block'] = true;	//	mark the request as blocked
		emgl_store_visitor_data_async($data);
	
	$spammer_block_action = get_option('emgl_spammer_block_action');
	if($spammer_block_action == 'redirect')
	{
		/**	redirect spammer to specific URL	**/
		$redirect_to = sprintf("Location: %s", get_option('emgl_spammer_redirection', "https://www.google.co.id"));
		header($redirect_to);
		exit;
	}
	elseif($spammer_block_action == 'block')
	{
		/**	block spammer completely	**/
		header('HTTP/1.0 403 Forbidden');
		exit;
	}
}

function emgl_insert_spammer_list($data = array())
{
	global $wpdb;
	$ip_address = $data['ip_address'];
    /**	insert spammer list	**/
    $spammer_result = $wpdb->query($wpdb->prepare("INSERT IGNORE INTO `" . EMGL_TABLE_SPAMMER_LIST . "` (ip_address) VALUES (%s)", $ip_address));
	
	if($spammer_result != false)
	{
		return true;	
	}
	else
	{
		return false;	
	}
	
}

function emgl_get_rejection_ratio_ajax()
{
	/**	ajax wrapper for spammer rejection ratio	**/
	$parameter = $_POST;
	$result['data'] = emgl_get_rejection_ratio($parameter);
	echo json_encode($result);	
}

function emgl_get_rejection_ratio($parameter = array())
{
	/**	get data for total rejection	**/
   $summary_duration = get_option('emgl_summary_duration');
	
	global $wpdb;
	
	/**	set default interval as daily	**/
	$interval = @$parameter['interval'] ? $parameter['interval'] : 86400; 
	
	$sql = $wpdb->prepare("SELECT 
	A.timekey*%d AS time, 
	SUM(A.passed_spammer) AS passed_spammer, 
	SUM(A.spammer) AS total_spammer, 
	100*SUM(A.passed_spammer)/SUM(A.spammer) AS passed_percentage, 
	100 - (100*SUM(A.passed_spammer)/SUM(A.spammer)) AS rejection_ratio 
	FROM 
	(SELECT FLOOR(UNIX_TIMESTAMP(trigger_timestamp)/(%d)) AS timekey, SUM(spammer_flag) AS spammer, 0 AS passed_spammer FROM `".EMGL_TABLE_BLOCK_VISITOR_LOG."` GROUP BY timekey 
	UNION 
	SELECT FLOOR (UNIX_TIMESTAMP(trigger_timestamp)/(%d)) AS timekey, SUM(spammer_flag) AS spammer, SUM(spammer_flag) AS passed_spammer FROM `".EMGL_TABLE_VISITOR_LOG."` GROUP BY timekey) A
WHERE FROM_UNIXTIME(A.timekey*%d) > DATE_SUB(NOW(), INTERVAL %d DAY) 
GROUP BY time", $interval, $interval, $interval, $interval, $summary_duration);
	
	$query_result = $wpdb->get_results($sql);
	
	return $query_result;	
}


function emgl_enqueue_admin($hook) {
	/**	enqueue script on admin panel only	**/
    if ( 'emgl_top_menu' != $hook ) {
        //return;
    }

    wp_enqueue_script( 'emgl_dashboard_script', plugin_dir_url( __FILE__ ) . 'lib/js/dashboard.js' );
    wp_enqueue_script( 'emgl_chart', plugin_dir_url( __FILE__ ) . 'lib/js/highcharts/highcharts.js' );
}
add_action( 'admin_enqueue_scripts', 'emgl_enqueue_admin' );

function emgl_get_page_title()
{
	if( empty( $title ) && ( is_home() || is_front_page() ) ) {
    	$title = wp_title('', FALSE);
  	}
	else
	{
		$title = wp_title('', FALSE);	
	}
	return $title;
}

?>