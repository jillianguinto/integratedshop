<?php

$access_token = "EAAGZB8gckSfQBAJgIxveB918DtkzjGSxmnpilCjn0URki3btjXXLRiSZATZCvc1NFtVKtW1ZBMkEvT0h9DxsPUFUeGfxd3ubo1qZBEqXSoT74Gcqy89fmG7nVUZCBZC9GcxYZCgNGpW5snmL45iCgzinASepiWryxJgCe0qz7sOrRwbZCXE8jpTMv";

define('bot_id',1, true);

$verify_token = '0c52e373fa83fd082f9af6d68a7fe16824ebe65f';

$hub_verify_token = null;
if(isset($_REQUEST['hub_mode']) && $_REQUEST['hub_mode']=='subscribe') {
    $challenge = $_REQUEST['hub_challenge'];
    $hub_verify_token = $_REQUEST['hub_verify_token'];

    if ($hub_verify_token === $verify_token) {
    	header('HTTP/1.1 200 OK');
	    echo $challenge;
	    die;
	}
}

$input = json_decode(file_get_contents('php://input'), true);

$sender = $input['entry'][0]['messaging'][0]['sender']['id'];
$sender_name = $input['entry'][0]['messaging'][0]['recipient']['name'];
$message = $input['entry'][0]['messaging'][0]['message']['text'];
$message_to_reply = '';
if(isset($input['entry'][0]['messaging'][0]['postback']['payload'])){
    $message = $input['entry'][0]['messaging'][0]['postback']['payload'];
}


if(!empty($input['entry'][0]['messaging'][0]['message']) || !empty($input['entry'][0]['messaging'][0]['postback']['payload']) ){
    
    //connect
    $hostname   = "119.81.221.66";
    $username   = "internal_bot";
    $password   = "O6B?coX2CZyJ";
    $database   = "internal_bot";

    //$datetime = date('Y-m-d H:i:s');

    if (!$dbhandle = mysql_connect($hostname, $username, $password)) {
        echo 'Could not connect to mysql';
        exit;
    }

    if (!mysql_select_db($database, $dbhandle)) {
        echo 'Could not select database';
        exit;
    }
    $sender_id = strtolower($sender);
    $a = '';
    $b = '';

    $guest = checkIfUserExist($sender_id);
    if(mysql_num_rows($guest)>0){
        $guest_result = mysql_fetch_assoc($guest);
        $user_id = $guest_result['id'];
    }
    else{
        $table = 'bot_user';
        $data_array = array(
            'name' => 'test101',
            'mobile' => '',
            'unique_id' => $sender_id,
            'source_id' => 3,
            'status' => 1,
            'date' => date('Y-m-d'),
            'bot_id' => bot_id
            );
        $user_id = insert_data($table,$data_array);
    }

    $data_array = array(
        'user_id' => $user_id,
        'message' => $message,
        'is_guest' => 1,
        'status' => 1,
        'date_time' => date('Y-m-d H:i:s')
    );

    $res = insert_data('bot_conversation',$data_array);

    $data = getAnswer($message);
    $count = mysql_num_rows($data);
    if($count>0){
        $result = mysql_fetch_assoc($data);
        $message_to_reply = $result['answer'];
        $data_array = array(
            'user_id' => $user_id,
            'message' => $result['answer'],
            'is_guest' => 0,
            'status' => 1,
            'date_time' => date('Y-m-d H:i:s')
        );
        insert_data('bot_conversation',$data_array);
        if($result['is_attachment']==1){
            $a = 1;
        }
        if($result['is_menu_driven']==1){
            $b = 1;
        }
    }
    else{
        $data = getUnAnswer();
        $result = mysql_fetch_assoc($data);
        $message_to_reply = $result['message'];
        $data_array = array(
            'user_id' => $user_id,
            'message' => $message,
            'status' => 1,
            'is_answered' => 0,
            'date_time' => date('Y-m-d H:i:s')
        );
        insert_data('bot_unlisted_question',$data_array);

        $data_array = array(
            'user_id' => $user_id,
            'message' => $result['message'],
            'is_guest' => 0,
            'status' => 1,
            'date_time' => date('Y-m-d H:i:s')
        );
        insert_data('bot_conversation',$data_array);
    }
    $data2['recipient']['id'] = $sender;
    $data2['message']['text'] = $message_to_reply;
    /*$jsonData = '{
        "recipient":{
            "id":"'.$sender.'"
        },
        "message":{
            "text":"'.$message_to_reply.'"
        }
    }';*/

    if($a==1){
      $attachment = getAttachment($result['id']);
      $file           = mysql_fetch_assoc($attachment);
      $file_path =  $file['path'];
      unset($data2['message']['text']);
      $data2['message']['attachment']['type'] = 'file';
      $data2['message']['attachment']['payload']['url'] = 'https://shop.clickhealth.com.ph/viberfiles/'.$file_path;
      $data2['message']['attachment']['payload']['is_reusable'] = true;
    }

    if($b==1) {
        unset($data2['message']['text']);
        $options = getOptions($result['id']);

        while($row = mysql_fetch_array($options)){
            $data_array2[] = array(
                'type' => 'postback',
                'title' => $row['question'],
                'payload' => $row['question']
            );
        }
        $data2['message']['attachment']['type'] = 'template';
        $data2['message']['attachment']['payload']['template_type'] = 'button';
        $data2['message']['attachment']['payload']['text'] = 'test';
        $data2['message']['attachment']['payload']['buttons'] = $data_array2;
    }

    if(strtolower($message)=='call') {
        unset($data2['message']['text']);
        $data_array2[] = array(
            'type' => 'phone_number',
            'title' => 'Call Representative',
            'payload' => '09178925619'
        );
        $data2['message']['attachment']['type'] = 'template';
        $data2['message']['attachment']['payload']['template_type'] = 'button';
        $data2['message']['attachment']['payload']['text'] = 'Need further assistance? Talk to a representative';
        $data2['message']['attachment']['payload']['buttons'] = $data_array2;
    }



        //API Url
    $url = 'https://graph.facebook.com/v2.6/me/messages?access_token='.$access_token;
    //Initiate cURL.
    $ch = curl_init($url);
    //The JSON data.

    //Encode the array into JSON.
    $jsonDataEncoded = json_encode($data2);
    //Tell cURL that we want to send a POST request.
    curl_setopt($ch, CURLOPT_POST, 1);
    //Attach our encoded JSON string to the POST fields.
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
    //Set the content type to application/json
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result2 = curl_exec($ch);
}

