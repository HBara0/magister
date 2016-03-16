<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Functions file
 * $id: functions.php
 * Created: 	@zaher.reda		Mar 16, 2009 | 09:48 AM
 * Last Update: @zaher.reda 	May 20, 2012 | 09:29 AM
 */
/**
 * Stripslases for a given template and then returns it
 * @param  String		$template 	String to be striped
 * @return String	 				Striped template
 */
function output_page($pagecontent, $options = null) {// default tpl, options to enforce a customised tpl
    global $core, $lang, $timer, $template, $header, $footer, $headerinc, $rightsidemenu, $additionalheaderinc;

    $pagetitle = '';
    if(isset($options['pagetitle']) && !empty($options['pagetitle'])) {
        $pagetitle = $lang->$options['pagetitle'];
    }
    else if(!empty($core->input['module'])) {
        $files = explode("/", $core->input['module']);
        if(is_array($files) && !empty($files[1])) {
            $pagetitle = $lang->$files[1];
        }
    }
    ${$options['helptourref'].'_helptour'} = get_helptour('newlayout');
    eval("\$template= \"".$template->get('defaulttpl')."\";");

    $template = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n".$template;
    $template = str_replace("<html", "<html xmlns=\"http://www.w3.org/1999/xhtml\"", $template);

    if($lang->settings['rtl'] == 1) {
        $template = str_replace("<html", "<html dir=\"rtl\"", $template);
    }

    $template = str_replace("<html", "<html xml:lang=\"{$lang->settings[htmllang]}\" lang=\"{$lang->settings[htmllang]}\"", $template);

    $timer->stop();

    $debug = 'Version '.SYSTEMVERSION.'<br />';
    if($core->usergroup['canPerformMaintenance'] == 1) {
        $debug .= 'Generated in '.$timer->get().' seconds';
    }
    $template = str_replace("<debug>", $debug, $template);

    if($core->settings['enablecompression'] == 1) {
        if(version_compare(PHP_VERSION, '4.2.0', '>=')) {
            $template = gzip_compression($template, $core->settings['gziplevel']);
        }
        else {
            $template = gzip_compression($template);
        }
    }

    @header("Content-type: text/html; charset={$lang->settings[charset]}");
    echo $template;
}

function output($output) {
    global $core;
    if($core->settings['enablecompression'] == 1) {
        if(version_compare(PHP_VERSION, '4.2.0', '>=')) {
            $output = gzip_compression($output, $core->settings['gziplevel']);
        }
        else {
            $output = gzip_compression($output);
        }
    }
    echo $output;
}

/**
 * GZIP cotents to a certain level
 * @param  String		$contents 	Contents to be zipped
 * @param  int			$level	 	Level of compression
 * @return String					Compressed content
 */
function gzip_compression($contents, $level = 1) {
    if(function_exists('gzcompress') && function_exists('crc32') && !headers_sent() && !(ini_get('output_buffering') && strpos(' '.ini_get('output_handler'), 'ob_gzhandler'))) {
        $httpaccept_encoding = '';

        if(isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
            $httpaccept_encoding = $_SERVER['HTTP_ACCEPT_ENCODING'];
        }

        if(strpos(' '.$httpaccept_encoding, 'x-gzip')) {
            $encoding = 'x-gzip';
        }

        if(strpos(" ".$httpaccept_encoding, 'gzip')) {
            $encoding = 'gzip';
        }

        if(strpos(' '.$httpaccept_encoding, 'deflate')) {
            $encoding = 'deflate';
        }

        if(isset($encoding)) {
            header("Content-Encoding: {$encoding}");
            if(function_exists('gzdeflate')) {
                $contents = gzdeflate($contents, $level);
            }
            elseif(function_exists('gzencode')) {
                $contents = gzencode($contents, $level);
            }
            else {
                $size = strlen($contents);
                $crc = crc32($contents);
                $gzdata = "\x1f\x8b\x08\x00\x00\x00\x00\x00\x00\xff";
                $gzdata .= substr(gzcompress($contents, $level), 2, -4);
                $gzdata .= pack("V", $crc);
                $gzdata .= pack("V", $size);
                $contents = $gzdata;
            }
        }
    }
    return $contents;
}

/**
 * Outputs XML
 * @param	string		$xml		XML content
 * @return  String					Formated XML
 */
function output_xml($xml) {
    global $lang;
    ob_clean();

    header('Content-type: text/xml');
    echo '<?xml version="1.0" encoding="'.$lang->settings['charset'].'"?>';
    echo '<xml>'.$xml.'</xml>';
}

/**
 * Prepare and set correct headers
 */
function set_headers() {
    header("Expires: Sat, 1 Jan 2000 01:00:00 GMT");
    header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache");
}

/**
 * Generates a random number
 * @param	int		$length		Length of the random string
 * @return  String	$output		Random string of characters and numbers
 */
function random_string($length) {
    $keys = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $max = strlen($keys) - 1;

    for($i = 0; $i < $length; $i++) {
        $rand = rand(0, $max);
        $rand_key[] = $keys{$rand};
    }

    $output = implode('', $rand_key);

    return $output;
}

/**
 * Retrive the user's session IP address
 * @return  String 				The IP Address
 */
function userip() {
    if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) { //Let's catch the proxy behind the nat router.
        if(preg_match_all("#[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}#s", $_SERVER['HTTP_X_FORWARDED_FOR'], $addr)) {
            foreach($addr[0] as $key => $value) {
                if(!preg_match("#^(10|172\.16|192\.168)\.#", $value)) {
                    $ip = $value;
                    break;
                }
            }
        }
    }
    if(!$ip || $ip == '') {
        if($_SERVER['REMOTE_ADDR']) {
            if(preg_match_all("#[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}#s", $_SERVER['REMOTE_ADDR'], $addr)) {
                foreach($addr[0] as $key => $value) {
                    if(!preg_match("#^(10|172\.16|192\.168)\.#", $value)) {
                        $ip = $value;
                        break;
                    }
                }
            }
        }
        else {
            if(preg_match_all("#[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}#s", $_SERVER['HTTP_CLIENT_IP'], $addr)) {
                foreach($addr[0] as $key => $value) {
                    if(!preg_match("#^(10|172\.16|192\.168)\.#", $value)) {
                        $ip = $value;
                        break;
                    }
                }
            }
        }
    }
    return $ip;
}

/**
 * Redirects to a new page
 * @param	String		$url		The URL to which should be directed
 * @param	int			$delay		Seconds to deplay redirection
 */
function redirect($url, $delay = 0, $redirect_message = '') {
    global $core, $lang, $template, $headerinc;
    $url = str_replace("&amp;", "&", $url);
    $url = str_replace("#", "&#", $url);
    if(!empty($redirect_message)) {
        eval("\$redirectpage = \"".$template->get('redirect')."\";");
        output_page($redirectpage);
        exit;
    }
    else {
        if($delay > 0) {
            header("Refresh: $delay; url=$url");
        }
        else {
            header("Location: $url");
        }
    }
    exit;
}

/**
 * Validates if an email address structure is valid
 * @param	String		$email			The email address to be checked
 * @return	Boolean						Either valid or not
 */
function isvalid_email($email) {
    if(strpos($email, ' ') !== false) {
        return false;
    }

    if(function_exists('filter_var')) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
    else {
        return preg_match("/^[a-zA-Z0-9&*+\-_.{}~^\?=\/]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9.-]+$/si", $email);
    }
}

/**
 * Custom htmlspecialchars (for unicode)
 * @param 	String 		$text	The string to format
 */
function chtmlspecialchars(&$text) {
    if(!is_array($text)) {
        $text = preg_replace("#&(?!\#[0-9]+;)#si", "&amp;", $text);
        $text = str_replace("<", "&lt;", $text);
        $text = str_replace(">", "&gt;", $text);
        $text = str_replace("\"", "&quot;", $text);
    }
}

/**
 * Parse OCODE [BB Code] into corresponding format
 * @param	String		$text		Text to be parsed
 * @return  String		$text		Parse text
 */
function parse_ocode(&$text) {
    if(!is_array($text)) {
        $text = preg_replace("#\[b\](.*?)\[/b\]#si", "<span style='font-weight: bold;'>$1</span>", $text);
        $text = preg_replace("#\[i\](.*?)\[/i\]#si", "<span style='font-style: italic;'>$1</span>", $text);
        $text = preg_replace("#\[u\](.*?)\[/u\]#si", "<span style='text-decoration: underline;'>$1</span>", $text);
    }
}

function fix_newline(&$text) {
    if(!is_array($text)) {
        $text = preg_replace("/\\n/i", '<br />', $text);
    }
}

/**
 * Displays a customized error message
 * @param	String		$message		The message to be displayed
 * @param	Boolean		$noexit			Don't stop execution or vice versa
 */
function error($message, $redirect_url = '', $noexit = false) {
    global $core, $template, $lang, $config, $settings, $headerinc;

    $error_message = $message;
    if(!empty($redirect_url)) {
        $redirect = '<meta http-equiv="refresh" content="3;URL='.$redirect_url.'" />';
    }
    eval("\$errorpage = \"".$template->get('errorpage')."\";");
    output_page($errorpage);

    if($noexit == false) {
        exit;
    }
}

/**
 * Creates a cookie
 * @param  String		$name 		Name of the cookie
 * @param  String		$value	 	Value of the cookie
 * @param  int			$duration	Expiry
 */
function create_cookie($name, $value, $duration = '', $secure = false, $httponly = false) {
    global $core;

    if(!is_array($value)) {
        if(empty($duration)) {
            $duration = (time() + (60 * $core->settings['idletime']));
        }
        setcookie(COOKIE_PREFIX.$name, urlencode($value), $duration, COOKIE_PATH, COOKIE_DOMAIN, $secure, $httponly);
    }
}

/**
 * Parse input fields of various types
 */
function parse_textfield($name, $id, $type, $value = '', $options = array(), $config = array()) {
    if(empty($id)) {
        return false;
    }

    if(!empty($options)) {
        foreach($options as $key => $val) {
            $attributes.= $key.'="'.$val.'"';
        }
    }

    $accepted_types = array('text', 'tel', 'number', 'search', 'url', 'email', 'datetime', 'date', 'month', 'week', 'time', 'checkbox', 'image', 'file');
    if(!array($accepted_types, $type)) {
        $type = 'text';
    }

    if(isset($config['id'])) {
        $id = $config['id'];
    }

    $text = '<input type="'.$type.'" name="'.$name.'" value="'.$value.'" id="'.$id.'"'.$attributes.'>';
    return $text;
}

