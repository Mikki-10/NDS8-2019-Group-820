<?php

/**
 * 
 */
class TOFA
{
	
	private $myVoiceIt;

	function __construct()
	{
		require_once('VoiceIt2.php');
		$this->myVoiceIt = new VoiceIt\VoiceIt2("key_e550fafd14554c01b0a9be7c5fcd0f72", "tok_7f41bf2491704693ad9a5a8af2e9d4bd");
	}

	//$type = enrollment OR verification
	function voiceit($username, $id, $random_id, $type = "verification")
	{
		$voiceit_user_id = $this->voiceit_user_exists($username);

		/*
		if (isset($voiceit_user_id) && $voiceit_user_id != "") 
		{
			if ($this->voiceit_user_is_enrolled($username)) 
			{
				# code...
			}
			else
			{
				$type = "enrollment";
			}
		}
		*/

		if ($type === "enrollment") 
		{
			$data = $this->myVoiceIt->getAllVoiceEnrollments($voiceit_user_id);
			$data = json_decode($data, true);
			$random_id = $data["count"];
		}

		$this->record_voice($username, $id, $random_id, $type);

	}

	function voiceit_user_exists($username)
	{
		$LDAP = new LDAP();
		$data = $LDAP->get_voiceit_user_data($username);

		//my_debug_print($data, __FILE__, __LINE__, "on");

		if (isset($data["voiceit"]) && $data["voiceit"] != "" && $data["voiceit"] != null) 
		{
			return $data["voiceit"];
		}
		else
		{
			//my_debug_print("Making a new voiceit user", __FILE__, __LINE__, "on");
			$voiceit_data = $this->myVoiceIt->createUser();
			$voiceit_data = json_decode($voiceit_data, true);
						
			$LDAP = new LDAP();
			$LDAP->add_voiceit($username, $voiceit_data["userId"]);
			return $voiceit_data["userId"];
		}
	}

	function voiceit_user_is_enrolled($username)
	{
		//my_debug_print(debug_string_backtrace(), __FILE__, __LINE__, "on");

		$LDAP = new LDAP();
		$ldap_data = $LDAP->get_voiceit_user_data($username);

		if ($ldap_data === "false") 
		{
			//my_debug_print("die()", __FILE__, __LINE__, "on");
			$this->voiceit_user_exists($username);

			$auto = 1;
			?>	
			<script type="text/javascript">
			setTimeout(function()
			{
			   location.href = "/";
			}, <?php echo $auto; ?>);
			</script>
			<?php

			die("Error no voiceit user");
		}
		else
		{
			if ($ldap_data["voiceit_enrolled"] == "1") 
			{
				return true;
			}
			else
			{
				//my_debug_print(debug_string_backtrace(), __FILE__, __LINE__, "on");

				$data = $this->myVoiceIt->getAllVoiceEnrollments($ldap_data["voiceit"]);
				$data = json_decode($data, true);

				//my_debug_print($data, __FILE__, __LINE__, "on");
				
				if ($data["count"] >= 3) 
				{
					$LDAP->set_voiceit_enrolled($username, $ldap_data["voiceit"]);
					return true;
				}
				else
				{
					return false;
				}
			}
		}
	}

	function create_enrollment($voiceit_user_id, $contentLanguage, $phrase, $recording)
	{
		$data = $this->myVoiceIt->createVoiceEnrollment($voiceit_user_id, $contentLanguage, $phrase, $recording);
		$data = json_decode($data, true);

		//my_debug_print($data, __FILE__, __LINE__, "on");

		if ($data["responseCode"] == "SUCC") 
		{
			//Remove me later
			$auto = 1;
			?>	
			<script type="text/javascript">
			setTimeout(function()
			{
			   location.href = "/?ok&textConfidence=<?php echo $data["textConfidence"] ?>";
			}, <?php echo $auto; ?>);
			</script>
			<?php
			//echo "Enrollment ok";
			//echo "<pre>"; var_dump($data); echo "</pre>";
			return true;
		}
		else
		{
			//Block login
			$DB = new DB();
			$db_data = $DB->ssh_validated($_POST["id"]);

			$auto = 1;
			?>	
			<script type="text/javascript">
			setTimeout(function()
			{
			   location.href = "/?fail&responseCode=<?php echo $data["responseCode"] ?>";
			}, <?php echo $auto; ?>);
			</script>
			<?php
			//echo "<pre>"; var_dump($data); echo "</pre>";
			return false;
		}
	}


