<?php
//connect
$hostname = "119.81.221.66";
$username = "internal_bot";
$password = "O6B?coX2CZyJ";
$database = "internal_bot";
define('bot_id',1, true);

//$datetime = date('Y-m-d H:i:s');

if (!$dbhandle = mysql_connect($hostname, $username, $password)) {
    echo 'Could not connect to mysql';
    exit;
}

if (!mysql_select_db($database, $dbhandle)) {
    echo 'Could not select database';
    exit;
}

$request = file_get_contents("php://input");
$input   = json_decode($request, true);
if ($input['event'] == 'webhook') {
    $webhook_response['status']         = 0;
    $webhook_response['status_message'] = "ok";
    $webhook_response['event_types']    = 'delivered';
    echo json_encode($webhook_response);
    die;
} else if ($input['event'] == "subscribed") {
    // when a user subscribes to the public account
} else if ($input['event'] == "conversation_started") {
    // when a conversation is started
    $sender_id          = $input['user']['id']; //unique viber id of user who sent the message
    $sender_name        = $input['user']['name']; //name of the user who sent the message

    $guest = checkIfUserExist($sender_id);
    if (mysql_num_rows($guest) > 0) {
        $guest_result = mysql_fetch_assoc($guest);
        $user_id      = $guest_result['id'];
    } else {
        $table      = 'bot_user';
        $data_array = array(
            'name' => $sender_name,
            'mobile' => '',
            'unique_id' => $sender_id,
            'source_id' => 2,
            'status' => 1,
            'date' => date('Y-m-d'),
            'bot_id' => bot_id
        );
        $user_id    = insert_data($table, $data_array);
    }
   

    $data2 = getPrimaryMessage();
    $result = mysql_fetch_assoc($data2);
    $message_to_reply = "Hi I'm Mira of CIT                                     ---------------                                                                                                 Im here to help you. ".$result['answer'];

    $data['auth_token'] = "46def6b876e7d7ac-fa7288c831fba38d-6d2985fe10e02062";
    $data['receiver']   = $sender_id;
    $data['text']       = $message_to_reply;
    $data['type']       = 'text';
    $data['BgColor']    = '#FFFFFF';

    $keyboard_array['Type']='keyboard';
    $keyboard_array['DefaultHeight']=false;
    $keyboard['keyboard']=$keyboard_array;

    if($result['is_menu_driven']==1){

      $options = getOptions($result['id']);

      while($row = mysql_fetch_array($options)){
        $button = array();
        $button['Columns']=6;
        $button['Rows']=1;
        $button['TextVAlign']="middle";
        $button['TextHAlign']="center";
        $button['TextOpacity']="100";
        $button['TextSize']="regular";
        $button['ActionType']="reply";
        $button['ActionBody']=$row['question'];
        $button['BgColor'] = '#2db9b9';
        $button['Text']=$row['question'];
        $keyboard['keyboard']['Buttons'][]=$button;
      }
      
    }

    $data['keyboard']=$keyboard['keyboard'];

    //here goes the curl to send data to user
    $ch                 = curl_init("https://chatapi.viber.com/pa/send_message");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json'
    ));
    $result = curl_exec($ch);
} elseif ($input['event'] == "message") {
    /* when a user message is received */
    $type               = $input['message']['type']; //type of message received (text/picture)
    $message            = $input['message']['text']; //actual message the user has sent
    $sender_id          = $input['sender']['id']; //unique viber id of user who sent the message
    $sender_name        = $input['sender']['name']; //name of the user who sent the message

    $data['auth_token'] = "46def6b876e7d7ac-fa7288c831fba38d-6d2985fe10e02062";
    $data['receiver']   = $sender_id;
    $data['type'] = 'text';
    $a = '';
    $b = '';
    
    $guest = checkIfUserExist($sender_id);
    if (mysql_num_rows($guest) > 0) {
        $guest_result = mysql_fetch_assoc($guest);
        $user_id      = $guest_result['id'];
    } else {
        $table      = 'bot_user';
        $data_array = array(
            'name' => $sender_name,
            'mobile' => '',
            'unique_id' => $sender_id,
            'source_id' => 2,
            'status' => 1,
            'date' => date('Y-m-d'),
            'bot_id' => bot_id
        );
        $user_id    = insert_data($table, $data_array);
    }
    
    $data_array = array(
        'user_id' => $user_id,
        'message' => $message,
        'is_guest' => 1,
        'status' => 1,
        'date_time' => date('Y-m-d H:i:s')
    );
    
    $res = insert_data('bot_conversation', $data_array);
    
    $data2 = getAnswer($message);
    $count = mysql_num_rows($data2);
    if ($count > 0) {
        $result           = mysql_fetch_assoc($data2);
        $message_to_reply = $result['answer'];
        $data_array       = array(
            'user_id' => $user_id,
            'message' => $result['answer'],
            'is_guest' => 0,
            'status' => 1,
            'date_time' => date('Y-m-d H:i:s')
        );
        insert_data('bot_conversation', $data_array);
        if($result['is_attachment']==1){
            $a = 1;
        }
        if($result['is_menu_driven']==1){
            $b = 1;
        }
    } else {
        $data2            = getUnAnswer();
        $result           = mysql_fetch_assoc($data2);
        $message_to_reply = $result['message'];
        $data_array       = array(
            'user_id' => $user_id,
            'message' => $message,
            'status' => 1,
            'is_answered' => 0,
            'date_time' => date('Y-m-d H:i:s')
        );
        insert_data('bot_unlisted_question', $data_array);
        
        $data_array = array(
            'user_id' => $user_id,
            'message' => $result['message'],
            'is_guest' => 0,
            'status' => 1,
            'date_time' => date('Y-m-d H:i:s')
        );
        insert_data('bot_conversation', $data_array);
    }
    if($a==1){
      $attachment = getAttachment($result['id']);
      $file           = mysql_fetch_assoc($attachment);
      $file_path =  $file['path'];
      $data['tracking_data'] = 'tracking data';
      $data['type'] = 'file';
      $data['media'] = 'https://shop.clickhealth.com.ph/viberfiles/'.$file_path;
      $data['size'] = 10000;
      $data['file_name'] = $file_path;
    }
    else{
      $data['text'] = $message_to_reply;
    }

    if(strtolower($message)=='call' || strtolower($message)=='Call our Helphotline') {
      $data['type'] = 'contact';
      unset($data['text']);
      $data['contact'] = array('name' => 'Mira', 'phone_number' => '09178925619');
    }
    

    if($b==1){
      $data['BgColor']    = '#FFFFFF';

      $keyboard_array['Type']='keyboard';
      $keyboard_array['DefaultHeight']=false;
      $keyboard['keyboard']=$keyboard_array;

      $options = getOptions($result['id']);

      while($row = mysql_fetch_array($options)){
        $button = array();
        $button['Columns']=6;
        $button['Rows']=1;
        $button['TextVAlign']="middle";
        $button['TextHAlign']="center";
        $button['TextOpacity']="100";
        $button['TextSize']="regular";
        $button['ActionType']="reply";
        $button['ActionBody']=$row['question'];
        $button['BgColor'] = '#2db9b9';
        $button['Text']=$row['question'];
        $keyboard['keyboard']['Buttons'][]=$button;
      }
       $data['keyboard']=$keyboard['keyboard'];
      
    }

   

    //here goes the curl to send data to user
    $ch           = curl_init("https://chatapi.viber.com/pa/send_message");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json'
    ));
    $result = curl_exec($ch);
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
function getPrimaryMessage() {
    $query = "SELECT * FROM bot_answer WHERE is_primary_message = 1 AND bot_id = 1 AND is_primary = 1 AND status = 1";
    $result = mysql_query($query);
    return $result;
}
function getUnAnswer(){
    $query  = "SELECT message FROM bot_no_answer WHERE status = 1 ORDER BY RAND() LIMIT 1";
    $result = mysql_query($query);
    return $result;
}
function checkIfUserExist($sender_id)
{
    $query  = "SELECT * FROM bot_user WHERE unique_id = '" . $sender_id . "' AND bot_id = '".bot_id."' AND status = 1";
    $result = mysql_query($query);
    return $result;
}

function getAnswer($message)
{
    $query  = 'SELECT a.* FROM bot_answer as a LEFT JOIN bot_question_answer as qa ON a.id = qa.answer_id LEFT JOIN bot_question as q ON qa.question_id = q.id WHERE (LOWER(question) LIKE "' . $message . '%" OR LOWER(keywords) LIKE "%' . $message . '%")  AND a.status = 1 ORDER BY RAND() LIMIT 1';
    $result = mysql_query($query);
    return $result;
}

function insert_data($table, $data_array)
{
    foreach (array_keys($data_array) as $key) {
        $fields[] = "`$key`";
        $data[]   = '"' . mysql_real_escape_string($data_array[$key]) . '"';
    }
    
    $fields = implode(",", $fields);
    $data   = implode(",", $data);
    
    if (mysql_query('INSERT INTO ' . $table . '(' . $fields . ') VALUES (' . $data . ')')) {
        return mysql_insert_id();
    } else {
        return array(
            "mysql_error" => mysql_error()
        );
    }
}
?>