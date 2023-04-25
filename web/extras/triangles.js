// puzzle 2 triangles to make a circle

var  svgns = "http://www.w3.org/2000/svg";
var  xlinkns = "http://www.w3.org/1999/xlink";

var intervalOne = null;
var triangleCount = 0;
var angleTri = 0;
var j = Math.floor(Math.random() * 9);

var colorArray = ["#0038ff","#ff9100","#ffd600","#21af0a","#29701b",
                  "#16c691","#6532ad","#a2376a","#b19e1d","#1e7c95"];

$(document).ready(function () {
    'use strict';
    setTimeout(function(){
      displayContinue();
    },1000);
});

function displayContinue() {
  var buttonName = "#btn-0";
  $(buttonName).attr("visibility","visible");
  $(buttonName).velocity("fadeIn", { duration: 1000 });
}

function continueClicked(evt) {
  var buttonName = "#btn-0";
  $(buttonName).removeClass();
  $(buttonName).velocity("fadeOut", { complete: hideButton, duration: 1000 });
  generateTriangles();
}

function hideButton() {
  $("#btn-0").attr("visibility","hidden");
  moveTime();
}

function generateTriangles() {
  intervalOne = setInterval(function() {setTri()}, 40);
}

function setTri(){
  angleTri = 5*triangleCount;
  log(angleTri);
  var x1 = 500;
  var y1 = 300;
  var angleRads = (angleTri*Math.PI)/180;
  log(angleRads);
  var x2 = 500 + 290*Math.cos(angleRads);
  var y2 = 300 - 300*Math.sin(angleRads);
  log(x2 + ", " + y2);
  //
  var svg = document.getElementsByTagName('svg')[0];
  var theTri = document.createElementNS("http://www.w3.org/2000/svg", 'polygon');
  var theTriId = "my-tri-"+triangleCount;
  var theTriIdHash = "#my-tri-"+triangleCount;
  var pointString = x1+","+y1+" "+x2+","+y1+" "+x2+","+y2+" "+x1+","+y1;
  theTri.setAttribute("points", pointString);
  //j = Math.floor(Math.random() * 9);
  theTri.style.fill = colorArray[j];
  theTri.style.opacity = ".5";
  svg.appendChild(theTri);
  //
  if (angleTri < 360) {
    triangleCount++;
  } else {
    clearInterval(intervalOne);
  }
}

function moveTime() {
  moveClock(500, 370, 750, "svg#puzzle2");
  setTimeout(function(){
    showGlobe();
  },1000);
}

function showGlobe() {
  $("#textline-10").html("3");
  $("#textline-11").html("1");
  $("#textline-12").html("4");
  var globeName = "#globe-0";
  $(globeName).attr("visibility","visible");
  $(globeName).velocity("fadeIn", { duration: 500 });
}
