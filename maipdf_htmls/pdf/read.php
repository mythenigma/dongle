<!DOCTYPE html>
<html>
<meta name="viewport" content="width=device-width, initial-scale=1">
<body ontouchstart="showCoordinates(event)" ontouchmove="bshowCoordinates(event)">

<h1>The TouchEvent's touches Property</h1>

<p>Touch somewhere in this document.</p>

<p>The horizontal and vertical coordinates of the touch are: <span id="demo"></span><span id="demo2"></span></p>

<p><strong>Note:</strong> This examples uses touches<strong>[0]</strong> meaning that it will only show the coordinates of one finger (the first).</p>

<p><strong>Note:</strong> Touch events works for touch devices only. <span id="demo3"></p>

<script>
function showCoordinates(event) {
	
   x = event.touches[0].clientX;
  y = event.touches[0].clientY;
  
  document.getElementById("demo3").innerHTML = x + ", " + y;
}
</script>

<script>
function bshowCoordinates(event) {
	document.getElementById("demo").innerHTML = x + ", " + y;
  var x1 = event.touches[0].clientX;
  var y1 = event.touches[0].clientY;
  
  document.getElementById("demo2").innerHTML = x1 + ", " + y1;
}
</script>



</body>
</html>