	//$type = enrollment OR verification
	function record_voice($username, $id, $random_id, $type)
	{
		?>
		<link rel="stylesheet" type="text/css" href="https://addpipe.com/simple-recorderjs-demo/style.css">

		<?php
		if ($type === "verification") 
		{
			?>
			<div style="text-align:center;">Say the phrase from the terminal if the session id match</div>
			<div style="text-align:center;">Session id: <?php echo $random_id; ?></div>
			<?php
		}
		elseif ($type === "enrollment") 
		{
			?>
			<div style="text-align:center;">Enrollment left: <?php echo 3-$random_id; ?></div>
			<?php
		}
		{

		}

		?>
		<br>
		<div id="controls" style="margin-top: 0px;">
	  	 <button id="recordButton">Record</button>
	  	 <button id="pauseButton" disabled>Pause</button>
	  	 <button id="stopButton" disabled>Stop</button>
	    </div>
	    <div id="formats">Format: start recording to see sample rate</div>
	  	<ol id="recordingsList"></ol>

	    <!-- inserting these scripts at the end to be able to use all the elements in the DOM -->
	  	<script src="https://cdn.rawgit.com/mattdiamond/Recorderjs/08e7abd9/dist/recorder.js"></script>
	  	<?php $this->get_voice_js_app($username, $id, $type); ?>
	  	<?php
	}


	function validate_voice($voiceit_user_id, $contentLanguage, $phrase, $recording, $db_id)
	{
		if ($this->myVoiceIt == null) 
		{
			echo "Error";
			echo "<br>NDS Stack trace<br>File: ".__FILE__." Function: ADD Line: ".__LINE__."<br>";

			my_debug_print("die()", __FILE__, __LINE__, "on");
			die();
		}
		else
		{
			$data = $this->myVoiceIt->voiceVerification($voiceit_user_id, $contentLanguage, $phrase, $recording);
			$data = json_decode($data, true);
		}

		if ($data["responseCode"] == "SUCC") 
		{
			$DB = new DB();
			$DB->update_confidence($db_id, $data["confidence"], $data["textConfidence"]);
			//Remove me later
			$auto = 1;
			?>	
			<script type="text/javascript">
			setTimeout(function()
			{
			   location.href = "/?ok";
			}, <?php echo $auto; ?>);
			</script>
			<?php
			//echo "Login ok";
			//echo "<pre>"; var_dump($data); echo "</pre>";
			return true;
		}
		else
		{
			//Block login
			$DB = new DB();
			$data = $DB->ssh_validated($_POST["id"]);

			$auto = 1;
			?>	
			<script type="text/javascript">
			setTimeout(function()
			{
			   location.href = "/?fail";
			}, <?php echo $auto; ?>);
			</script>
			<?php
			//echo "<pre>"; var_dump($data); echo "</pre>";
			return false;
		}
	}

