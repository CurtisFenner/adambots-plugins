<?php
/*
Plugin Name: AdamBots Dropdown Manager
Plugin URI: http://adambots.com/
Description: Manages and generates a simple dropdown
Version: 1.0
Author: Curtis Fenner
Author URI: http://adambots.com
License: ALL RIGHTS RESERVED
*/
/*
Copyright 2014 Curtis Fener (email : cfenneremail@gmail.com)
*/

add_action('admin_menu', 'catcontrol');

function catcontrol() {
	add_submenu_page("upload.php", 'Dropdown Manager, Title', 'Manage Dropdown', 'delete_posts', 'adambots-dropdown-manage', 'ab_dropdown_page');
}

if (isset($_REQUEST["bigtext"])) {
	?>
<script>
alert("Received <?php echo strlen($_REQUEST["bigtext"]); file_put_contents("../data/dropsource.txt",$_REQUEST["bigtext"]); ?> characters.\nYour work was saved.");
</script>
	<?php
}

function ab_dropdown_page() {
	?>
	<style scoped>
	.tab,.del,.ins {
		display:inline-block;
		color:white !important;
		margin:5px;
		padding:5px;
		text-decoration:none;
		cursor:pointer;
	}
	.del, .ins {
		display:inline-block;
		min-width:18px;
		height:18px;
		text-align:center;
		border-radius:3px;
		font-weight:bold;
	}
	.tab {
		background:#333;
		margin-right:0;
		border-radius:3px;
	}
	.tab:hover {
		background:black;
	}
	.del {
		background:#FF5577;
		margin-left:0;
	}
	.del:hover {
		background:#FF0000;
	}
	.ins {
		background:#55DD77;
	}
	.ins:hover {
		background:#00DD00;
	}
	.tabline {
		width:400px;
	}
	.push {
		margin-left:50px;
	}
	</style>
	<h1>Dropdown Editor</h1>
	Click tab (<b>black</b>) to edit. Click X (red) to delete tab. Click + (green) to add new tab.
	<div id="dropdowndata" style="display:none;"><?php
	echo file_get_contents("../data/dropsource.txt");
	?>
	</div>
	<div id="tablist">
	</div>
	<div id="edittab">
	</div>
	<form action="" method="POST" id="dropdownform">
	<input type="hidden" name="bigtext" id="bigtext" value="">
	</form>
	<input type="button" id="bigsave" value="SAVE CHANGES TO WEBSITE">
	<script>
	"use strict";


	bigsave.onclick = function() {
		if (currentTab >= 0) {
			alert("Please save your changes to the open tab first :)");
			return;
		}
		var s = "";
		for (var i = 0; i < tabs.length; i++) {
			s += tabs[i].join("\n") + "\n";
		}
		bigtext.value = s;
		dropdownform.submit();
	}


	if (!Array.prototype.indexOf) {
		Array.prototype.indexOf = function (searchElement, fromIndex) {
			if ( this === undefined || this === null ) {
				throw new TypeError( '"this" is null or not defined' );
			}

			var length = this.length >>> 0; // Hack to convert object.length to a UInt32

			fromIndex = +fromIndex || 0;

			if (Math.abs(fromIndex) === Infinity) {
				fromIndex = 0;
			}

			if (fromIndex < 0) {
				fromIndex += length;
				if (fromIndex < 0) {
					fromIndex = 0;
				}
			}

			for (;fromIndex < length; fromIndex++) {
				if (this[fromIndex] === searchElement) {
					return fromIndex;
				}
			}

			return -1;
		};
	}






	function file_get_contents() {
		return dropdowndata.innerHTML;
	}

	var tabs;
	var currentTab = -1;

	function updateTab() {
		if (!tabs[currentTab]) {
			return;
		}

		var rtab = [ "Tab:" + document.getElementById("tabName").value + "|" + document.getElementById("tabLink").value ];
		var els = document.getElementsByClassName("tabline");
		for (var i = 0; i < els.length; i++) {
			var v = els[i].value;
			var sort = els[i].getAttribute("data-sort");
			if (sort === "section") {
				rtab.push("Title:" + v);
			}
			if (sort === "linktitle") {
				rtab.push(v + "|" + els[i+1].value + "|" + els[i+2].value);
				//Skip the next two!
				i+=2;
			}
		}
		//tabs[currentTab][0] = "Tab:" + document.getElementById("tabName").value + "|" + document.getElementById("tabLink").value;
		tabs[currentTab] = rtab;
		render();
		clickTab(-1);
	}

	function deleteTab(i) {
		if (!window.confirm("Are you sure you mean to delete the tab '" + tabs[i][0] + "'?")) {
			return;
		}
		tabs.splice(i,1);
		render();
		clickTab(-1);
	}

	function insertTab(i) {
		tabs.splice(i,0,["Tab:New Tab|"]);
		render();
		clickTab(i);
	}

	function insertLink(at) {
		var now = currentTab;
		updateTab();
		tabs[now].splice(at,0,"New Link|DEFAULT URL CHANGE ME|Default Description");
		clickTab(now);
	}

	function insertSection(i) {
		var now = currentTab;
		updateTab();
		//i refers to the index in the tab rather than by sections.
		tabs[now].splice(i,0,"Title:New Section");
		clickTab(now);
	}

	function deleteSection(at) {
		var tab = tabs[currentTab];
		var until = at+1;
		for (var i = at+1; i < tab.length; i++) {
			if (tab[i].substring(0,6) === "Title:") {
				until = i;
				break;
			}
		}
		if (window.confirm("Are you sure you want to delete section '" + tab[at] + "' and its " + (until - at-1) + " links?")) {
			var now = currentTab;
			updateTab();
			tabs[now].splice(at,until - at);
			clickTab(now);
		}
	}

	function deleteLink(at) {
		if (window.confirm("Are you sure you want to delete link '" + tabs[currentTab][at] + "'?")) {
			var now = currentTab;
			updateTab();
			tabs[now].splice(at,1);
			clickTab(now);
		}
	}


	function clickTab(i) {
		currentTab = i;
		if (i < 0) {
			edittab.innerHTML = "";
			return;
		}
		var tab = tabs[i];
		var title = tab[0].split("Tab:")[1];
		//Tab title
		//Tab default link
		var s = "";
		s += "Tab Name:<input id='tabName' value='" + title.split("|")[0] + "'><br>";
		s += "Tab Link (Only used without JavaScript)<input id='tabLink' value='" + title.split("|")[1] + "'><br><div>";
		for (var i = 1; i < tab.length; i++) {
			var line = tab[i];
			if (line.substring(0,6) == "Title:") {
				s += "</div><hr>";
				s += "<a class=ins onclick='insertSection(" + i + ");'>+Section</a><hr>";
				s += "<b>Section Title:</b> <input style=font-weight:bold; class='tabline' data-sort=section value='" + line.substring(6) + "'>";
				s += "<a class=del onclick='deleteSection(" + i + ");'>X</a>";
				s += "<hr><div class=push>";

				s += "<a class=ins onclick='insertLink(" + (i+1) + ");'>+Link</a><hr>";
			} else {
				var data = line.split("|");
				if (data.length == 2) {
					data = [data[0],data[0].split(/\s+/)[data[0].split(/\s+/).length-1],data[1]];
				}
				s += "Link Title: <input class='tabline' data-sort=linktitle value=\"" + data[0] + "\"><a class=del onclick='deleteLink(" + i + ");'>X</a><br>";
				s += "Link URL: <input class='tabline' data-sort=linklink value='" + data[1] + "'><br>";
				s += "Link Description: <input class='tabline' data-sort=linkdesc value=\"" + data[2] + "\"><hr>";
				s += "<a class=ins onclick='insertLink(" + (i+1) + ");'>+Link</a><hr>";
			}
		}
		s += "</div><hr><a class=ins onclick='insertSection(" + tab.length + ")'>+Section</a><hr><input type=button onclick=updateTab() value='Update Changes to Tab'>";
		edittab.innerHTML = s;
	}

	function load() {
		var lines = file_get_contents().split("\n");
		tabs = [];
		var tab = null;
		for (var i = 0; i < lines.length; i++) {
			var line = lines[i];
			if (line.substring(0,4) == "Tab:") {
				if (tab != null) {
					tabs.push(tab);
				}
				tab = [];
			}
			if (tab && line.trim()) {
				tab.push(line);
			}
		}
		if (tab) {
			tabs.push(tab);
		}
		//Tabs is now a list of tabs.
	}

	function render() {
		var s = "";
		for (var i = 0; i < tabs.length; i++) {
			var title = tabs[i][0];
			s += "<a ' class='ins' onclick='insertTab(" + i + ")'>+</a>";
			s += "<a  class=\"tab\" onclick=\"clickTab(" + i + ");\">" + title.split("Tab:")[1].split("|")[0] + "</a><a class='del' onclick='deleteTab(" + i + ")';>X</a>";
		}

		s += "<a class='ins' onclick='insertTab(" + tabs.length + ")'>+</a>";
		tablist.innerHTML = s;
	}

	load();
	render();



	</script>


<?php 
} //end of function ab_dropdown_page()
?>