/**
 * Parses a select list
 * @param type $name
 * @param type $tabindex
 * @param type $options
 * @param type $selected_options
 * @param type $multiple_list
 * @param string $onchange_actions
 * @param type $config
 * @return string
 */
function parse_selectlist($name, $tabindex, $options, $selected_options, $multiple_list = 0, $onchange_actions = '', $config = array()) {
    if(!is_array($options)) {
        return;
    }
    if($multiple_list == 1) {
        if(!isset($config['multiplesize']) || empty($config['multiplesize'])) {
            $config['multiplesize'] = 5;
        }

        $config['size'] = $config['multiplesize'];
        $multiple = ' multiple="multiple" SIZE="'.$config['multiplesize'].'"';
    }

    if(is_array($selected_options)) {
        $multiple_selected = true;
    }
    if(!empty($onchange_actions)) {
        $onchange_actions = ' onchange=\''.$onchange_actions.'\'';
    }

    $id = $name;
    if(isset($config['id'])) {
        $id = $config['id'];
    }
    $disabled = '';
    if(isset($config['disabled'])) {
        $disabled = '  disabled="'.$config['disabled'].'" ';
    }
    if(isset($config['required']) && ($config['required'] == true || $config['required'] == 'required')) {
        $required = ' required = "required"';
    }

    if(!isset($config['size']) && $multiple_list != 1) {
        $config['size'] = 1;
    }

    if(isset($config['width'])) {
        $list_style = 'width: '.$config['width'].';';
    }
    if(isset($config['data_attribute'])) {
        $datattr = $config['data_attribute'].';';
    }

    if(isset($config['class'])) {
        $list_class = ' class="'.$config['class'].'" ';
    }

    $list .= '<select style="'.$list_style.'" id="'.$id.'" name="'.$name.'" '.$disabled.' size="'.$config['size'].'" tabindex="'.$tabindex.'"'.$required.$multiple.$onchange_actions.$datattr.$list_class.'>';

    if($config['blankstart'] == true && empty($config['placeholder'])) {
        $list .= '<option></option>';
    }

    if(!empty($config['placeholder'])) {
        if(empty($selected_options)) {
            $placeholder_selected = ' selected';
        }
        $list .= '<option disabled value="0"'.$placeholder_selected.'>'.$config['placeholder'].'</option>';
        unset($placeholder_selected);
    }
    foreach($options as $key => $val) {
        if(is_object($val)) {
            if(method_exists($val, 'get_id')) {
                $key = $val->get_id();
            }
            $val = $val->get_displayname();
        }

        if($multiple_selected == true) {
            $selected_options = array_filter($selected_options, 'strlen');
            if(in_array($key, $selected_options)) {
                $attributes = ' selected="selected"';
                $selected = true;
            }
        }
        else {
            if($selected_options == $key && $selected_options != null) {
                $attributes = ' selected="selected"';
                $selected = true;
            }
        }
        if(isset($config['disabledNonSelectedItems']) && $config['disabledNonSelectedItems'] == 1 && $selected != true) {
            $attributes .= ' disabled="disabled"';
        }
        elseif(isset($config['disabledItems'][$key]) && $selected != true) {
            $attributes .= ' disabled="disabled"';
        }
        if(isset($config['optionids']) && is_array($config['optionids'])) {
            if(is_array($config['optionids']['when'])) {
                if(array_key_exists($key, $config['optionids']['when'])) {
                    $attributes.=' id='.$config['optionids']['id'];
                }
            }
        }
        $list .= '<option value="'.$key.'"'.$attributes.'>'.$val.'</option>';
        $attributes = $selected = '';
    }
    $list .= '</select>';

    return $list;
}

function parse_yesno($name, $tabindex, $checked_option = 0) {
    global $lang;

    if($checked_option == '1') {
        $yes_checked = ' checked="checked"';
    }
    else {
        $no_checked = ' checked="checked"';
    }

    $radio = "<label><input type='radio' name='{$name}' value='1' id='{$name}_1'{$yes_checked}>{$lang->yes}</label>";
    $radio .= "<label><input type='radio' name='{$name}' value='0' id='{$name}_0'{$no_checked}>{$lang->no}</label>";
    return $radio;
}

function parse_radiobutton($name, $items, $checked_option = '', $display_title = true, $seperator = '', $config = array()) {
    if(is_array($items)) {
        foreach($items as $key => $val) {
            $checked = '';
            if($display_title === false) {
                $val = '';
            }

            if($key == $checked_option) {
                $checked = ' checked="checked"';
            }

            if($config['required']) {
                $required = ' required = "required"';
            }

            $radio .= '<input type="radio" name="'.$name.'" value="'.$key.'" id="'.$name.'_'.$key.'"'.$checked.$required.'/> '.$val.$seperator;
        }
        return $radio;
    }

    return false;
}

function parse_checkboxes($name, $items, $selected_options = array(), $display_title = true, $title = '', $seperator = '', $ide = '', $tabindex = '') {
    if(is_array($items)) {
        if(!empty($tabindex)) {
            $tabindex = 'tabindex='.$tabindex;
        }
        if(!empty($ide)) {
            $ids = $ide;
        }
        foreach($items as $key => $val) {
            $checked = '';
            if($display_title === false) {
                $val = '';
            }

            if(is_array($selected_options)) {
                if(in_array($key, $selected_options)) {
                    $checked = ' checked="checked"';
                }
            }
            if(isset($ids) && !empty($ids)) {
                $id = $ids;
            }
            else {
                $id = $name.'_'.$key;
            }
            $checkbox .= '<input name="'.$name.'['.$key.']" id="'.$id.'" type="checkbox" '.$tabindex.' title="'.$title.'" value="'.$key.'"'.$checked.'/>'.$val.$seperator;
        }
        return $checkbox;
    }
    return false;
}

function value_exists($table, $attribute, $value, $extra_where = '') {
    global $db;

    if(!empty($extra_where)) {
        $extra_where = ' AND '.$extra_where;
    }
    $attribute = $db->escape_string($attribute);
    $query = $db->query("SELECT {$attribute} FROM ".Tprefix."{$table} WHERE {$attribute}='".$db->escape_string($value)."'{$extra_where}");
    if($db->num_rows($query) > 0) {
        return true;
    }
    else {
        return false;
    }
}

function get_specificdata($table, $attributes, $key_attribute, $value_attribute, $order, $blankstart = 0, $where = '') {
    global $db;
    if(is_array($attributes)) {
        foreach($attributes as $key => $val) {
            $attributes_string .= $comma.$val;
            $comma = ', ';
        }
    }
    else {
        $attributes_string = $attributes;
    }

    if(is_array($order)) {
        if(!isset($order['sort']) || empty($order['sort'])) {
            $order['sort'] = 'ASC';
        }
        $order = 'ORDER BY '.$order['by'].' '.$order['sort'];
    }
    else {
        if(!empty($order)) {
            $order = 'ORDER BY '.$order.' ASC';
        }
    }

    if(!empty($where)) {
        $where = 'WHERE '.$where.' ';
    }

    $query = $db->query("SELECT {$attributes_string} FROM ".Tprefix."{$table} {$where}{$order}");

    if($db->num_rows($query) > 0) {
        if($blankstart == 1) {
            $data[0] = '';
        }

        while($result = $db->fetch_array($query)) {
            if($key_attribute == '0') {
                $result[$key_attribute] = 0;
            }
            $data[$result[$key_attribute]] = $result[$value_attribute];
        }
        return $data;
    }
    else {
        return false;
    }
}

