<?php

require_once __DIR__ . '/../../core/FbChatMock.php';

$chat = new FbChatMock();
$messages = $chat->getMessages();
$chat_converstaion = array();

if (!empty($messages)) {
  $chat_converstaion[] = '<table>';
  foreach ($messages as $message) {
    $msg = htmlentities($message['message'], ENT_NOQUOTES);
    $user_name = ucfirst($message['username']);
    $sent = date('F j, Y, g:i a', $message['sent_on']);
    $chat_converstaion[] = <<<MSG
      <tr class="msg-row-container">
        <td>
          <div class="msg-row">
            <div class="avatar"></div>
            <div class="message">
              <span class="user-label"><a href="#" style="color: #6D84B4;">{$user_name}</a> <span class="msg-time">{$sent}</span></span><br/>{$msg}
            </div>
          </div>
        </td>
      </tr>
MSG;
  }
  $chat_converstaion[] = '</table>';
} else {
  echo '<span style="margin-left: 25px;">No chat messages available!</span>';
}

echo implode('', $chat_converstaion);
?>