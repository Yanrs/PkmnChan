// OOP Way
fbChat = {
  bootChat: function() {
    var chatArea = $('#chatMsg'),
            that = this;

    // Load the messages every 5 seconds
    setInterval(this.getMessages, 5000);

    // Bind the keyboard event
    chatArea.bind('keydown', function(event) {
      if (event.keyCode === 13 && event.shiftKey === false) {
        var message = chatArea.val();

        if (message.length !== 0) {
          that.sendMessage(message);
          event.preventDefault();
        } else {
          alert('Provide a message to send!');
        }
      }
    });
  },
  sendMessage: function(message) {
    var that = this;
    $.ajax({
      url: '/ajax/add_msg.php',
      method: 'post',
      data: {msg: message},
      success: function(data) {
        $('#chatMsg').val('');
        that.getMessages();
      }
    });
  },
  getMessages: function() {
    $.ajax({
      url: '/ajax/get_messages.php',
      method: 'GET',
      success: function(data) {
        $('.msg-wgt-body').html(data);
      }
    });
  }
};

// Initialize the chat
//fbChat.bootChat();

// Procedural way
/**
 * Add a new chat message
 * 
 * @param {string} message
 */
function send_message(message) {
  $.ajax({
    url: '/ajax/add_msg.php',
    method: 'post',
    data: {msg: message},
    success: function(data) {
      $('#chatMsg').val('');
      get_messages();
    }
  });
}

/**
 * Get's the chat messages.
 */
function get_messages() {
  $.ajax({
    url: '/ajax/get_messages.php',
    method: 'GET',
    success: function(data) {
      $('.msg-wgt-body').html(data);
    }
  });
}

/**
 * Initializes the chat application
 */
function boot_chat() {
  var chatArea = $('#chatMsg');

  // Load the messages every 5 seconds
  setInterval(get_messages, 20000);

  // Bind the keyboard event
  chatArea.bind('keydown', function(event) {
    // Check if enter is pressed without pressing the shiftKey
    if (event.keyCode === 13 && event.shiftKey === false) {
      var message = chatArea.val();
      // Check if the message is not empty
      if (message.length !== 0) {
        send_message(message);
        event.preventDefault();
      } else {
        alert('Provide a message to send!');
        chatArea.val('');
      }
    }
  });
}

// Initialize the chat
boot_chat();