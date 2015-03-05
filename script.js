$(document).ready(function() {

  // hide text & email input & removal forms at first
  $('.inputText').hide();
  $('.inputEmail').hide();
  $('.success').hide();
  $('.removeAlert').hide();
  $('.removeSuccess').hide();

  // user clicks to enter text info
  $('.chooseText').click(function() {
    $('.textOrEmail').replaceWith($('.inputText').show());
    //$('.remover').hide();
  });

  // user clicks to enter email info
  $('.chooseEmail').click(function() {
    $('.textOrEmail').replaceWith($('.inputEmail').show());
    //$('.remover').hide();
  });

  // toggle select all text in text areas
  $(":text").focus(function() {
    $(this).select();

    // work around for google chrome
    $(this).mouseup(function() {
      $(this).unbind("mouseup");
      return false;
    });
  });

  // create a string of data to post for adding user to db
  var phone = $('input:phone').val();
  var alertPrice = $('input:alertPrice').val();
  var email = $('input:email').val();
  var alertPriceEmail = $('input:alertPriceEmail').val();
  var textDataString = 'phone=' + phone + '&alertPrice=' + alertPrice;
  var emailDataString = 'email=' + email + '&alertPriceEmail=' + alertPriceEmail;

  $('.textSubmit').click(function() {
    //validate & process here
    //// http://net.tutsplus.com/tutorials/javascript-ajax/submit-a-form-without-page-refresh-using-jquery/
    $.ajax({
      type: "POST",
      url: "process.php",
      data: textDataString,
      success: function() {
        $('.textOrEmail').hide();
        $('.inputEmail').hide();
        $('.inputText').hide();
        $('.success').show();
      }
    });
    return false;
  });

  $('.emailSubmit').click(function() {
    //validate & process here
    //// http://net.tutsplus.com/tutorials/javascript-ajax/submit-a-form-without-page-refresh-using-jquery/
    $.ajax({
      type: "POST",
      url: "process.php",
      data: emailDataString,
      success: function() {
        $('.textOrEmail').hide();
        $('.inputEmail').hide();
        $('.inputText').hide();
        $('.success').show();
      }
    });
    return false;
  });

  // display remove textbox
  $('.removeAlert').click(function() {
    $('.remover').show();
  });

  $removerString = 'phoneOrEmail=' + $('input:phoneOrEmail').val();

  $('.removeSubmit').click(function() {
    $.ajax({
      type: "POST",
      url: "remover.php",
      data: removalString,
      success: function() {
        $('.remover').hide();
        $('.removeSuccess').show();
      }
    });
  });

});
