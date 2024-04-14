//import './bootstrap';

/**
 * Displays a message with the specified type and text.
 * Automatically fades out the message after 4 seconds.
 *
 * @param {string} type - The type of the message ('success' or 'error').
 * @param {string} text - The text of the message to be displayed.
 */
function message(type, text) {
    var alertClass = (type === 'success') ? 'alert-success' : 'alert-danger';
    var typeText = (type === 'success') ? 'Éxito' : 'Error';

    var html = '<div class="alert ' + alertClass + ' alert-dismissible" id="message">';
    html += '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
    html += '<strong>' + typeText + '</strong> ' + text;
    html += '</div>';

    var $message = $(html);
    $message.hide().appendTo('#messages').fadeIn();

    setTimeout(function() {
        $message.fadeOut(function() {
            $(this).remove();
        });
    }, 4000);
}

/**
 * Copies the specified text to the clipboard.
 * @param {string} text - The text to be copied.
 */
function copyToClipboard(text) {
  navigator.clipboard.writeText(text)
    .then(function() {
      message('success', 'Enlace copiado al portapapeles');
    })
    .catch(function() {
      message('error', 'No se pudo copiar el enlace');
    });
}

function queueAproved(btn) {
  $("#espera").hide();
  $("#rechazados").hide();
  $("#revision").hide();

  $("#btnespera").removeClass("active");
  $("#btnrechazados").removeClass("active")
  $("#btnrevision").removeClass("active")

  $("#" + btn).show();
  $("#btn" +btn).addClass("active");
}