function quick_search($table, $attributes, $value, $select_attributes, $key_attribute, $options = array(), $andor_param = 'OR') {
    global $db, $lang;

    $value = $db->escape_string($value);
    if(is_array($select_attributes)) {
        foreach($select_attributes as $key => $val) {
            $select_attributes_string .= $comma.$val;
            $comma = ', ';
        }
        $select_attributes_string .= ', '.$key_attribute;
    }
    else {
        return false;
    }

    if(is_array($attributes)) {
        foreach($attributes as $key => $val) {
            $where_string .= $andor.' '.$val.' LIKE "%'.$value.'%"';
            if($options['disableSoundex'] != 1) {
                $soundex_where_string .= "{$andor}SOUNDEX({$val}) = SOUNDEX('$value')";
            }
            $andor = ' '.$andor_param.' ';
        }
    }
    else {
        return false;
    }

    if(is_array($options['order'])) {
        $order = ' '.$db->escape_string('ORDER BY '.$options['order']['by'].' '.$options['order']['sort']);
    }
    $extra_where_string = '';
    if(!empty($options['extra_where'])) {
        $extra_where_string = ' AND '.$options['extra_where'];
    }
    $query = $db->query("SELECT {$select_attributes_string} FROM ".Tprefix."{$table} WHERE ({$where_string}){$extra_where_string} {$order}");

    $clean_key_attribute = $key_attribute;
    if(strstr($key_attribute, '.')) {
        $key_attribute_parts = explode('.', $key_attribute);
        $clean_key_attribute = $key_attribute_parts[1];
    }

    if($db->num_rows($query) > 0) {
//$results_list .= "<ul id='searchResultsList'>";
        while($result = $db->fetch_assoc($query)) {
            $space = '';
            foreach($select_attributes as $key => $val) {
                $output .= $space.$result[$val];
                $space = ' - ';
                $foundkeys[] = $result[$clean_key_attribute];
            }
            $results[$result[$clean_key_attribute]] = $output;

            $output = '';
//$results_list .= "<li id='".$result[$key_attribute]."'>{$output}</li>";
        }
        $space = '';
    }

    if($options['disableSoundex'] != 1) {
        if(is_array($foundkeys)) {
            $notkeys = implode(',', $foundkeys);
            $notin = ' AND '.$key_attribute.' NOT IN ('.$notkeys.') ';
        }

        $query2 = $db->query("SELECT {$select_attributes_string} FROM ".Tprefix."{$table} WHERE ({$soundex_where_string}){$notin}{$extra_where_string}{$order}");
        if($db->num_rows($query2) > 0) {
            while($result2 = $db->fetch_assoc($query2)) {
                foreach($select_attributes as $key => $val) {
                    $output .= $space.$result2[$val];
                    $space = ' ';
                }
                $results[$result2[$key_attribute]] = $output;
                $output = '';
            }
            $db->free_result($query2);
        }
    }
    if($options['source'] == 'addentity') {
        return $results;
    }
    if(is_array($results)) {
        foreach($results as $key => $val) {
            if($options['returnType'] == 'json' || $options['returnType'] == 'jsontoken') {
                $results_list['"'.$key.'"']['id'] = $key;
                $results_list['"'.$key.'"']['value'] = preg_replace('/[^\da-z]/i', ' ', $val);
            }
            if($options['returnType'] != 'jsontoken' && isset($options['descinfo']) && !empty($options['descinfo'])) {
                switch($options['descinfo']) {
                    case 'citycountry':
//                        $hotelsobj = TravelManagerHotels::get_data('tmhid='.$key);
//                        if(is_object($hotelsobj)) {
//                            $city = new Cities($hotelsobj->city);
//                        }
//                        else {
//                            $city = new Cities($key);
//                        }
                        $city = new Cities($key);
                        if($options['returnType'] == 'json') {
                            $results_list['"'.$key.'"']['id'] = $city->ciid;
                            $results_list['"'.$key.'"']['desc'] = $city->name.' - '.$city->get_country()->name;
                        }
                        else {
                            $details = '<br /><span class="smalltext">'.$city->name.' - '.$city->get_country()->name.'</span>';
                            $results_list .= '<li id="'.$city->ciid.'">'.$val.$details.'</li>';
                        }
                        unset($details);
                        break;
                    case 'hotelcitycountry':
                        $hotelsobj = TravelManagerHotels::get_data('tmhid='.$key);
                        $city = new Cities($hotelsobj->city);
                        if($options['returnType'] == 'json') {
                            $results_list['"'.$key.'"']['id'] = $key;
                            $results_list['"'.$key.'"']['desc'] = $city->name.' - '.$city->get_country()->name;
                        }
                        else {
                            $details = '<br /><span class="smalltext">'.$city->get_country()->name.'</span>';
                            $results_list .= '<li id="'.$key.'">'.$val.$details.'</li>';
                        }
                        unset($details);
                        break;
                    case 'productsegment':
                        $product = new Products($key);
                        $chemfuncprod_objs = $product->get_chemfunctionproducts();
                        if(is_array($chemfuncprod_objs)) {
                            foreach($chemfuncprod_objs as $chemfuncprod_obj) {
                                $application_obj = $chemfuncprod_obj->get_segapplicationfunction();
                                if($options['returnType'] == 'json') {
                                    $results_list['"'.$key.'"']['id'] = $chemfuncprod_obj->cfpid;
                                    $results_list['"'.$key.'"']['desc'] = $chemfuncprod_obj->get_chemicalfunction()->title.' - '.$application_obj->get_application()->title.' - '.$application_obj->get_segment()->title;
                                }
                                else {
                                    $details = '<br /><span class="smalltext">'.$chemfuncprod_obj->get_chemicalfunction()->title.' - '.$application_obj->get_application()->title.' - '.$application_obj->get_segment()->title.'</span>';
                                    $results_list .= '<li id="'.$chemfuncprod_obj->cfpid.'">'.$val.$details.'</li>';
                                }
                            }
                        }
                        else { /* get Defaultfunction of the product */
                            $chemfuncprod_objs = $product->get_defaultchemfunction();
                            if(is_array($chemfuncprod_objs)) {
                                foreach($chemfuncprod_objs as $chemfuncprod_obj) {
                                    $application_obj = $chemfuncprod_obj->get_segapplicationfunction();
                                    if($options['returnType'] == 'json') {
                                        $results_list['"'.$key.'"']['id'] = $chemfuncprod_obj->cfpid;
                                        $results_list['"'.$key.'"']['desc'] = $chemfuncprod_obj->get_chemicalfunction()->title.' - '.$application_obj->get_application()->title.' - '.$application_obj->get_segment()->title;
                                    }
                                    else {
                                        $details = '<br/><span class="smalltext">'.$chemfuncprod_obj->get_chemicalfunction()->title.' - '.$application_obj->get_application()->title.' - '.$application_obj->get_segment()->title.'</span>';
                                        $results_list .= '<li id="'.$chemfuncprod_obj->cfpid.'">'.$val.$details.'</li>';
                                    }
                                }
                            }
                            else {
                                if($options['returnType'] == 'json') {
                                    unset($results_list['"'.$key.'"']);
                                }
                            }
                        }
                        unset($details);
                        break;
                    case 'genericsegment':
                        $product = new Products($key);
                        $generic = $product->get_genericproduct();
                        if(is_object($generic)) {
                            if($options['returnType'] == 'json') {
                                $results_list['"'.$key.'"']['desc'] = $generic->get_segment()->title;
                            }
                            else {
                                $details = '<br/><span class="smalltext">'.$generic->get_segment()->title.'</span>';
                                $results_list .= '<li id="'.$key.'">'.$val.$details.'</li>';
                            }
                        }
                        else {
                            if($options['returnType'] == 'json') {
                                unset($results_list['"'.$key.'"']);
                            }
                        }
                        break;
                    case 'checmicalfunction':
                        $chemfunchem_objs = ChemFunctionChemicals::get_data('csid='.$key, array('returnarray' => 1));
                        if(is_array($chemfunchem_objs)) {
                            unset($results_list['"'.$key.'"']);

                            foreach($chemfunchem_objs as $chemfunchem_obj) {
                                $application_obj = $chemfunchem_obj->get_segapplicationfunction();

                                if($options['returnType'] == 'json') {
                                    $results_list['"'.$chemfunchem_obj->cfcid.'"']['value'] = $val;
                                    $results_list['"'.$chemfunchem_obj->cfcid.'"']['id'] = $chemfunchem_obj->cfcid;
                                    if(!empty($application_obj->get_application()->title)) {
                                        $results_list['"'.$chemfunchem_obj->cfcid.'"']['desc'] = $chemfunchem_obj->get_chemicalfunction()->title.' - '.$application_obj->get_application()->title.' - '.$application_obj->get_segment()->title;
                                    }
                                }
                                else {
                                    if(!empty($application_obj->get_application()->title)) {
                                        $details = '<br /><span class="smalltext">'.$chemfunchem_obj->get_chemicalfunction()->title.' - '.$application_obj->get_application()->title.' - '.$application_obj->get_segment()->title.'</span>';
                                    }
                                    $results_list .= '<li id="'.$chemfunchem_obj->cfcid.'">'.$val.$details.'</li>';
                                }
                            }
                        }
                        else {
                            if($options['returnType'] == 'json') {
                                unset($results_list['"'.$key.'"']);
                            }
                        }
                        break;
                    case 'entbrandsproducts':
                        $entbrandproducts = EntBrandsProducts::get_data('ebid='.$key, array('returnarray' => 1));
                        if(is_array($entbrandproducts)) {
                            unset($results_list['"'.$key.'"']);
                            foreach($entbrandproducts as $entbrandproduct) {
                                $endprod = $entbrandproduct->get_endproduct();
                                $characteristic = $entbrandproduct->get_charactersticvalue();
                                $characteristic_output = $characteristic->get_id();
                                if(!empty($characteristic_output)) {
                                    $characteristic_output = ' ('.$characteristic->get_displayname().')';
                                }
                                if(is_object($endprod)) {
                                    $details = $endprod->title;
                                    $first_parent = $endprod->get_parent();
                                    if(is_object($first_parent)) {
                                        $details = $first_parent->get_displayname();
                                        $secondpar_obj = $first_parent->get_parent();
                                        if(is_object($secondpar_obj)) {
                                            $details = $secondpar_obj->get_displayname().' < '.$details;
                                            $third_par = $secondpar_obj->get_parent();
                                            if(is_object($third_par)) {
                                                $originalpar_obj = $third_par->get_mother();
                                                if(is_object($originalpar_obj)) {
                                                    $details = $originalpar_obj->get_displayname().'< ... < '.$details;
                                                }
                                            }
                                        }
                                    }
                                    if($options['returnType'] == 'json') {
                                        $results_list['"'.$entbrandproduct->get_id().'"']['value'] = $val.$characteristic_output;
                                        $results_list['"'.$entbrandproduct->get_id().'"']['id'] = $entbrandproduct->get_id();
                                        $results_list['"'.$entbrandproduct->get_id().'"']['desc'] = $details;
                                    }
                                    else {
                                        $details = '<br /><span class="smalltext">'.$entbrandproduct->get_endproduct()->title.$characteristic_output.'</span>';
                                        $results_list .= '<li id="'.$entbrandproduct->get_id().'">'.$val.$details.'</li>';
                                    }
                                }
                                else {
                                    if($options['returnType'] == 'json') {
                                        $results_list['"'.$entbrandproduct->get_id().'"']['value'] = $val.$characteristic_output;
                                        $results_list['"'.$entbrandproduct->get_id().'"']['id'] = $entbrandproduct->get_id();
                                    }
                                    else {
                                        $results_list .= '<li id="'.$entbrandproduct->get_id().'">'.$val.$characteristic_output.'</li>';
                                    }
                                }
                            }
                        }
                        else {
                            if($options['returnType'] == 'json') {
                                unset($results_list['"'.$key.'"']);
                            }
                        }
                        break;
                    case 'endproducttypes':
                        $current_obj = new EndProducTypes($key);

                        if(is_object($current_obj)) {
                            $first_parent = $current_obj->get_parent();
                            if(is_object($first_parent)) {
                                $details = $first_parent->get_displayname();
                                $secondpar_obj = $first_parent->get_parent();
                                if(is_object($secondpar_obj)) {
                                    $details = $secondpar_obj->get_displayname().' < '.$details;
                                    $third_par = $secondpar_obj->get_parent();
                                    if(is_object($third_par)) {
                                        $originalpar_obj = $third_par->get_mother();
                                        if(is_object($originalpar_obj)) {
                                            $details = $originalpar_obj->get_displayname().'< ... < '.$details;
                                        }
                                    }
                                }
                                if($options['returnType'] == 'json') {
                                    $results_list['"'.$current_obj->eptid.'"']['value'] = $val;
                                    $results_list['"'.$current_obj->eptid.'"']['id'] = $current_obj->eptid;
                                    $results_list['"'.$current_obj->eptid.'"']['desc'] = $details;
                                }
                                else {
                                    $details = '<br /><span class="smalltext">'.$details.'</span>';
                                    $results_list .= '<li id="'.$current_obj->eptid.'">'.$val.$details.'</li>';
                                }
                            }
                            else {
                                if($options['returnType'] == 'json') {
                                    $results_list['"'.$current_obj->eptid.'"']['value'] = $val;
                                    $results_list['"'.$current_obj->eptid.'"']['id'] = $current_obj->eptid;
                                }
                                else {
                                    $results_list .= '<li id="'.$current_obj->eptid.'">'.$val.'</li>';
                                }
                            }
                        }
                        else {
                            if($options['returnType'] == 'json') {
                                unset($results_list['"'.$key.'"']);
                            }
                        }
                        unset($details);

                        break;
                    case 'endproducttype':
                        $current_obj = new EndProducTypes($key);
                        if(is_object($current_obj)) {
                            $first_parent = $current_obj->get_parent();
                            if(is_object($first_parent)) {
                                $details = $first_parent->get_displayname();
                                $secondpar_obj = $first_parent->get_parent();
                                if(is_object($secondpar_obj)) {
                                    $details.='-->'.$secondpar_obj->get_displayname();
                                    $third_par = $secondpar_obj->get_parent();
                                    if(is_object($third_par)) {
                                        $originalpar_obj = $third_par->get_mother();
                                        if(is_object($originalpar_obj)) {
                                            $details.='->.....->'.$originalpar_obj->get_displayname();
                                        }
                                    }
                                }
                                if($options['returnType'] == 'json') {
                                    $results_list['"'.$current_obj->eptid.'"']['value'] = $val;
                                    $results_list['"'.$current_obj->eptid.'"']['id'] = $current_obj->eptid;
                                    $results_list['"'.$current_obj->eptid.'"']['desc'] = $details;
                                }
                                else {
                                    $details = '<br /><span class="smalltext">'.$details.'</span>';
                                    $results_list .= '<li id="'.$current_obj->eptid.'">'.$val.$details.'</li>';
                                }
                            }
                        }
                        else {
                            if($options['returnType'] == 'json') {
                                unset($results_list['"'.$key.'"']);
                            }
                        }
                        unset($details);

                        break;
                    case 'brands':
                        $brand_obj = new EntitiesBrands($key);
                        $customer_obj = $brand_obj->get_entity();
                        if(is_object($customer_obj)) {
                            if($options['returnType'] == 'json') {
                                $results_list['"'.$key.'"']['value'] = $val;
                                $results_list['"'.$key.'"']['id'] = $brand_obj->ebid;
                                $results_list['"'.$key.'"']['desc'] = $customer_obj->get_displayname();
                            }
                            else {
                                $details = '<br /><span class="smalltext">'.$customer_obj->get_displayname().'</span>';
                                $results_list .= '<li id="'.$key.'">'.$val.$details.'</li>';
                            }
                        }
                        else {
                            if($options['returnType'] == 'json') {
                                unset($results_list['"'.$key.'"']);
                            }
                        }
                        unset($details);
                        break;
                    case 'country':
                        $entity = new Entities($key);
                        if(!empty($entity->country)) {
                            if($options['returnType'] == 'json' || $options['returnType'] == 'jsontoken') {
                                $results_list['"'.$key.'"']['desc'] = $entity->get_country()->name;
                            }
                            else {
                                $details = '<br /><span class="smalltext">'.$entity->get_country()->name.'</span>';
                                $results_list .= '<li id="'.$key.'">'.$val.$details.'</li>';
                            }
                        }
                        else {
                            if($options['returnType'] != 'json') {
                                $results_list .= '<li id="'.$key.'">'.$val.'</li>';
                            }
                        }
                        break;
                    default:
                        if($options['returnType'] != 'json') {
                            $results_list .= '<li id="'.$key.'">'.$val.'</li>';
                        }
                        break;
                    case 'basicfacilities':
                        unset($category, $details, $desc_distance);
                        $facility = new FacilityMgmtFacilities($key);
                        $motherfacility = $facility->get_mother();
                        $details = '';
                        if(is_object($motherfacility) && !empty($motherfacility->fmfid) && $motherfacility->fmfid != $facility->fmfid) {
                            $details .= $motherfacility->get_displayname();
                        }
                        if(!empty($facility->capacity)) {
                            $details .=' -'.$lang->capacity.': '.$facility->capacity;
                        }
                        $category = $lang->otheravailable;
                        if(isset($options['extrainput']) && !is_empty($options['extrainput'])) {
                            $query = $db->query("SELECT affid, name, phone1, X(geoLocation) AS longitude, Y(geoLocation) AS latitude FROM ".Tprefix."affiliates WHERE asText(geoLocation) IS NOT NULL AND affid= ".intval($facility->affid));
                            while($affiliate = $db->fetch_assoc($query)) {
                                $affiliatelong = $affiliate['longitude'];
                                $affiliatelat = $affiliate['latitude'];
                            }
                            $distance = calculateDistance($options['extrainput']['userlong'], $options['extrainput']['userlat'], $affiliatelat, $affiliatelong, 'K');
                            if(!empty($distance)) {
                                $desc_distance = ' '.$distance.' KM';
                                if($distance <= 10) {
                                    $category = $lang->capsnearby;
                                }
                            }
                        }
                        if(is_object($facility) && !empty($facility->fmfid)) {
                            if($options['returnType'] == 'json') {
                                if(!empty($desc_distance)) {
                                    $results_list['"'.$key.'"']['value'] = $results_list['"'.$key.'"']['value'].$desc_distance;
                                }
                                if($category == $lang->capsnearby) {
                                    $results_list['"'.$key.'"']['style'] = 'style="background-color:#A5FFA5;"';
                                }
                                $results_list[$category]['"'.$key.'"'] = $results_list['"'.$key.'"'];
                                $results_list[$category]['"'.$key.'"']['desc'] = $details;

                                unset($results_list['"'.$key.'"']);
                            }
                            else {
                                $details = '<br/><span class="smalltext">'.$details.'</span>';
                                $results_list .= '<li id="'.$key.'">'.$val.$details.'</li>';
                            }
                        }
                        else {
                            if($options['returnType'] == 'json') {
                                unset($results_list['"'.$key.'"']);
                            }
                        }
                        break;
                    case 'reservationfacilities':
                        unset($category, $isreserved, $details, $distance, $desc_distance, $affiliategeoloc, $meetingres);
                        $facility = new FacilityMgmtFacilities($key);
                        $motherfacility = $facility->get_mother();
                        if(is_object($motherfacility) && !empty($motherfacility->fmfid) && $motherfacility->fmfid != $facility->fmfid) {
                            $details = $motherfacility->get_displayname();
                        }
                        if(is_object($facility) && !empty($facility->fmfid)) {
                            $category = $lang->otheravailable;
                            if(isset($options['extrainput']) && !is_empty($options['extrainput'])) {
                                $from = $options['extrainput']['from'];
                                $to = $options['extrainput']['to'];
                                $isreserved = $facility->is_reserved($from, $to);
                                if(is_object($isreserved)) {
                                    $category = $lang->capsreserved;
                                    $reservedby = $isreserved->get_reservedBy()->get_displayname();
                                    $details.=' '.$lang->reservedby.' : '.$reservedby;
                                    if($isreserved->mtid == $options['extrainput']['mtid']) {
                                        $meetingres = 1;
                                    }
                                }
                                else {
                                    if(!empty($facility->capacity)) {
                                        $details .= ' - '.$lang->capacity.': '.$facility->capacity;
                                    }
                                    $query = $db->query("SELECT affid, name, phone1, X(geoLocation) AS longitude, Y(geoLocation) AS latitude FROM ".Tprefix."affiliates WHERE asText(geoLocation) IS NOT NULL AND affid= ".intval($facility->affid));
                                    while($affiliate = $db->fetch_assoc($query)) {
                                        $affiliategeoloc['lon'] = $affiliate['longitude'];
                                        $affiliategeoloc['lat'] = $affiliate['latitude'];
                                    }
                                    $distance = calculateDistance($options['extrainput']['userlong'], $options['extrainput']['userlat'], $affiliategeoloc['lat'], $affiliategeoloc['lon'], 'K');
                                    if(!empty($distance)) {
                                        $desc_distance = ' ('.number_format($distance, 2).' KM)';
                                        if($distance <= 10) {
                                            $category = $lang->capsnearby;
                                        }
                                    }
                                }
                            }

                            if($options['returnType'] == 'json') {
                                $results_list['"'.$key.'"']['desc'] = $details;
                                $results_list['"'.$key.'"']['style'] = 'class="li-greenbullet"';
                                if(is_object($isreserved)) {
                                    $results_list['"'.$key.'"']['style'] = 'class="li-redbullet"';
                                }
                                if(!empty($desc_distance)) {
                                    $results_list['"'.$key.'"']['value'] = $additionavalue.$results_list['"'.$key.'"']['value'].$desc_distance;
                                }
                                elseif($meetingres == 1) {
                                    $results_list['"'.$key.'"']['value'] = $additionavalue.$results_list['"'.$key.'"']['value'].'('.$lang->forthismeeting.')';
                                }
                                $results_list[$category]['"'.$key.'"']['distance'] = $desc_distance;
                                $results_list[$category]['"'.$key.'"'] = $results_list['"'.$key.'"'];
                                unset($additionavalue, $results_list['"'.$key.'"']);
                            }
                            else {
                                $details = '<br/><span class="smalltext">'.$details.'</span>';
                                $results_list .= '<li '.$style.' id="'.$key.'">'.$val.$details.'</li>';
                            }
                        }
                        else {
                            if($options['returnType'] == 'json') {
                                unset($results_list['"'.$key.'"']);
                            }
                        }
                        break;
                }
                if(isset($results_list['"'.$key.'"']['desc']) && !empty($results_list['"'.$key.'"']['desc'])) {
                    $results_list['"'.$key.'"']['desc'] = preg_replace('/[^\da-z]/i', ' ', $results_list['"'.$key.'"']['desc']);
                }
                if(isset($results_list['"'.$key.'"']['value']) && !empty($results_list['"'.$key.'"']['value'])) {
                    $results_list['"'.$key.'"']['value'] = preg_replace('/[^\da-z]/i', ' ', $results_list['"'.$key.'"']['value']);
                }
            }
            else {
                if($options['returnType'] != 'json' && $options['returnType'] != 'jsontoken') {
                    $results_list .= '<li id = "'.$key.'">'.$val.'</li>';
                }
            }
        }
    }

    if(!is_array($results) || empty($results_list)) {
        if($options['returnType'] != 'json') {
            $results_list = '<span class="red_text">'.$lang->nomatchfound.'</span>';
        }
        else {
            $results_list[0]['value'] = $lang->nomatchfound;
        }
    }
    else {
        if($options['returnType'] == 'json' && ($options['descinfo'] == 'basicfacilities' || $options['descinfo'] == 'reservationfacilities')) {
            ksort($results_list);
            foreach($results_list as $category => $results) {
                $new_resultlist ['"'.$category.'"']['style'] = 'style="text-align:center;pointer-events:none;background-color:#eaf2ea;"';
                $new_resultlist ['"'.$category.'"']['id'] = $category;
                $new_resultlist ['"'.$category.'"']['desc'] = "";
                $new_resultlist ['"'.$category.'"']['value'] = $category;
                $new_resultlist = array_merge_recursive($new_resultlist, $results);
            }
            $results_list = $new_resultlist;
        }
    }
    if($options['outputjsonformat'] == 'tokens') {
        $results_list = json_encode(array_values($results_list));
    }
    else if($options['returnType'] == 'json') {
        $results_list = json_encode($results_list);
    }
    else {
        $results_list = '<ul id = "searchResultsList">'.$results_list.'</ul>';
    }

    return $results_list;
}

