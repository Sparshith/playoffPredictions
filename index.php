<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Playoff Korv3r</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Dosis:400,700,800,600,300">
  <?php
    foreach (glob("component/styles/*.css") as $css) {
      echo "<link type='text/css' rel='stylesheet' href='$css'>\n";
    }
  ?>
  <style>
  body {
    background: #2f2f2f;
    font-family: "Dosis", Helvetica, Arial, sans-serif;
  }
  .bubble-container {
    background: #f4ee42;
    height: 100vh;
  }
  .bubble-container .input-wrap textarea {
    margin: 0;
    width: calc(100% - 30px);
  }
  </style>
</head>
<body>
<div id="chat"></div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script src="component/Bubbles.js"></script>
<script type="text/javascript">
var chatWindow = new Bubbles(document.getElementById("chat"), "chatWindow", {
  inputCallbackFn: function(o) {
    if(o.convo[o.standingAnswer].textInputAction) {
      var textFuncToCall = o.convo[o.standingAnswer].textInputAction;
      var textInput = o.input;
      window[textFuncToCall](textInput);
    }
  }
}) 


var convo = {
  gambit: {
    says: ["Hi", "Are you a new user?"],
    reply: [
      {
        question: "Yes",
        answer: "signupFunction"
      },
      {
        question: "No",
        answer: "Login"
      }
    ]
  },
  signupUsername: {
    says: ["Enter a username"],
    textInputAction: "storeUserName",
  },
  signupPassword: {
    says: ["Enter a password "],
    textInputAction: "storePassword",
  }
}

storePassword = function(password) {
  console.log("username", convo.userVariables.username);
  console.log("password", password);
  hideInputBox();
  /**
  * Make API call to register user here
  **/
}

storeUserName = function(username) {
  convo.userVariables = {};
  convo.userVariables.username = username;
  chatWindow.talk(convo, "signupPassword");
  showInputBox('Enter password here');
}

signupFunction = function() {
  chatWindow.talk(convo, "signupUsername")
  showInputBox('Enter username here');
}

showInputBox = function(placeholderText){
  setTimeout(function() {
    var textField = $('.bubble-container .input-wrap textarea');
    textField.fadeIn().attr("placeholder", placeholderText).val("").focus().blur();
  }, 1000)
}

hideInputBox = function() {
  var textField = $('.bubble-container .input-wrap textarea');
  textField.fadeOut().attr("placeholder", "").val("").focus().blur();
}

// pass JSON to your function and you're done!
chatWindow.talk(convo, "gambit")
</script>
<!--   <?php
    foreach (glob("component/js/*.js") as $js) {
      echo "<script type='text/javascript' src='$js'></script>\n";
    }
  ?> -->
</body>
