
var intervalOne = null;
var clockHidden = true;
var clickCount = null;
var array1 = null;
var array2 = null;
var numberOfTries = 0;

initPuzzle();

function initPuzzle() {
  setTime();
  startClock();
  clickCount = 0;
}

function shuffle (array) {
  //console.log("shuffling...");
  var i = 0;
  var j = 0;
  var temp = null;
  for (i = array.length - 1; i > 0; i -= 1) {
    j = Math.floor(Math.random() * (i + 1));
    temp = array[i];
    array[i] = array[j];
    array[j] = temp;
  }
  return array;
}

function clockClicked(evt) {
  var puzzleId = evt.target.parentNode.id.substring("puzzle".length);
  completePuzzle(puzzleId);
}

function moveClock(xloc, yloc) {
  // move clock to the top-most level so it does not move under things
  $("#clk1").remove().appendTo("svg");
  $('#clk1').velocity({
    x: xloc,
    y: yloc
  }, {
    duration: 1000,
    easing: "spring"
  } );
}

function startClock() {
  intervalOne = setInterval(function() {setTime()}, 1000);
}

function stopClock() {
  clearInterval(intervalOne);
}

function setTime(){
  var d = new Date();
  var deltasec = 6*d.getSeconds();
  var deltamin = 6*d.getMinutes();
  var deltahour = 30*(d.getHours()%12) + d.getMinutes()/2;
  $('#sec').attr('transform','rotate('+deltasec+' 0 0)');
  $('#min').attr('transform','rotate('+deltamin+' 0 0)');
  $('#hour').attr('transform','rotate('+deltahour+' 0 0)');
}

function getDistance(startX, startY, endX, endY) {
  return Math.sqrt(Math.pow((startX - endX), 2) + Math.pow((startY - endY), 2));
}

function r2d(x) {
    return x / (Math.PI / 180);
}

function d2r(x) {
    return x * (Math.PI / 180);
}