function log_action() {
    global $db, $core;

    $data = func_get_args();

    if(count($data) == 1 && is_array($data[0])) {
        $data = $data[0];
    }

    if(!is_array($data)) {
        $data = array($data);
    }

    $log_entry = array(
            'uid' => $core->user['uid'],
            'ipaddress' => $db->escape_string(userip()),
            'date' => TIME_NOW,
            'module' => $db->escape_string($core->input['module']),
            'action' => $db->escape_string($core->input['action']),
            'data' => $db->escape_string(@serialize($data))
    );

    $db->insert_query('logs', $log_entry);
}

function record_contribution($rid, $isdone = 0) {
    global $db, $core;

    if($db->fetch_field($db->query("SELECT COUNT(*) AS contributed FROM ".Tprefix."reportcontributors WHERE rid='{$rid}' AND uid='{$core->user[uid]}'"), 'contributed') == 0) {
        $db->insert_query('reportcontributors', array('rid' => $rid, 'uid' => $core->user['uid'], 'isDone' => $isdone, 'timeDone' => TIME_NOW));
    }
    else {
        $db->update_query('reportcontributors', array('isDone' => 1, 'timeDone' => TIME_NOW), "rid='{$rid}' AND uid='{$core->user[uid]}'");
    }
}

/**
 * Gets the quarter information based on settings
 * @param  Boolean		$real		Whether a real current quarter or reporting quarter
 * @return Array					Current quarter and year
 */
