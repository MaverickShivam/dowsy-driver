<html>
<head>
	<style>
	.button-group, .play-area {
  border: 1px solid grey;
  padding: 1em 1%;
  margin-bottom: 1em;
}

.button {
  padding: 0.5em;
  margin-right: 1em;
}

.play-area-sub {
  width: 47%;
  padding: 1em 1%;
  display: inline-block;
  text-align: center;
}

#capture {
  display: none;
}

#snapshot {
  display: inline-block;
  width: 320px;
  height: 240px;
}

	</style>
</head>
<body>
<!-- The buttons to control the stream -->
<div class="button-group" style="display:none;">
  <button id="btn-start" type="button" class="button">Start Streaming</button>
  <button id="btn-stop" type="button" class="button">Stop Streaming</button>
  <button id="btn-capture" type="button" class="button">Capture Image</button>
</div>

<!-- Video Element & Canvas -->
<div class="play-area">
  <div class="play-area-sub">
    <h3 id="status">The Stream</h3>
    <video id="stream" width="320" height="240"></video>
  </div>
  <div class="play-area-sub" style="display:none;">
    <h3>The Capture</h3>
    <canvas id="capture" width="320" height="240"></canvas>
    <div id="snapshot"></div>
  </div>
</div>
<script>

var btnStart = document.getElementById( "btn-start" );
var btnStop = document.getElementById( "btn-stop" );
var btnCapture = document.getElementById( "btn-capture" );

// The stream & capture
var stream = document.getElementById( "stream" );
var capture = document.getElementById( "capture" );
var snapshot = document.getElementById( "snapshot" );

// The video stream
var cameraStream = null;

// Attach listeners
btnStart.addEventListener( "click", startStreaming );
btnStop.addEventListener( "click", stopStreaming );
btnCapture.addEventListener( "click", captureSnapshot );

// Start Streaming
function startStreaming() {

	var mediaSupport = 'mediaDevices' in navigator;

	if( mediaSupport && null == cameraStream ) {

		navigator.mediaDevices.getUserMedia( { video: true } )
		.then( function( mediaStream ) {

			cameraStream = mediaStream;

			stream.srcObject = mediaStream;

			stream.play();
		})
		.catch( function( err ) {

			console.log( "Unable to access camera: " + err );
		});
	}
	else {

		alert( 'Your browser does not support media devices.' );

		return;
	}
}

// Stop Streaming
function stopStreaming() {

	if( null != cameraStream ) {

		var track = cameraStream.getTracks()[ 0 ];

		track.stop();
		stream.load();

		cameraStream = null;
	}
}

function captureSnapshot() {

	if( null != cameraStream ) {

		var ctx = capture.getContext( '2d' );
		var img = new Image();

		ctx.drawImage( stream, 0, 0, capture.width, capture.height );
		
		
		img.src		= capture.toDataURL( "image/png" );
		img.width	= 240;
		
		img.setAttribute("id", "myimg");
		
		snapshot.innerHTML = '';

		snapshot.appendChild( img );
		
		
		//alert(document.getElementById("myimg").src);
		var xhr = new XMLHttpRequest();
		xhr.open('POST', 'saveimg.php', true);
		xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		xhr.onload = function () {
			// do something to response
			//alert(this.responseText);
		};
		xhr.send("blob="+document.getElementById("myimg").src);
		
		//document.getElementById("status").innerHTML="<?php echo file_get_contents('status.text'); ?>";
		//document.getElementById("status").style["color"]="rgb<?php echo file_get_contents('color.text'); ?>";
		

		var yhttp = new XMLHttpRequest();
		yhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
			   // Typical action to be performed when the document is ready:
			   document.getElementById("status").innerHTML= yhttp.responseText;
			}
		};
		yhttp.open("GET", "getpythondata.php?status=1", true);
		yhttp.send();
		
		var zhttp = new XMLHttpRequest();
		zhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
			   // Typical action to be performed when the document is ready:
			   document.getElementById("status").style["color"]= "rgb"+zhttp.responseText;
			}
		};
		zhttp.open("GET", "getpythondata.php?color=1", true);
		zhttp.send();
	}
}
window.setInterval(captureSnapshot,500);
startStreaming();
</script>

</body>
</html>