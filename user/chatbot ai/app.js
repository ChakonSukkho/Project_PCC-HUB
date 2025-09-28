document.getElementById('send-btn').addEventListener('click', function() {
  var userInput = document.getElementById('user-input').value;
  if (userInput.trim() !== '') {
    var userMessage = document.createElement('div');
    userMessage.classList.add('message');
    userMessage.classList.add('user-message');
    userMessage.innerHTML = `<p>${userInput}</p>`;
    document.querySelector('.chat-body').appendChild(userMessage);
    document.getElementById('user-input').value = '';

    // Scroll to the bottom of the chat
    document.querySelector('.chat-body').scrollTop = document.querySelector('.chat-body').scrollHeight;
  }
});

document.querySelectorAll('.emoji').forEach(function(button) {
  button.addEventListener('click', function() {
    var emoji = button.innerText;
    var userMessage = document.createElement('div');
    userMessage.classList.add('message');
    userMessage.classList.add('user-message');
    userMessage.innerHTML = `<p>${emoji}</p>`;
    document.querySelector('.chat-body').appendChild(userMessage);

    // Scroll to the bottom of the chat
    document.querySelector('.chat-body').scrollTop = document.querySelector('.chat-body').scrollHeight;
  });
});
