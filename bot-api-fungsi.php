<?php


if (! defined('HS')) 
	die('Tidak boleh diakses langsung.');

function myPre($value) 
{
    print '<pre>';
    print_r ($value);
    print '</pre>';
}

function apiRequest($method, $data)
{
	if (!is_string($method)) {
    	error_log("Nama method harus bertipe string!\n");
    	return false;
  	}

  	if (!$data) {
    	$data = array();
  	} else if (!is_array($data)) {
    	error_log("Data harus bertipe array\n");
    	return false;
  	}


    $url =  "https://api.telegram.org/bot" . $GLOBALS['token'] . "/". $method;

    $options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
        ),
    );
    $context  = stream_context_create($options);

    $result = file_get_contents( $url, false, $context);

    return $result;
}

function getApiUpdate($offset) 
{
	$method = 'getUpdates';
	$data['offset'] = $offset; 

    $result = apiRequest($method, $data);

    $result = json_decode($result, true);
    if ($result["ok"]==1)
        return $result["result"];
    
    return array();
}

function sendApiMsg($chatid, $text, $msg_reply_id=false, $parse_mode=false, $disablepreview = false)
{
	$method = 'sendMessage';
    $data = [ 'chat_id' => $chatid, 'text'  => $text ];
    
    if ($msg_reply_id) 
    	$data['reply_to_message_id'] = $msg_reply_id;
    if ($parse_mode) 
    	$data['parse_mode'] = $parse_mode;
    if ($disablepreview)
    	$data['disable_web_page_preview'] = $disablepreview ;

    $result = apiRequest($method, $data);
}


function sendApiAction($chatid, $action='typing')
{
	$method = 'sendChatAction';
    $data = array(
        'chat_id' => $chatid,
        'action'  => $action

    );
    $result = apiRequest($method, $data);
}

function sendApiKeyboard($chatid, $text, $keyboard = Array(), $inline= false)
{
    $method = 'sendMessage';
    $replyMarkup = [
        'keyboard' => $keyboard,
        'resize_keyboard'=>true
    ];

    $data = array(
        'chat_id' => $chatid,
        'text'  => $text,
        'parse_mode' => 'Markdown',   

    );

    $inline  
    ?	$data['reply_markup'] = json_encode(array("inline_keyboard" => $keyboard))
    :	$data['reply_markup'] = json_encode( $replyMarkup ) ;

    $result = apiRequest($method, $data);
}


function editMessageText($chatid, $message_id, $text, $keyboard = Array(), $inline= false)
{
    $method = 'editMessageText';
    $replyMarkup = [
        'keyboard' => $keyboard,
        'resize_keyboard'=>true
    ];

    $data = array(
        'chat_id' => $chatid,
        'message_id' => $message_id,
        'text'  => $text,
        'parse_mode' => 'Markdown',   

    );

    $inline  
    ?	$data['reply_markup'] = json_encode(array("inline_keyboard" => $keyboard))
    :	$data['reply_markup'] = json_encode( $replyMarkup ) ;

    $result = apiRequest($method, $data);
}

function sendApiHideKeyboard($chatid, $text)
{
	$method = 'sendMessage';
    $data = array(
        'chat_id' => $chatid,
        'text'  => $text,
        'parse_mode' => 'Markdown',
        'reply_markup'  =>  json_encode(array("hide_keyboard" => true))

    );
   
	$result = apiRequest($method, $data);
}



?>