function currentquarter_info($real = false) {
    global $core;
    $time_now = TIME_NOW;
    $current_year = date('Y', $time_now);

    for($i = 1; $i <= 4; $i++) {
        $quarter_start = strtotime($current_year.'-'.$core->settings['q'.$i.'start']);
        $quarter_end = strtotime($current_year.'-'.$core->settings['q'.$i.'end']);
        if($time_now >= $quarter_start && $time_now <= $quarter_end) {
            $current_quarter = $i;
            if($real === false) {
                $current_quarter = $i - 1;
                if($current_quarter == 0) {
                    $current_quarter = 4;
                    $current_year -= 1;
                }
            }
            return array('quarter' => $current_quarter, 'year' => $current_year);
        }
    }
    return false;
}

function parse_moduleslist($current_module, $modules_dir = 'modules', $is_selectlist = false) {
    global $core, $lang;

    $path = ROOT.$modules_dir;
    $list = '';

    if(is_dir($path)) {
        $files = scandir($path);
        foreach($files as $file) {
            if($file != '.' && $file != '..') {
                $file_info = pathinfo($path.'/'.$file);
                if($file_info['extension'] == 'php') {
                    require $path.'/'.$file;
                    if($is_selectlist === true) {
                        if($core->usergroup[$module['globalpermission']] == 1) {
                            $selected = '';
                            if($current_module == $module['name']) {
                                $selected = ' selected';
                            }
                            $list .= '<option value="'.$module['name'].'"'.$selected.'>'.$module['title'].'</option>';
                        }
                    }
                    else {
                        if($current_module != $module['name']) {
                            if($core->usergroup[$module['globalpermission']] == 1) {
                                $moduleicon = 'default';
                                if(file_exists('images/modules-icons/'.$module['name'].'.png')) {
                                    $moduleicon = $module['name'];
                                }
                                $list .= '<li class="searchable"><a href="index.php?module='.$module['name'].'/'.$module['homepage'].'"><img src="images/modules-icons/'.$moduleicon.'.png" alt="'.$module['name'].'"/> '.$module['title'].'</a></li>';
                            }
                        }
                        else {
                            $current_module_title = $module['title'];
                        }
                    }
                }
            }
        }
    }

    if(!empty($list)) {
        return $list;
//        if($is_selectlist === true) {
//            return '<select name="defaultModule" id="defaultModule"><option value="">&nbsp;<option>'.$list.'</select>';
//        }
//        else {
//            return '<div id="currentmodule_name"><span class="subtitle">'.$current_module_title.'</span> <br /><div class="moduleslist_container">'.$list.'</div></div>';
//        }
    }
    else {
        return false;
    }
}

function parse_menuitems($module_name, $modules_dir = 'modules') {
    global $core, $lang, $module;

    if(IN_AREA == 'user') {
        if(!empty($module_name)) {
            if(!isset($module)) {
                require ROOT.$modules_dir.'/'.$module_name.'.php';
            }
            if($core->usergroup[$module['globalpermission']] == 1) {
                if(is_array($module['menu'])) {
                    $menu = $module['menu'];

                    $array_indexes = array_keys($menu['file']);

                    while($item = current($menu['file'])) {
                        $key = key($menu['file']);
                        if(is_array($item)) {
                            $current_index = array_search($key, $array_indexes, true);

                            $array2_indexes = array_keys($menu['title']);
                            $array2_key = $array2_indexes[$current_index];

                            $array3_indexes = array_keys($menu['permission']);
                            $array3_key = $array3_indexes[$current_index];

                            if($core->usergroup[$menu['permission'][$array3_key][0]] == 1) {
                                $items .= '<li class="expandable list-group-item"><span id="'.$key.'">'.$lang->$array2_indexes[$current_index].'</span>';
                                $items .= '<div id="'.$key.'_children_container" style="display: none;">';
                                $items .= '<ul id="'.$key.'_children" style="padding-left:0px;">';
                                foreach($item as $k => $v) {
                                    if($core->usergroup[$menu['permission'][$array3_key][($k + 1)]] == 1) {
                                        $items .= "<li class='list-group-item'><span id='{$module_name}/{$v}'><a href='index.php?module={$module_name}/{$v}'>{$lang->$menu[title][$array2_key][$k]}</a></span></li>\n";
                                    }
                                }
                                $items .= '</ul></div></li>';
                            }
                        }
                        else {
                            if($core->usergroup[$menu['permission'][$key]] == 1) {
                                $items .= "<li class='list-group-item'><span id='{$module_name}/{$item}'><a href='index.php?module={$module_name}/{$item}'>{$lang->$menu[title][$key]}</a></span></li>\n";
                            }
                        }
                        next($menu['file']);
                    }
                }
            }
        }
    }
    return $items;
}

/**
 * To replace parse_userentities_data() gradually
 */
