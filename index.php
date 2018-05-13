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
        answer: "LoginFunction"
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
    textInputAction: "checkUserName",
  },
  signupPassword: {
    says: ["Enter a password "],
    textInputAction: "createUser",
  },
  loginUsername: {
    says: ["Enter a username"],
    textInputAction: "storeUserName",
  },
  loginUsernameRedo: {
    says: ["Sorry, this account was not found! Please sign up if you are a new user"],
  },
  loginPassword: {
    says: ["Enter a password "],
    textInputAction: "loginUser",
  },
  /**
  * More properties are added in predictionSelectionFunction.
  **/
  predictionSelection: {
    says: ["Who do you think will win?"],
    answer: "confidencePicker"
  },
  confidencePicker: {
    says: ["How confident are you of this prediction?"],
    textInputAction: "logPrediction"
  },
  thankYou: {
    says: ["Your prediction has been successfully logged. You can check your position on the leaderboard post the game!"],
    reply: [
      {
        question: "ByeBye!"
      },
      {
        question: "Continue predicting",
        answer: "fetchUpcomingGamesFunction"
      }
    ]
  },
  stall: {
    says: ["I'm just booting up the systems, hold on tight!"]
  }
}

signupFunction = function() {
  chatWindow.talk(convo, "signupUsername")
  showInputBox('Enter username here');
}

LoginFunction = function() {
  chatWindow.talk(convo, "loginUsername")
  showInputBox('Enter username here');
}

predictionSelectionFunction = function(option) {
  var option = JSON.parse(decodeURIComponent(option));
  convo.predictionVariables = {};
  convo.predictionVariables.gameId = option.gameId;
  option.matchup.forEach(function(game){
    game.answer = 'addConfidenceScore';
  })
  convo.predictionSelection.reply = option.matchup;
  chatWindow.talk(convo, "predictionSelection");
}

addConfidenceScore = function(option) {
  convo.predictionVariables.teamId = option;
  chatWindow.talk(convo, "confidencePicker");
  showInputBox('Enter a whole number between 50 and 100 here');
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

//smh
fetchUpcomingGamesFunction = function() {
  fetchUpcomingGames();
}


/**
* Helper functions to make ajax calls
**/

fetchUpcomingGames = function() {
  $.ajax({
    url: 'php/fetchUpcomingGames.php',
    type: 'POST',
    dataType: 'json',
    data: {},
    success: function(response) {
      if(response.data) {
        convo.upcomingGames = {};
        convo.upcomingGames.says = ["Predict the following upcoming games"];
        convo.upcomingGames.reply = [];
        response.data.forEach(function(game){
          game.convGameFormat.answer =  'predictionSelectionFunction';
          game.convGameFormat.option = encodeURIComponent(JSON.stringify(game.convGameFormat.option));
          convo.upcomingGames.reply.push(game.convGameFormat);
        });
        chatWindow.talk(convo, "upcomingGames");
      }
    }
  })
}


if(Cookies.get("userHash")) {
  // chatWindow.talk(convo, 'stall');
  fetchUpcomingGames();
} else {
  chatWindow.talk(convo, "gambit")
}

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

storeUserName = function(username) {
  convo.userVariables = {};
  convo.userVariables.username = username;
  chatWindow.talk(convo, "loginPassword");
  showInputBox('Enter password here');
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
      fetchUpcomingGames();
    }
  });
}

loginUser = function(password) {
  var data = {
    username: convo.userVariables.username,
    password: password
  }

  $.ajax({
    url: 'php/loginUser.php',
    type: 'POST',
    dataType: 'json',
    data: data,
    success: function(response) {
      if(response.status === 'found') {
        hideInputBox();
        var userHash = response.userHash;
        document.cookie = "userHash=" + userHash;
        convo.welcomeBack = {};
        convo.welcomeBack.says = ['Welcome back '+ convo.userVariables.username];
        chatWindow.talk(convo, "welcomeBack");
        setTimeout(function(){ fetchUpcomingGames(); }, 1000);
      } else {
        chatWindow.talk(convo, "loginUsernameRedo");
        chatWindow.talk(convo, "gambit");
      }
    }
  });
}

logPrediction = function(confidenceScore) {
  var data = {
    confidence: confidenceScore,
    teamId: convo.predictionVariables.teamId,
    gameId: convo.predictionVariables.gameId,
    userHash: Cookies.get("userHash")
  };

  $.ajax({
    url: 'php/logPrediction.php',
    type: 'POST',
    dataType: 'json',
    data: data,
    success: function(response) {
      hideInputBox();
      chatWindow.talk(convo, "thankYou");
    }
  });
}


</script>
<!--   <?php
    foreach (glob("component/js/*.js") as $js) {
      echo "<script type='text/javascript' src='$js'></script>\n";
    }
  ?> -->
</body>
