var svgns = "http://www.w3.org/2000/svg";
var container = document.getElementById('sierpinski');
var counter = 0;
var endCount = 0;
var plotPoint = null;
var myFiller = "#db1717";

var myX = 450;
var myY = 250;

var myX1 = 450;
var myY1 = 32;

var myX2 = 200;
var myY2 = 465;

var myX3 = 700;
var myY3 = 465;

var myX4 = 450;
var myY4 = 250;

var myRadius = 1;
var colorOff = [true,true,true];
var availableColors = ["#db1717", "#01c131", "#3032e7","#202020"];
var currentColors = ["#202020", "#202020", "#202020"];
var myString = "Size of Points: r = 1";

$(document).ready(function () {
    'use strict';
})

function toggleColor(evt,myVertex) {
  console.log(myVertex);
  console.log(colorOff[myVertex]);
  if (colorOff[myVertex]) {
    colorOff[myVertex] = false;
    currentColors[myVertex] = availableColors[myVertex];
  } else {
    colorOff[myVertex] = true;
    currentColors[myVertex] = availableColors[3];
  }
  var circleName = "#cir"+myVertex;
  $(circleName).attr("fill",currentColors[myVertex]);
}

function startTriangle(evt,numberPoints,mySpeed) {
  $("#10-btn").attr("visibility","hidden");
  $("#1000-btn").attr("visibility","hidden");
  $("#5000-btn").attr("visibility","hidden");
  $("#10000-btn").attr("visibility","hidden");
  $("#r1-btn").attr("visibility","hidden");
  $("#r2-btn").attr("visibility","hidden");
  $("#r5-btn").attr("visibility","hidden");
  $("#txt2").text("");
  $("#txt3").text("");
  counter = 0;
  endCount = numberPoints;
  plotPoint = setInterval(myPlotter, mySpeed);
}

function myPlotter() {

  var randomVertex = Math.floor(Math.random()*3);
  // console.log(randomVertex);
  if (randomVertex == 0) {
    myX = (myX1 + myX4)/2;
    myY = (myY1 + myY4)/2;
  } else if (randomVertex == 1) {
    myX = (myX2 + myX4)/2;
    myY = (myY2 + myY4)/2;
  } else {
    myX = (myX3 + myX4)/2;
    myY = (myY3 + myY4)/2;
  }
  myX4 = myX;
  myY4 = myY;

  var circle = document.createElementNS(svgns, 'circle');
  circle.setAttributeNS(null, 'cx', myX);
  circle.setAttributeNS(null, 'cy', myY);
  circle.setAttributeNS(null, 'r', myRadius);
  // circle.setAttributeNS(null, 'stroke', "#202020");
  circle.setAttributeNS(null, 'fill', currentColors[randomVertex]);
  sierpinski.appendChild(circle);

  counter++;
  if (counter>endCount) {
    clearInterval(plotPoint);
    $("#10-btn").attr("visibility","visible");
    $("#1000-btn").attr("visibility","visible");
    $("#5000-btn").attr("visibility","visible");
    $("#10000-btn").attr("visibility","visible");
    $("#r1-btn").attr("visibility","visible");
    $("#r2-btn").attr("visibility","visible");
    $("#r5-btn").attr("visibility","visible");
    $("#txt2").text("Points to Plot");
    $("#txt3").text(myString);
  }
}

function setRadius(evt,theRadius) {
  myString = "Size of Points: r = " + theRadius;
  $("#txt3").text(myString);
  myRadius = theRadius;
}

function reloadPage(evt) {
  location.reload();
}