function get_user_business_assignments($uid) {
    global $db, $core;
    if(empty($uid)) {
        exit;
    }

    $uid = $db->escape_string($uid);
    if($uid == $core->user['uid']) {
        $usergroup = $core->usergroup;
    }
    else {
        $usergroup = $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."usergroups WHERE gid=(SELECT gid FROM ".Tprefix."users_usergroups WHERE isMain=1 AND uid={$uid})"));
    }

    $data = array();
    /* Get which suppliers user is editing - START */
    $auditing = $db->query("SELECT eid FROM ".Tprefix."suppliersaudits WHERE uid='{$uid}'");
    if($db->num_rows($auditing) > 0) {
        while($auditfor = $db->fetch_assoc($auditing)) {
            $data['auditfor'][] = $auditfor['eid'];
        }
    }
    else {
        $data['auditfor'] = array();
    }
    /* Get which suppliers user is editing - END */

    /* GET users affiliates - START */
    $affiliates_query = $db->query("SELECT affid, isMain, canHR, canAudit FROM ".Tprefix."affiliatedemployees WHERE uid='{$uid}'");
    if($db->num_rows($affiliates_query) > 0) {
        while($affiliate = $db->fetch_assoc($affiliates_query)) {
            $affiliates[$affiliate['affid']] = $affiliate['affid'];
            if($affiliate['isMain'] == 1) {
                $data['mainaffiliate'] = $affiliate['affid'];
            }

            if($affiliate['canHR'] == 1) {
                $data['hraffids'][$affiliate['affid']] = $affiliate['affid'];
            }

            if($affiliate['canAudit'] == 1) {
                $data['auditedaffids'][$affiliate['affid']] = $affiliate['affid'];
            }
        }
    }
    else {
        if(!is_array($affiliates)) {
            $affiliates = array(0);
        }
    }
    $data['affiliates'] = $affiliates;
    /* Get users affiliates - END */

    /* Get user affiliated entities - START */
    if(is_array($data['auditfor']) && !empty($data['auditfor'])) {
        foreach($data['auditfor'] as $key => $val) {
            $audited_affiliates = array();
            $data['suppliers']['eid'][$val] = $val;
            $audited_affiliates = get_specificdata('affiliatedentities', 'affid', 'affid', 'affid', '', 0, "eid='{$val}'");
            $data['auditedaffiliates'][$val] = $audited_affiliates; //Temporary to maintain backward compatibilty
            if(is_array($audited_affiliates)) {
                foreach($audited_affiliates as $affid) {
                    $data['suppliers']['affid'][$val][$affid] = $affid;
                    $data['affiliates'][$affid] = $affid;
                }
            }
        }
    }

    $audited_affiliates_query = $db->query("SELECT ae.eid, ae.affid, e.type FROM ".Tprefix."affiliatedentities ae LEFT JOIN ".Tprefix."entities e ON (e.eid=ae.eid) WHERE affid IN (SELECT affid FROM ".Tprefix."affiliatedemployees WHERE uid={$uid} AND canAudit=1)");
    if($db->num_rows($audited_affiliates_query) > 0) {
        while($audited_affiliate = $db->fetch_assoc($audited_affiliates_query)) {
            if($audited_affiliate['type'] == 's') {
                $data['suppliers']['eid'][$audited_affiliate['eid']] = $audited_affiliate['eid'];
                $data['suppliers']['affid'][$audited_affiliate['eid']][$audited_affiliate['affid']] = $audited_affiliate['affid'];
            }
            elseif($audited_affiliate['type'] == 'c') {
                $data['customers'][$audited_affiliate['eid']] = $audited_affiliate['eid'];
            }
        }
    }

    $entities = $db->query("SELECT ae.eid, ae.affid, e.type FROM ".Tprefix."assignedemployees ae LEFT JOIN ".Tprefix."entities e ON (e.eid=ae.eid) WHERE ae.uid='{$uid}'");
    if($db->num_rows($entities) > 0) {
        while($entity = $db->fetch_assoc($entities)) {
            if($entity['type'] == 's') {
                $data['suppliers']['eid'][$entity['eid']] = $entity['eid'];
                $data['suppliers']['affid'][$entity['eid']][$entity['affid']] = $entity['affid'];
            }
            elseif($entity['type'] == 'c') {
                $data['customers'][$entity['eid']] = $entity['eid'];
            }
        }
    }

    if(!isset($data['customers'])) {
        $data['customers'] = array(0);
    }

    if(!isset($data['suppliers'])) {
        $data['suppliers'] = array(0);
    }
    /* Get user affiliated entities - END */

    return $data;
}

/**
 *  Get user entities view permissions and return them is a ready WHERE clause statement
 * @paran	String						Option suppliersbyaffid/suppliersbyspid
 * @param	Int							supplier or affiliate ID
 * @paran	Int							User ID
 * @paran	Boolean						Without AND
 * @paran	String						Attributes prefix
 * @return 	Array		$where			Where statement content
 */
function getquery_business_assignments() {
    global $core, $db;
    $arguments = func_get_args();

    if(!empty($arguments[2])) {
        $user = get_user_business_assignments($arguments[2]);
        $usergroup = $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."usergroups WHERE gid=(SELECT gid FROM ".Tprefix."users_usergroups WHERE isMain=1 AND uid={$arguments[2]})"));
    }
    else {
        $user = $core->user;
        $usergroup = $core->usergroup;
    }

    $auditfor = array();
    if(isset($user['auditfor'])) {
        $auditfor = $user['auditfor'];
    }

    $and = ' AND ';
    if($arguments[3] == 1) {
        $and = '';
    }

    $attribute_prefix = 'r.';
    if(!empty($arguments[4])) {
        $attribute_prefix = $db->escape_string($arguments[4]).'.';
    }
    $where = array();
    if($arguments[0] == 'suppliersbyaffid' || $arguments[0] == 'affiliatebyspid') {
        $query_attribute = '';
        if($arguments[0] == 'suppliersbyaffid') {
            if($usergroup['canViewAllSupp'] == 0) {
                foreach($user['suppliers']['eid'] as $key => $val) {
                    if(in_array($arguments[1], $user['suppliers']['affid'][$val])) {
                        $found_ids[] = $val;
                    }
                }
                $query_attribute = $attribute_prefix.'spid';
            }
        }
        else {
            if($usergroup['canViewAllAff'] == 0) {
                $found_ids = $user['suppliers']['affid'][$arguments[1]];

                $query_attribute = $attribute_prefix.'affid';
            }
        }

        if(!empty($query_attribute)) {
            $where['extra'] = $and.'('.$query_attribute.' IN ('.implode(',', $found_ids).'))';
        }
    }
    else {
        if($usergroup['canViewAllSupp'] == 0) {
            $where['extra'] = $and.'(';
            foreach($user['suppliers']['eid'] as $val) {
                $inaffiliates_query = '';
                if($usergroup['canViewAllAff'] == 0) {
                    $inaffiliates_query = ' AND '.$attribute_prefix.'affid IN ('.implode(',', $user['suppliers']['affid'][$val]).')';
                }

                $where['extra'] .= $query_or.'('.$attribute_prefix.'spid='.$val.$inaffiliates_query.')';
                $where['multipage'] .= $query_or.'(spid='.$val.$inaffiliates_query.')';
                $where['byspid'][$val] = $inaffiliates_query;

                $query_or = ' OR ';
            }
            $where['extra'] .= ')';
        }

        if($usergroup['canViewAllSupp'] == 1 && $usergroup['canViewAllAff'] == 0) {
            $inaffiliates = implode(',', $user['affiliates']);

            $where['extra'] = ' AND '.$attribute_prefix.'affid IN ('.$inaffiliates.') '; //AND.
            $where['multipage'] = 'affid IN ('.$inaffiliates.')';
        }
    }
    return $where;
}

function parse_userentities_data($uid) {
    global $db, $core;
    if(empty($uid)) {
        exit;
    }

    $uid = $db->escape_string($uid);
    if($uid == $core->user['uid']) {
        $usergroup = $core->usergroup;
    }
    else {
        $usergroup = $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."usergroups WHERE gid=(SELECT gid FROM ".Tprefix."users_usergroups WHERE isMain=1 AND uid={$uid})"));
    }

    $data = array();
    $auditing = $db->query("SELECT eid FROM ".Tprefix."suppliersaudits WHERE uid='{$uid}'");
    if($db->num_rows($auditing) > 0) {
        while($auditfor = $db->fetch_assoc($auditing)) {
            $data['auditfor'][] = $auditfor['eid'];
        }
    }
    else {
        $data['auditfor'] = array();
    }

    if($usergroup['canViewAllAff'] == 0) {
//$affiliates = get_specificdata('affiliatedemployees', 'affid', 'affid', 'affid', '', 0, "uid='{$uid}'");
        $affiliates_query = $db->query("SELECT affid, isMain, canAudit FROM ".Tprefix."affiliatedemployees WHERE uid='{$uid}'");
        if($db->num_rows($affiliates_query) > 0) {
            while($affiliate = $db->fetch_assoc($affiliates_query)) {
                $affiliates[$affiliate['affid']] = $affiliate['affid'];
                if($affiliate['isMain'] == 1) {
                    $data['mainaffiliate'] = $affiliate['affid'];
                }
            }
        }
        else {
            if(!is_array($affiliates)) {
                $suppliers = array(0);
            }
        }
        $data['affiliates'] = $affiliates;

        if(is_array($data['auditfor']) && !empty($data['auditfor'])) {
            foreach($data['auditfor'] as $key => $val) {
                $data['auditedaffiliates'][$val] = get_specificdata('affiliatedentities', 'affid', 'affid', 'affid', '', 0, "eid='{$val}'");
            }
        }
    }

    if($usergroup['canViewAllCust'] == 0 || $usergroup['canViewAllSupp'] == 0) {
        $entities = $db->query("SELECT ae.eid, ae.affid, e.type FROM ".Tprefix."assignedemployees ae LEFT JOIN ".Tprefix."entities e ON (e.eid=ae.eid) WHERE ae.uid='{$uid}'");
        if($db->num_rows($entities) > 0) {
            while($entity = $db->fetch_assoc($entities)) {
                if($entity['type'] == 's') {
                    $data['suppliers']['eid'][$entity['eid']] = $entity['eid'];
                    $data['suppliers']['affid'][$entity['eid']][$entity['affid']] = $entity['affid'];
                }
                elseif($entity['type'] == 'c') {
                    $data['customers'][$entity['eid']] = $entity['eid'];
                }
            }
        }

        $audited_affiliates_query = $db->query("SELECT ae.eid, ae.affid, e.type FROM ".Tprefix."affiliatedentities ae LEFT JOIN ".Tprefix."entities e ON (e.eid=ae.eid) WHERE affid IN (SELECT affid FROM ".Tprefix."affiliatedemployees WHERE uid={$uid} AND canAudit=1)");
        if($db->num_rows($audited_affiliates_query) > 0) {
            while($audited_affiliate = $db->fetch_assoc($audited_affiliates_query)) {
                if($entity['type'] == 's') {
                    $data['suppliers']['eid'][$audited_affiliate['eid']] = $audited_affiliate['eid'];
                    $data['suppliers']['affid'][$audited_affiliate['eid']][$audited_affiliate['affid']] = $audited_affiliate['affid'];
                }
                elseif($entity['type'] == 'c') {
                    $data['customers'][$audited_affiliate['eid']] = $audited_affiliate['eid'];
                }
            }
        }

        if(!isset($data['customers'])) {
            $data['customers'] = array(0);
        }

        if(!isset($data['suppliers'])) {
            $data['suppliers'] = array(0);
        }
    }

    return $data;
}

/**
 * Get user entities view permissions and return them is a ready WHERE clause statement
 * @paran	String						Option suppliersbyaffid/suppliersbyspid
 * @param	Int							supplier or affiliate ID
 * @paran	Int							User ID
 * @paran	Boolean						Without AND
 * @paran	String						Attributes prefix
 * @return 	Array		$where			Where statement content
 */