function getAttachment($id) {
    $query = "SELECT * FROM bot_attachment WHERE answer_id = '".$id."'";
    $result = mysql_query($query);
    return $result;
}
function getOptions($id) {
    $query = "SELECT * FROM bot_question WHERE answer_id = '".$id."' AND status = 1";
    $result = mysql_query($query);
    return $result;
}

function getUnAnswer() {
    $query = "SELECT message FROM bot_no_answer WHERE status = 1 ORDER BY RAND() LIMIT 1";
    $result =  mysql_query($query);
    return $result;
}
function checkIfUserExist($sender_id){
    $query = "SELECT * FROM bot_user WHERE unique_id = '".$sender_id."' AND bot_id = '".bot_id."' AND status = 1";
    $result =  mysql_query($query);
    return $result;
}

function getAnswer($message) {
    $query = 'SELECT a.* FROM bot_answer as a LEFT JOIN bot_question_answer as qa ON a.id = qa.answer_id LEFT JOIN bot_question as q ON qa.question_id = q.id WHERE (LOWER(question) LIKE "'.$message.'%" OR LOWER(keywords) LIKE "%'.$message.'%")  AND a.status = 1 ORDER BY RAND() LIMIT 1';
    $result =  mysql_query($query);
    return $result;
}

function insert_data($table,$data_array){
    foreach( array_keys($data_array) as $key ) {
        $fields[] = "`$key`";
        $data[] = '"' . mysql_real_escape_string($data_array[$key]) . '"';
    }

    $fields = implode(",", $fields);
    $data = implode(",", $data);

    if( mysql_query('INSERT INTO '.$table. '('.$fields.') VALUES ('. $data.')') ) {
        return mysql_insert_id();
    } else {
        return array( "mysql_error" => mysql_error() );
    }
}

    curl_close($ch);

?>