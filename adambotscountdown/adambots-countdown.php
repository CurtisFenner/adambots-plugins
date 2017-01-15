<?php
/*
Plugin Name: AdamBots Count Down Manager
Plugin URI: http://adambots.com/
Description: Runs the countdown clock on the AdamBots home page.
Version: 1.0
Author: Nathan Fenner
Author URI: http://adambots.com
License: ALL RIGHTS RESERVED
*/
/*
Copyright 2014 Nathan Fener (email : nfenneremail@gmail.com)
*/

add_action('admin_menu', 'countdown_control');

function countdown_get_contents() {
	$q = file_get_contents("../data/countdown.txt");
	if ($q == "") {
		return file_get_contents("data/countdown.txt");
	}
	return $q;
}


function countdown_control() {
	add_submenu_page("upload.php", 'Countdown Clock Manager', 'Manage Countdown', 'delete_posts', 'adambots-countdown-manage', 'countdown_page');
}

if (isset($_REQUEST["countdowninput"])) {
	file_put_contents("../data/countdown.txt",$_REQUEST["countdowninput"]);
}


/*
if (isset($_REQUEST["bigtext"])) {
	?>
<script>
alert("Received <?php echo strlen($_REQUEST["bigtext"]); file_put_contents("../data/dropsource.txt",$_REQUEST["bigtext"]); ?> characters.\nYour work was saved.");
</script>
	<?php
}*/

function countdown_page() {
/*
event is an epoch ("until time") and message



*/


?>

<h2>Countdown Manager</h2>
<script id="countdown_current_source" type="text/countdown"><?php
	echo countdown_get_contents();
?></script>

<form id="countdownform" action="" method="POST">
<input type="hidden" name="countdowninput" id="countdowninput" value=""/>
</form>

<hr/>

<div id="countdowndisplay">
</div>

<hr/>


Countdown Message Until: <input id="countdown_selector" type="datetime-local"/><br/>
Countdown Message:<br/><textarea style="width:100%;" id="countdown_message_selector"></textarea><br/>
<button id="countdown_add">Add Event</button>
<hr/>
In the message field:<br/>
Use ### to represent the countdown time. For example:<br/>
<code>The event starts in ###</code><br/>
Or use the following shortcodes for auto-formatting fun:<br/>
<code>[ongoing FRC Championship]</code><br/>
<code>[ongoing Relay for Life]</code><br/>
<code>[ongoing whatever text you want to appear here]</code><br/>
Or<br/><code>[countto FRC Championship]</code><br/>
<code>[countto Relay for Life]</code><br/>
<code>[countto whatever text you want to appear here]</code>

<hr/>
<button id="countdown_save">Save Countdown</button>
<br/>
<div style="font-size:7px;color:#D0D0D0">Made by Nathan Fenner. For information, contain nfenn<span>erema</span>i<span>l@gm</span>ail.com</div>

<?php
if (isset($_REQUEST["countdowninput"])) {
	?><span style="color:#1A3"> Countdown saved!</span><?php
}
?>

<script>

function dateEpoch(epoch) {
	return new Date(epoch);
}
function niceTime(date) {
	if (typeof date == "number") {
		date = dateEpoch(date);
	}
	return date.toLocaleTimeString() + " " + date.toLocaleDateString();
}
countdowninput.value = countdown_current_source.innerHTML;
var countdown_data = countdown_current_source.innerHTML.split("\n");
var countdown_parsed = [];
for (var countdown_i = 0; countdown_i < countdown_data.length; countdown_i++) {
	var countdown_line = countdown_data[countdown_i];
	if (countdown_line != "") {
		var countdown_epoch = 1 * countdown_line.replace(/^(\d+).*/,"$1");
		var countdown_message = countdown_line.replace(/^\d+ (.*)/,"$1");
		countdown_parsed.push({ epoch: countdown_epoch, message: countdown_message});
	}
}

function countdown_compare(a,b) {
	return a.epoch  - b.epoch;
}

function countdown_serialize() {
	var s = "";
	for (var i = 0; i < countdown_parsed.length; i++) {
		var e = countdown_parsed[i];
		s += e.epoch + " " + e.message.replace(/\n/g," ");
		s += "\n";
	}
	return s;
}

var countdown_list_els = [];

function countdown_list_render() {
	while (countdowndisplay.firstChild != null) {
		countdowndisplay.removeChild(countdowndisplay.firstChild);
	}
	countdown_list_els = [];
	for (var i = 0; i < countdown_parsed.length; i++) {
		var ev = countdown_parsed[i];
		var div = document.createElement("div");
		div.style.background = "#FAFAFA";
		div.style.marginBottom = "10px";
		div.style.padding = "10px";
		var del = document.createElement("button");
		var sureCheck = document.createElement("span");
		sureCheck.innerHTML = "Are you sure?";
		var sureYes = document.createElement("button");
		sureYes.innerHTML = "Yes";
		var sureNo = document.createElement("button");
		sureNo.innerHTML = "No";
		sureNo.dataset.num = i;
		del.dataset.num = i;
		del.style.background = "#F00";
		del.style.border = "#A00 solid";
		del.style.borderRadius = "10px"
		del.style.color = "#FFF";

		sureYes.style.background = "#F00";
		sureYes.style.border = "#A00 solid";
		sureYes.style.borderRadius = "10px"
		sureYes.style.color = "#FFF";
		sureYes.style.display = "none";

		sureNo.style.background = "#F00";
		sureNo.style.border = "#A00 solid";
		sureNo.style.borderRadius = "10px"
		sureNo.style.color = "#FFF";
		sureNo.style.display = "none";

		sureCheck.style.display = "none";


		del.innerHTML = "Delete Event";
		sureYes.dataset.num = i;
		countdown_list_els[i] = [sureCheck,sureYes,sureNo];
		del.onclick = deleteShow;
		sureNo.onclick = deleteHide;
		sureYes.onclick = deleteEvent;
		div.appendChild(del);
		div.appendChild(sureCheck);
		div.appendChild(sureYes);
		div.appendChild(sureNo);
		var time = document.createElement("h3");
		time.innerHTML = niceTime(ev.epoch);
		div.appendChild(time);
		countdowndisplay.appendChild(div);
		var msg = document.createElement("div");
		msg.innerHTML = ev.message.replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/"/g,"&quot;");
		div.appendChild(msg);
	}
}

function deleteShow(e) {
	var num = 1 * e.target.dataset.num;
	var q = countdown_list_els[num];
	for (var i = 0; i < q.length; i++) {
		q[i].style.display = "";
	}
}
function deleteHide(e) {
	var num = 1 * e.target.dataset.num;
	var q = countdown_list_els[num];
	for (var i = 0; i < q.length; i++) {
		q[i].style.display = "none";
	}
}


var rel;
function deleteEvent(e) {
	var num = 1 * e.target.dataset.num;
	countdown_parsed.splice(num, 1);
	countdowninput.value = countdown_serialize( countdown_parsed );
	countdown_list_render();
}

function countdown_addEvent(ev) {
	if (!isFinite(ev.epoch)) {
		return;
	}
	countdown_parsed.push(ev);
	countdown_parsed.sort( countdown_compare );
	countdowninput.value = countdown_serialize( countdown_parsed );
	countdown_list_render();
}


countdown_add.onclick = function() {
	var msg = countdown_message_selector.value;
	var time = new Date(countdown_selector.value).getTime();
	countdown_addEvent({epoch: time, message: msg});
}

countdown_save.onclick = function() {
	countdownform.submit();
}

countdown_list_render();

</script>

<?php 
} //end of function countdown_page()