function getquery_entities_viewpermissions() {
    global $core, $db;
    $arguments = func_get_args();

    if(!empty($arguments[2])) {
        $user = parse_userentities_data($arguments[2]);
        $usergroup = $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."usergroups WHERE gid=(SELECT gid FROM ".Tprefix."users_usergroups WHERE isMain=1 AND uid={$arguments[2]})"));
    }
    else {
        $user = $core->user;
        $usergroup = $core->usergroup;
    }

    $auditfor = array();
    if(isset($user['auditfor'])) {
        $auditfor = $user['auditfor'];
    }

    $and = ' AND ';
    if($arguments[3] == 1) {
        $and = '';
    }

    $attribute_prefix = 'r.';
    if(!empty($arguments[4])) {
        $attribute_prefix = $db->escape_string($arguments[4]).'.';
    }
    $where = array();

    if(is_array($arguments) && $arguments[0] == 'suppliersbyaffid' || $arguments[0] == 'affiliatebyspid') {
        $query_attribute = '';
        if($arguments[0] == 'suppliersbyaffid') {
            if($usergroup['canViewAllSupp'] == 0) {
                if(is_array($user['suppliers']['eid'])) {
                    foreach($user['suppliers']['eid'] as $key => $val) {
                        if(in_array($val, $auditfor)) {
                            if(in_array($arguments[1], $user['auditedaffiliates'][$val])) {
                                $found_ids[] = $val;
                            }
                        }
                        else {
                            if(in_array($arguments[1], $user['suppliers']['affid'][$val])) {
                                $found_ids[] = $val;
                            }
                        }
                    }
                    if(!empty($arguments[5])) {
                        $query_attribute = $arguments[5];
                    }
                    else {
                        $query_attribute = 'spid';
                    }
                    $query_attribute = $attribute_prefix.$query_attribute;
                }
            }
        }
        else {
            if($usergroup['canViewAllAff'] == 0) {
                if(in_array($arguments[1], $auditfor)) {
                    $found_ids = $user['auditedaffiliates'][$arguments[1]];
                }
                else {
                    $found_ids = $user['suppliers']['affid'][$arguments[1]];
                }
                $query_attribute = $attribute_prefix.'affid';
            }
        }

        if(!empty($found_ids)) {
            if(!empty($query_attribute)) {
                $where['extra'] = $and.'('.$query_attribute.' IN ('.implode(',', $found_ids).'))';
            }
        }
        else {
            if($usergroup['canViewAllAff'] == 0 && $usergroup['canViewAllSupp'] == 0 && !empty($query_attribute)) {
                $where['extra'] = $and.$query_attribute.' IN (0)';
            }
        }
    }
    else {
        if($usergroup['canViewAllSupp'] == 0) {
            if(is_array($user['suppliers']['eid'])) {
                $where['extra'] = $and.'(';
                foreach($user['suppliers']['eid'] as $val) {
                    if(in_array($val, $auditfor)) {
                        $inaffiliates_query = '';
                        if($usergroup['canViewAllAff'] == 0) {
                            if(is_array($user['auditedaffiliates'][$val])) {
                                $inaffiliates_query = ' AND '.$attribute_prefix.'affid IN ('.implode(',', $user['auditedaffiliates'][$val]).')';
                            }
                        }
                    }
                    else {
                        $inaffiliates_query = '';
                        if($usergroup['canViewAllAff'] == 0) {
                            $inaffiliates_query = ' AND '.$attribute_prefix.'affid IN ('.implode(',', $user['suppliers']['affid'][$val]).')';
                        }
                    }
                    $where['extra'] .= $query_or.'('.$attribute_prefix.'spid='.$val.$inaffiliates_query.')';
                    $where['multipage'] .= $query_or.'(spid='.$val.$inaffiliates_query.')';
                    $where['byspid'][$val] = $inaffiliates_query;

                    $query_or = ' OR ';
                }
                $where['extra'] .= ')';
            }
        }

        if($usergroup['canViewAllSupp'] == 1 && $usergroup['canViewAllAff'] == 0) {
            $inaffiliates = implode(',', $user['affiliates']);

            $where['extra'] = ' AND '.$attribute_prefix.'affid IN ('.$inaffiliates.') '; //AND.
            $where['multipage'] = 'affid IN ('.$inaffiliates.')';
        }
    }
    return $where;
}

/* Calculates how many working dates exist between 2 dates
 * @param	Array		$workdays					Array of the working days numbers in a week
 * @param	int			$check_dates_start			Timestamp from the starting date
 * @param	int			$check_dates_end			Timestamp from the end date
 * @return	int			$count_off_days				Numbers of working days between the 2 dates
 */
/* function count_workingdays2($workdays, $check_dates_start, $check_dates_end) {
  $reached_last_day == false;
  $date_being_checked = '';
  $count_off_days = 0;

  while($reached_last_day == false) {
  if(empty($date_being_checked)) {
  $date_being_checked = $check_dates_start;
  }

  if(in_array(date('N', $date_being_checked), $workdays)) {
  $count_off_days++;
  }

  $date_being_checked = $date_being_checked+(60*60*24);
  if($date_being_checked >= $check_dates_end) {
  $reached_last_day = true;
  }
  }
  return $count_off_days;
  } */
/**
 * Gives a different class for alternative rows
 * @param  String		$class			Current class
 * @return String		$class			The alternative row other class
 */
function alt_row($class) {
    if(empty($class)) {
        return 'trow';
    }

    if($class == 'trow') {
        return 'altrow';
    }
    else {
        return 'trow';
    }
}

/**
 * Formats the request url to a sortable one
 * @return String		$sort_url		Formatted url
 */
function sort_url() {
    $sort_url = $_SERVER['REQUEST_URI'];

    if(preg_match("/\&sortby=[a-z.]+/i", $sort_url)) {
        $sort_url = preg_replace("/\&sortby=[a-z.]+/i", '', $sort_url);
        $sort_url = preg_replace("/\&order=[a-z.]+/i", '', $sort_url);
    }

    return $sort_url;
}

function is_empty() {
    $arguments = func_get_args();
    foreach($arguments as $key => $val) {
        if(empty($val)) {
            return true;
        }
    }
    return false;
}

function array_sum_recursive($array) {
    if(is_array($array)) {
        $total = 0;
        foreach($array as $val) {
            if(is_array($val)) {
                $total += array_sum_recursive($val);
            }
            else {
                $total += $val;
            }
        }
        return $total;
    }
    return false;
}

function get_day_name($day_number, $type = 'names') {
    $names = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
    $letters = array('M', 'T', 'W', 'Th', 'F', 'S', 'Su');

    return ${$type}[$day_number - 1];
}

function format_size($size) {
    if($size < 1024) {
        return $size.'B';
    }
    elseif($size > 1024 && $size < 1048576) {
        return sprintf('%.0fkB', ($size / 1024));
    }
    elseif($size >= 1048576) {
        return sprintf('%.2fMB', ($size / 1048576));
    }
}

function getdate_custom($timestamp) {
    if(empty($timestamp)) {
        $timestamp = TIME_NOW;
    }
//    if($timestamp == 1441058400) {
//        $s = 1441058400;
//    }
    $date = getdate($timestamp);
    $date['week'] = date('W', $timestamp);
    $date['wdayiso'] = date('N', $timestamp);

    return $date;
}

function generate_random_color($lum = 0.97, $hue = 0.58, $sat = 0.6) {
    $color_dims = array('r', 'g', 'b');

    foreach($color_dims as $c) {
        $colors['dec'][$c] = $colors['int'][$c] = mt_rand(0, 255);
        $effect = $lum * $hue * $sat;
        $colors['dec'][$c] = round(min(max(0, $colors['dec'][$c] + ($colors['int'][$c] * $effect)), 255));
        $colors['hex'][$c] = dechex($colors['dec'][$c]);
    }

    $color = implode('', $colors['hex']);
    if(strlen($color) < 6 || ((($colors['dec']['r'] * 299) + ($colors['dec']['g'] * 587) + ($colors['dec']['b'] * 114)) / 1000) > 250) {
        $color = generate_random_color($lum, $hue, $sat);
    }
    return $color;
}

function parse_date($format, $date, $daytime = 0) {
    $delimiter = substr($format, 1, 1);
    $format_parts = explode($delimiter, $format);
    $date_parts = explode($delimiter, $date);
    if(count($date_parts) != 3) {
        return $date;
    }

    foreach($format_parts as $key => $value) {
        $date_parts[$value] = $date_parts[$key];
        unset($date_parts[$key]);
    }
    if($daytime == 0) {
        $timestamp = mktime(0, 0, 0, $date_parts['m'], $date_parts['d'], $date_parts['Y']);
    }
    else {
        $timestamp = mktime(23, 59, 59, $date_parts['m'], $date_parts['d'], $date_parts['Y']);
    }

    if(date($format, $timestamp) == $date) {
        return $timestamp;
    }
    else {
        return null;
    }
}

function get_curent_page_URL() {
    $pageURL = 'http';
    if($_SERVER["HTTPS"] == "on") {
        $pageURL .= "s";
    }
    $pageURL .= "://";
    if($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
    }
    else {
        $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    }
    return $pageURL;
}

// moved here from stock/reports because in use in assets too
function get_name_from_id($id, $tablename, $idcolumn, $namecolumn, $returnidifresolvefails = false) {
    static $idtonamecache = array();
    global $db;
    try {
        $name = $idtonamecache[$tablename][$idcolumn][$namecolumn][$id];
        if(isset($name)) {
            return $name;
        }
    }
    catch(Exception $e) {
        $msg = 'Exception '.$e->getMessage();
    }
    $name = $db->fetch_field($db->query('SELECT '.$namecolumn.' FROM '.Tprefix.$tablename.' WHERE '.$idcolumn.'="'.$db->escape_string($id).'"'), $namecolumn);
    $idtonamecache[$tablename][$idcolumn][$namecolumn][$id] = $name;
    if(isset($name)) {
        return $name;
    }
    else {
        if($returnidifresolvefails) {
            return $id;
        }
        else {
            return '-NA-';
        }
    }
}

