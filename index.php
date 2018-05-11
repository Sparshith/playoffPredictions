<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Ola3po | NBA predictions</title>
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
<script  type="application/javascript"  src="https://cdn.jsdelivr.net/npm/js-cookie@2/src/js.cookie.min.js"></script>
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
    says: ["Hi, I am Ola3po, I will be helping you with predicting games."],
    reply: [
      {
        question: "Sign me up",
        answer: "signupFunction"
      },
      {
        question: "Login",
        answer: "Login"
      },
      {
        question: "How does this work?",
        answer: "intro"
      }
    ]
  },
  signupUsername: {
    says: ["Enter a username"],
    textInputAction: "checkUserName",
  },
  signupUsernameRedo: {
    says: ["Sorry, that username is already taken. Please enter another username!"],
    textInputAction: "checkUserName",  },
  signupPassword: {
    says: ["Enter a password "],
    textInputAction: "createUser",
  },
  upcomingGames: {
    says: ["Predict the following upcoming games"],
    reply: [
      {
        question: "San Antonio vs Golden State",
        followUpReply: "SASGSW",
        answer: "predictionSelectionFunction"
      },
      {
        question: "Washington vs Torongo",
        option: "WASTOR",
        answer: "predictionSelectionFunction"
      },
      {
        question: "Miami vs Philadelphia",
        option: "MIAPHI",
        answer: "predictionSelectionFunction"
      },
      {
        question: "New Orleans vs Portland",
        option: "NEWPOR",
        answer: "predictionSelectionFunction"
      },
      {
        question: "Milwaukee vs Boston",
        option: "MILBOS",
        answer: "predictionSelectionFunction"
      }
    ]
  },
  predictionSelection: {
    says: ["Who do you think will win?"]
  },
  confidencePicker: {
    says: ["How confident are you of this prediction?"]
  }
}

signupFunction = function() {
  chatWindow.talk(convo, "signupUsername")
  showInputBox('Enter username here');
}

predictionSelectionFunction = function(option) {
  chatWindow.talk(convo, "predictionSelection");
}

/**
* UI functions
**/
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

if(Cookies.get("userHash")) {
  chatWindow.talk(convo, "upcomingGames");
} else {
  chatWindow.talk(convo, "gambit")
}
/**
* Helper functions to make ajax calls
**/

checkUserName = function(username) {
  var data = {
    username: username
  };

  $.ajax({
    url: 'php/checkUserName.php',
    type: 'POST',
    dataType: 'json',
    data: data,
    success: function(response) {
      if(response.status == 'new') {
        convo.userVariables = {};
        convo.userVariables.username = username;
        chatWindow.talk(convo, "signupPassword");
        showInputBox('Enter password here');
      } else if(response.status == 'already_exists') {
        chatWindow.talk(convo, "signupUsernameRedo");
      }
    }
  });
}

createUser = function(password) {
  var data = {
    username: convo.userVariables.username,
    password: password
  }

  $.ajax({
    url: 'php/createUser.php',
    type: 'POST',
    dataType: 'json',
    data: data,
    success: function(response) {
      hideInputBox();
      var userHash = response.userHash;
      document.cookie = "userHash=" + userHash;
    }
  });
}


</script>
<script src="javascript/custom.js"></script>
<!--   <?php
    foreach (glob("component/js/*.js") as $js) {
      echo "<script type='text/javascript' src='$js'></script>\n";
    }
  ?> -->
</body>