	function get_voice_js_app($username, $id, $type)
	{
		?>
		<script> 
		//webkitURL is deprecated but nevertheless
		URL = window.URL || window.webkitURL;

		var gumStream; 						//stream from getUserMedia()
		var rec; 							//Recorder.js object
		var input; 							//MediaStreamAudioSourceNode we'll be recording

		// shim for AudioContext when it's not avb. 
		var AudioContext = window.AudioContext || window.webkitAudioContext;
		var audioContext //audio context to help us record

		var recordButton = document.getElementById("recordButton");
		var stopButton = document.getElementById("stopButton");
		var pauseButton = document.getElementById("pauseButton");

		//add events to those 2 buttons
		recordButton.addEventListener("click", startRecording);
		stopButton.addEventListener("click", stopRecording);
		pauseButton.addEventListener("click", pauseRecording);

		function startRecording() {
			console.log("recordButton clicked");

			/*
				Simple constraints object, for more advanced audio features see
				https://addpipe.com/blog/audio-constraints-getusermedia/
			*/
		    
		    var constraints = { audio: true, video:false }

		 	/*
		    	Disable the record button until we get a success or fail from getUserMedia() 
			*/

			recordButton.disabled = true;
			stopButton.disabled = false;
			pauseButton.disabled = false

			/*
		    	We're using the standard promise based getUserMedia() 
		    	https://developer.mozilla.org/en-US/docs/Web/API/MediaDevices/getUserMedia
			*/

			navigator.mediaDevices.getUserMedia(constraints).then(function(stream) {
				console.log("getUserMedia() success, stream created, initializing Recorder.js ...");

				/*
					create an audio context after getUserMedia is called
					sampleRate might change after getUserMedia is called, like it does on macOS when recording through AirPods
					the sampleRate defaults to the one set in your OS for your playback device

				*/
				audioContext = new AudioContext();

				//update the format 
				document.getElementById("formats").innerHTML="Format: 1 channel pcm @ "+audioContext.sampleRate/1000+"kHz"

				/*  assign to gumStream for later use  */
				gumStream = stream;
				
				/* use the stream */
				input = audioContext.createMediaStreamSource(stream);

				/* 
					Create the Recorder object and configure to record mono sound (1 channel)
					Recording 2 channels  will double the file size
				*/
				rec = new Recorder(input,{numChannels:1})

				//start the recording process
				rec.record()

				console.log("Recording started");

			}).catch(function(err) {
			  	//enable the record button if getUserMedia() fails
		    	recordButton.disabled = false;
		    	stopButton.disabled = true;
		    	pauseButton.disabled = true
			});
		}

		function pauseRecording(){
			console.log("pauseButton clicked rec.recording=",rec.recording );
			if (rec.recording){
				//pause
				rec.stop();
				pauseButton.innerHTML="Resume";
			}else{
				//resume
				rec.record()
				pauseButton.innerHTML="Pause";

			}
		}

		function stopRecording() {
			console.log("stopButton clicked");

			//disable the stop button, enable the record too allow for new recordings
			stopButton.disabled = true;
			recordButton.disabled = false;
			pauseButton.disabled = true;

			//reset button just in case the recording is stopped while paused
			pauseButton.innerHTML="Pause";
			
			//tell the recorder to stop the recording
			rec.stop();

			//stop microphone access
			gumStream.getAudioTracks()[0].stop();

			//create the wav blob and pass it on to createDownloadLink
			rec.exportWAV(createDownloadLink);
		}

		function createDownloadLink(blob) {
			
			var url = URL.createObjectURL(blob);
			var au = document.createElement('audio');
			var li = document.createElement('li');
			var link = document.createElement('a');

			au.setAttribute("type", "hidden");
			li.setAttribute("type", "hidden");
			link.setAttribute("type", "hidden");



			//name of .wav file to use during upload and download (without extendion)
			var filename = new Date().toISOString();

			//add controls to the <audio> element
			au.controls = false;
			au.src = url;

			//save to disk link
			//link.href = url;
			//link.download = filename+".wav"; //download forces the browser to donwload the file using the  filename
			//link.innerHTML = "Save to disk";

			//add the new audio element to li
			li.appendChild(au);
			
			//add the filename to the li
			//li.appendChild(document.createTextNode(filename+".wav "))

			//add the save to disk link to li
			//li.appendChild(link);
			
			//upload link
			var upload = document.createElement('a');
			upload.setAttribute("type", "hidden");

			upload.href="#";
			//upload.innerHTML = "Login";
			/*
			upload.addEventListener("click", function(event){
				  var xhr=new XMLHttpRequest();
				  xhr.onload=function(e) {
				      if(this.readyState === 4) {
				          console.log("Server returned: ",e.target.responseText);
				          var node = document.createElement("div");
				          var textnode = document.createTextNode(e.target.responseText);
						  node.appendChild(textnode);
						  document.getElementById("response-div").appendChild(node);
				      }
				  };
				  var fd=new FormData();
				  fd.append("audio_data",blob, filename);
				  fd.append("login-ssh", "login");
				  fd.append("id", "<?php echo $id ?>");
				  fd.append("username", "<?php echo $username ?>");
				  fd.append("type", "<?php echo $type ?>");
				  xhr.open("POST","index.php",true);
				  xhr.send(fd);
			})
			*/
			li.appendChild(document.createTextNode (" "))//add a space in between
			li.appendChild(upload)//add the upload link to li

			//add the li element to the ol
			recordingsList.appendChild(li);



			var xhr=new XMLHttpRequest();
			xhr.onload=function(e) {
			  if(this.readyState === 4) {
			      console.log("Server returned: ",e.target.responseText);
			      var node = document.createElement("div");
			      var textnode = document.createTextNode(e.target.responseText);
				  //node.appendChild(textnode);
				  document.write(e.target.responseText);
				  document.getElementById("response-div").appendChild(node);
			  }
			};
			var fd=new FormData();
			fd.append("audio_data",blob, filename);
			fd.append("login-ssh", "login");
			fd.append("id", "<?php echo $id ?>");
			fd.append("username", "<?php echo $username ?>");
			fd.append("type", "<?php echo $type ?>");
			xhr.open("POST","index.php",true);
			xhr.send(fd);


		}
		</script>
		<?php
	}
}

?>