function getAffiliateList($idsonly = false) {
    global $core, $db;
    if($core->usergroup['canViewAllAff'] == 0) {
        $tmpaffiliates = $core->user['affiliates'];
        foreach($tmpaffiliates as $value) {
            if($idsonly) {
                $affiliates[$value] = $value;
            }
            else {
                $affiliates[$value] = get_name_from_id($value, 'affiliates', 'affid', 'name');
            }
        }
    }
    else {
        $affiliates_query = $db->query('SELECT affid,name from '.Tprefix.'affiliates');
        if($db->num_rows($affiliates_query) > 0) {
            while($affiliate = $db->fetch_assoc($affiliates_query)) {
                if($idsonly) {
                    $affiliates[$affiliate['affid']] = $affiliate['affid'];
                }
                else {
                    $affiliates[$affiliate['affid']] = $affiliate['name'];
                }
            }
        }
    }
    asort($affiliates);
    return $affiliates;
}

function encapsulate_in_fieldset($html, $legend = "+", $boolStartClosed = false) {
//log_performance(__METHOD__);

    $id = md5(rand(9, 99999).time());

    $start_js_val = 1;
    $fsstate = "open";
    $content_style = "";

    if($boolStartClosed) {
        $start_js_val = 0;
        $fsstate = "closed";
        $content_style = "display: none;";
    }

    $js = "<script type='text/javascript'>

  var fieldset_state_$id = $start_js_val;

  function toggle_fieldset_$id() {

    var content = document.getElementById('content_$id');
    var fs = document.getElementById('fs_$id');

    if (fieldset_state_$id == 1) {
      // Already open.  Let's close it.
      fieldset_state_$id = 0;
      content.style.display = 'none';
      fs.className = 'c-fieldset-closed-$id'+' collapsible_fieldset';
    }
    else {
      // Was closed.  let's open it.
      fieldset_state_$id = 1;
      content.style.display = '';
      fs.className = 'c-fieldset-open-$id'+' collapsible_fieldset';
    }
  }
  function expand_fieldset_$id() {
	  var content = document.getElementById('content_$id');
	  var fs = document.getElementById('fs_$id');
      fieldset_state_$id = 1;
      content.style.display = '';
      fs.className = 'c-fieldset-open-$id'+' collapsible_fieldset';
  }
  </script><noscript><b>This page contains collapsible fieldsets which require Javascript to function properly.</b></noscript>";

    $rtn = "
    <fieldset class='c-fieldset-$fsstate-$id collapsible_fieldset' id='fs_$id'>
      <legend><a href='javascript: toggle_fieldset_$id();'>$legend</a></legend>
      <div id='content_$id' style='$content_style'>
        $html
      </div>
    </fieldset>
    $js

  <style>
  fieldset.c-fieldset-open-$id {
    border: 1px solid;
  }

  fieldset.c-fieldset-closed-$id {
    border: 2px solid;
    border-bottom-width: 0;
    border-left-width: 0;
    border-right-width: 0;
  }
  </style>

  ";
    return $rtn;
}

function formatit($number) {
    if(isset($number)) {
        return str_pad(round(number_format($number, 6, '.', ''), 6), 11, ' ', STR_PAD_LEFT);
    }
    else {
        return str_pad('-', 11, ' ', STR_PAD_LEFT);
    }
}

function array_merge_recursive_replace() {
    $arrays = func_get_args();
    $base = array_shift($arrays);
    foreach($arrays as $array) {
        reset($base);
        while(list($key, $value) = @each($array)) { //return the current key and  value pair from an array
            if(is_array($value) && @is_array($base[$key])) {
                $base[$key] = array_merge_recursive_replace($base[$key], $value);
            }
            else {
                $base[$key] = $value;
            }
        }
    }
    return $base;
}

function get_object_bytype($dim, $id, $simple = true) {
    switch($dim) {
        case 'affid':
        case 'useraffid':
            return new Affiliates($id);
            break;
        case 'cid':
        case 'spid':
            return new Entities($id);
            break;
        case 'pid':
            return new Products($id);
            break;
        case 'coid':
            return new Countries($id);
            break;
        case 'uid':
        case 'createdBy':
        case 'modifiedBy':
        case 'reportsTo':
        case 'businessMgr':
            return new Users($id);
            break;
        case 'ltid':
            return new LeaveTypes($id);
            break;
        case 'aletid':
            return new LeaveExpenseTypes($id);
            break;
        case 'lid':
            return new Leaves($id);
            break;
        case 'psid':
            return new ProductsSegments($id);
            break;
        case 'eptid':
            return new EndProducTypes($id);
            break;
        case 'psaid':
            return new SegmentApplications($id);
            break;
        case 'csid':
            return new Chemicalsubstances($id);
            break;
        case 'saleType':
        case 'stid':
            return new SaleTypes($id);
    }
}

function get_classname_bytable($table) {
    switch($table) {
        case 'entities':
            return Entities::CLASSNAME;
            break;
        case 'affiliatedentities':
            return AffiliatedEntities::CLASSNAME;
        case 'entitiessegments':
            return EntitiesSegments::CLASSNAME;
            break;
        case 'entitiesrepresentatives':
            return EntitiesRepresentatives::CLASSNAME;
            break;
        case 'visitreports':
            return VisitReports::CLASSNAME;
            break;
        case 'assignedemployees':
            return AssignedEmployees::CLASSNAME;
            break;
        case 'keycustomers':
            return KeyCustomers::CLASSNAME;
            break;
        case 'budgeting_budgets_lines':
            return BudgetLines::CLASSNAME;
            break;
        case 'budgeting_budgets':
            return Budgets::CLASSNAME;
            break;
        case 'grouppurchase_forecast':
            return GroupPurchaseForecast::CLASSNAME;
            break;
        case 'productsegments':
            return ProductsSegments::CLASSNAME;
            break;
        case 'products':
            return Products::CLASSNAME;
            break;
        case 'suppliersaudits':
            return SupplierAudits::CLASSNAME;
            break;
        default:
            return false;
    }
}

function fix_url($url) {
    if(!preg_match("~^(?:f|ht)tps?://~i", $url)) {
        $url = "http://".$url;
    }
    return $url;
}

/**
 * Sorts a multi-dimensional array by a specific column
 * @param array $data   The array to be sorted, passed by reference
 * @param type $order_attr
 * @param type $sort    Sorting flag
 */
function array_multisort_bycolumn(&$data, $order_attr, $sort = SORT_DESC) {
    ${$order_attr} = array();
    if(!is_array($data)) {
        return;
    }
    foreach($data as $data_key => $data_row) {
        ${$order_attr}[$data_key] = $data_row->{$order_attr};
    }
    array_multisort(${$order_attr}, $sort, $data);
}

/**
 * Generates a random unique string
 * @param string $prefix A prefix to be prepended to the generated alias
 * @return string
 */
function generate_checksum($prefix = '') {
    $identifier = substr(md5(uniqid(microtime())), 1, 10);

    if(!empty($prefix)) {
        $prefix = $prefix.'_';
    }
    return $prefix.$identifier;
}

/**
 * Converts a string to alias like string where are special characters and spaces are removed
 * @global \Core $core
 * @param String $string Text to convert to alias
 * @return String   Text converted to alias
 */
function generate_alias($string) {
    global $core;
    $string = str_replace(' ', '-', trim($string));
    $string = $core->sanitize_inputs($string, array('removetags' => true));
    $string = preg_replace('/[\@\!\&\(\)$%\^\*\+\#\/\\,.;:=]+/i', '', $string);
    $string = strtolower($string);
    return $string;
}

function is_nearby($maxdistance, $lat1, $lon1, $lat2, $lon2, $unit) {
    $distance = calculateDistance($lat1, $lon1, $lat2, $lon2, $unit);
    if($distance > $maxdistance) {
        return false;
    }
    return true;
}

function calculateDistance($lat1, $lon1, $lat2, $lon2, $unit) {

    $theta = $lon1 - $lon2;

    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));

    $dist = acos($dist);

    $dist = rad2deg($dist);

    $miles = $dist * 60 * 1.1515;
    $unit = strtoupper($unit);

    if($unit == "K") {
        return ($miles * 1.609344);
    }
    else if($unit == "N") {
        return ($miles * 0.8684);
    }
    else {
        return $miles;
    }
}

function get_lastquarters($currenctq_data) {
    switch($currenctq_data['quarter']) {
        case '2':
            $last_twoqs = array(1 => ($currenctq_data['year'] ), 4 => ($currenctq_data['year'] - 1));
            return $last_twoqs;
        case '1':
            $last_twoqs = array(4 => ($currenctq_data['year'] - 1), 3 => ($currenctq_data['year'] - 1));
            return $last_twoqs;
        default :
            $last_twoqs = array(( $currenctq_data['quarter'] - 1) => $currenctq_data['year'], ($currenctq_data['quarter'] - 2) => $currenctq_data['year']);
            return $last_twoqs;
    }
}

function get_lastquarter($currenctq_data) {
    switch($currenctq_data['quarter']) {
        case '1':
            $last_q = array('quarter' => 4, 'year' => $currenctq_data['year'] - 1);
            return $last_q;
        default :
            $last_q = array('quarter' => $currenctq_data['quarter'] - 1, 'year' => $currenctq_data['year']);
            return $last_q;
    }
}

function get_quarter_extremities($quarter, $year) {
    switch($quarter) {
        case 1:
            return array('start' => strtotime('01-Jan-'.$year), 'end' => strtotime('31-Mars-'.$year));
        case 2:
            return array('start' => strtotime('01-Apr-'.$year), 'end' => strtotime('30-Jun-'.$year));
        case 3 :
            return array('start' => strtotime('01-Jul-'.$year), 'end' => strtotime('30-Spet-'.$year));
        case 4:
            return array('start' => strtotime('01-Oct-'.$year), 'end' => strtotime('31-Dec-'.$year));
    }
    return false;
}

function get_helptour($reference) {
    global $lang;
    $helptour = new HelpTour();
    $helptour->set_id($reference.'_helptour');
    $helptour->set_cookiename($reference.'_helptour');
    $helptouritems_obj = new HelpTourItems();
    $touritems = $helptouritems_obj->get_helptouritems($reference);
    if(is_array($touritems)) {
        $helptour->set_items($touritems);
        return $helptour->parse();
    }
}

?>