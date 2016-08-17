function showToast(msg) {
  $('#toast').removeClass('error').text(msg).fadeIn(400).delay(3000).fadeOut(400);
}

function showError(msg) {
  $('#toast').addClass('error').text(msg).fadeIn(400).delay(3000).fadeOut(400);
}
