<?php
//connect
$hostname = "119.81.221.66";
$username = "internal_bot";
$password = "O6B?coX2CZyJ";
$database = "internal_bot";
$bot_id = 1;

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
    /*$sender_id = $input['user']['id']; //unique viber id of user who sent the message
    $sender_name = $input['user']['name']; //name of the user who sent the message
    $data['auth_token'] = "46def6b876e7d7ac-fa7288c831fba38d-6d2985fe10e02062";
    //46def6b876e7d7ac-fa7288c831fba38d-6d2985fe10e02062
    $data['receiver'] = $sender_id;
    $data['text'] = 'Welcome';
    $data['type'] = 'text';
    //here goes the curl to send data to user
    $ch = curl_init("https://chatapi.viber.com/pa/send_message");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    $result = curl_exec($ch);*/
} else if ($input['event'] == "conversation_started") {
    // when a conversation is started
    $sender_id          = $input['user']['id']; //unique viber id of user who sent the message
    $sender_name        = $input['user']['name']; //name of the user who sent the message
    $data['auth_token'] = "46def6b876e7d7ac-fa7288c831fba38d-6d2985fe10e02062";
    //46def6b876e7d7ac-fa7288c831fba38d-6d2985fe10e02062
    $data['receiver']   = $sender_id;
    $data['text']       = 'Welcome';
    $data['type']       = 'text';
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
    // here goes the data to send message back to the user
    //$data['auth_token'] = "46c4ce79c6e7d5da-a1f4b189bb1e959c-359d05560bf3736f";
    $data['auth_token'] = "46def6b876e7d7ac-fa7288c831fba38d-6d2985fe10e02062";
    //46def6b876e7d7ac-fa7288c831fba38d-6d2985fe10e02062
    $data['receiver']   = $sender_id;
    
    $guest = checkIfUserExist($sender_id);
    if (mysql_num_rows($guest) > 0) {
        $guest_result = mysql_fetch_assoc($guest);
        $user_id      = $guest_result['id'];
    } else {
        $table      = 'bot_user';
        $data_array = array(
            'name' => $sender_name,
            'mobile' => $sender_id,
            'status' => 1,
            'date' => date('Y-m-d')
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
    } else {
        $data2            = getUnAnswer();
        $result           = mysql_fetch_assoc($data2);
        $message_to_reply = $result['message'];
        $data_array       = array(
            'user_id' => $user_id,
            'message' => $message,
            'status' => 1,
            'is_answered' => 0
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
    
    $data['text'] = $message_to_reply;
    $data['type'] = 'text';
    //here goes the curl to send data to user
    $ch           = curl_init("https://chatapi.viber.com/pa/send_message");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json'
    ));
    $result = curl_exec($ch);
}

function getPrimaryMessage() {
    $query = "SELECT * FROM bot_answer WHERE is_primary_message = 1 AND is_primary = 1 AND status = 1";
    $result = mysql_query($query);
    return $result;
}
function getUnAnswer()
{
    $query  = "SELECT message FROM bot_no_answer WHERE status = 1 ORDER BY RAND() LIMIT 1";
    $result = mysql_query($query);
    return $result;
}
function checkIfUserExist($sender_id)
{
    $query  = "SELECT * FROM bot_user WHERE mobile = '" . $sender_id . "' AND bot_id = '".$bot_id."' AND status = 1";
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