function countdown_setup($data_name,$div_name) {
	?><script id="<?php echo $data_name;?>" type="text/countdown"><?php echo countdown_get_contents(); ?></script>
	<script>

/*[ongoing event]
The <span style='color:#ffd800;'>$1</span> is going on!

[countto event]
Countdown to the <span style='color:#ffd800;font-size:100%;'>$1</span>:<br class=mobileonly>###

examples:

[countto Rochester ACS <em>Relay for Life&nbsp;</em>]
[ongoing Rochester ACS <em>Relay for Life&nbsp;</em>]*/

		(function() { // scopes
			var data = <?php echo $data_name;?>.innerHTML.split("\n");
			var r = [];
			for (var i = 0; i < data.length; i++) {
				if (data[i] != "") {
					var epoch = 1*data[i].replace(/^(\d+).*/,"$1");
					var message = data[i].replace(/^\d+ (.*)/,"$1");
					message = message.replace(/\[ongoing ([^\]]*)\]/,"The <span style='color:#ffd800;'>$1</span> is going on!");
					message = message.replace(/\[countto ([^\]]*)\]/,"Countdown to the <span style='color:#ffd800;font-size:100%;'>$1</span>:<br class=mobileonly>###");
					r.push({epoch:epoch,message:message});
				}
			}

			function pluralUnit(num,tex) {
				return "<span style='display:inline-block;'><span style='font-weight:bold;color:#ffd800;padding-left:10px;width:30px;display:inline-block;text-align:center;font-size:100%;'>" + num + "</span> " + tex + (num != 1 ? "s" : "") + " " + "</span>";
			}

			function loop() {
				var now = Date.now();
				var i = 0;
				while (i < r.length && r[i].epoch < now) {
					i++;
				}
				var s = "";
				if (i < r.length) {
					// message
					var difference = r[i].epoch - now;
					var message = r[i].message;
					// next, add in the fancy time thing
					var rem = difference / 1000; //rem is in seconds
					var minSec = 60;
					var hrSec = 60*minSec;
					var daySec = 24*hrSec;
					var weekSec = 7*daySec;
					var weeks = Math.floor(rem / weekSec);
					rem = rem % weekSec;
					var days = Math.floor(rem / daySec);
					rem = rem % daySec;
					var hours = Math.floor(rem / hrSec);
					rem = rem % hrSec;
					var minutes = Math.floor(rem / minSec);
					rem = rem % minSec;
					var seconds = Math.floor(rem);
					var timeString = (weeks != 0 ? pluralUnit(weeks,"week") : "") + (days != 0 ? pluralUnit(days,"day") : "")+ pluralUnit(hours,"hour") + pluralUnit(minutes,"minute") + pluralUnit(seconds,"second");
					s = message.replace("###",timeString);
				}
				<?php echo $div_name;?>.innerHTML = s;
			}
			loop();
			setInterval(loop,1000); // render every second
		})();
	</script>
	<?php
}



?>