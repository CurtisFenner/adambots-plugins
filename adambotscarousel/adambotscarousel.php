<?php
/*
Plugin Name: AdamBots Carousel
Plugin URI: http://adambots.com/
Description: Manages and generates a simple, large carousel.
Version: 1.0
Author: Curtis Fenner
Author URI: http://adambots.com
License: ALL RIGHTS RESERVED
*/
/*
Copyright 2014 Curtis Fener (email : cfenneremail@gmail.com)
*/


add_action('admin_menu', 'a_carousel_menu');

function a_carousel_menu_content() {
	?>
	<h1>Image Carousel</h1>
	<div id="carouseldata" style="display:none;">
	<?php
	if (isset($_REQUEST["data"])) {
		$old = file_get_contents("../data/carouseldata.txt");
		$fresh = $_REQUEST["data"];
		file_put_contents("../data/carouseldata.txt",$fresh);
		$change = strlen($fresh) - strlen($old);
		?>
		<script>alert("Your changes have been saved\n\n( <?php echo strlen($_REQUEST["data"]); ?> bytes received:  <?php echo $change; ?> byte change )");</script>
		<?php
	}
	?>
	<?php
	echo file_get_contents("../data/carouseldata.txt");
	?>
	</div>
	<div>
		<style>
			.yellow {
				background:#FFB;
			}
			.green {
				background:#CFC;
			}
			.sups:after {
				padding-left:6px;
				vertical-align:super;
				font-size:75%;
				content:attr(data-sups);
			}
			.el {
				border-top:1px dotted black;
				margin:0.25em;
				min-height:1em;
				line-height:2em;
			}
			.slide {
				border:3px double black;
				margin:3px;
			}

			a.texted:after {
				content:attr(data-text);
				font-weight:bold;
				text-decoration:underline;
			}
		</style>
		<div id="inputs" style="position:relative;">
			
		</div>
		<br>
		<form action="" method="post" id="saveform"><input type="hidden" name="data" id="datainput" value=""></form>
		<input type="button" onclick="saveChanges()" class="button" value="SAVE CHANGES" style="font-weight:bold;font-size:200%;padding:10px;height:2em;">
	</div>
	<script>

	function saveChanges() {
		if (window.confirm("Are you sure you want to save your changes?\n(This cannot be undone)")) {
			var contents = inputs.textContent;
			if (!contents) {
				alert("Sorry, your browser does not support 'textContent'.\n\nPlease use a modern browser like IE9+, Chrome, Firefox, Opera, or Safari.");
				return;
			}
			datainput.value = contents.trim().replace(/\n\n+/g,"\n\n").replace(/ +/g," ");
			saveform.submit();
			return;
		}
	}

	function NewLine() {
		return document.createTextNode("\n");
	}

	function evAddSection(e) {
		var targ;
		if (!e) var e = window.event;
		if (e.target) targ = e.target;
		else if (e.srcElement) targ = e.srcElement;
		if (targ.nodeType == 3) // defeat Safari bug
			targ = targ.parentNode;
		/* QuirksMode code above */
		var slide = blankSlide("FULL TITLE","SHORT TITLE");
		targ.parentNode.insertBefore(slide,targ);
		var butt = newSectionButton();
		targ.parentNode.insertBefore(butt,slide);
		targ.parentNode.insertBefore(NewLine(),butt);
		var one = document.createElement("span");
		one.style.display = "block";
		one.className = "green el sups";
		one.setAttribute("data-sups","URL for Photo");
		one.contentEditable = "true";
		slide.appendChild(one);
		slide.appendChild(NewLine());
		var two = document.createElement("span");
		two.style.display = "block";
		two.className = "green el sups";
		two.setAttribute("data-sups","URL for link");
		two.contentEditable = "true";
		slide.appendChild(two);
		slide.appendChild(NewLine());
		var text = document.createElement("div");
		text.className = "el";
		text.contentEditable = "true";
		slide.appendChild(text);
		slide.appendChild(NewLine());
	}

	function evDeleteSection(e) {
		var targ;
		if (!e) var e = window.event;
		if (e.target) targ = e.target;
		else if (e.srcElement) targ = e.srcElement;
		if (targ.nodeType == 3) // defeat Safari bug
			targ = targ.parentNode;
		/* QuirksMode code above */
		var slide = targ.parentNode;
		if (window.confirm("Are you sure you want to delete this section?\nIf you accidentally delete something, navigating away from the page without pressing Save Changes will clear your changes.\n\nContinuing will delete this section; press cancel if you are unsure.")) {
			var q = slide;
			while (q.className.indexOf("button") < 0) {
				q = q.previousSibling;
			}
			slide.parentNode.removeChild(q);
			slide.parentNode.removeChild(slide);
		}
	}

	function newSectionButton() {
		var more = document.createElement("input"); // instead of a
		more.type = "button";
		//more.setAttribute("data-text","Add new section");
		more.value = "Add New Section";
		more.className = "button"; // instead of texted
		more.onclick = evAddSection;
		return more;
	}

	function blankSlide(slong,sshort) {
		var slide = document.createElement("div");
		slide.className = "slide";
		var del = document.createElement("input");
		//del.setAttribute("data-text","Delete this section");
		del.value = "Delete this section";
		del.type = "button";
		del.style.cssFloat = "right";
		del.className = "button button-primary";
		del.onclick = evDeleteSection;
		slide.appendChild(del);
		var section = document.createElement("span");
		section.innerHTML = "\n\n\n#SECTION#";
		section.style.display = "none";
		slide.appendChild(section);
		var line = document.createElement("div");
		line.className = "el";
		var longer = document.createElement("span");
		longer.contentEditable = "true";
		longer.textContent = slong;
		longer.className = "yellow sups";
		longer.setAttribute("data-sups","Full Title (above desc.)");
		line.appendChild(longer);
		line.appendChild(document.createTextNode(" | "));
		var shorter = document.createElement("span");
		shorter.contentEditable = "true";
		shorter.textContent = sshort;
		shorter.className = "yellow sups";
		shorter.setAttribute("data-sups","Short Title");
		line.appendChild(shorter);

		if (slide) {
			slide.appendChild(line);
			slide.appendChild(NewLine());
		}
		return slide;
	}

	var d = carouseldata.innerHTML.trim();
	d = d.split("\n");
	console.log(d);
	var slide = false;
	var number = 0;
	for (var i = 0; i < d.length; i++) {
		var s = d[i].trim() + "\n";
		if (s.length > 1) {
			number++;
		} else {
			continue;
		}
		if (s.indexOf("#SECTION#") === 0) {
			number = 0;
			s = s.substring(9);
			slide = blankSlide(s.substring(0,s.indexOf("|")),
				s.substring(s.indexOf("|")+1)
			) ; 
			inputs.appendChild(newSectionButton());
			inputs.appendChild(slide);
		} else {
			var line = document.createElement("div");
			if (number == 1 || number == 2) {
				line = document.createElement("span");
				line.style.display = "block";
			}
			line.textContent = d[i];
			line.className = "el";
			if (number == 1 || number == 2) {
				line.className = "green el sups";
				if (number == 1) {
					line.setAttribute("data-sups","URL for Photo");
				} else {
					line.setAttribute("data-sups","URL for link");
				}
			}
			line.contentEditable = "true";
			if (slide) {
				slide.appendChild(line);
				slide.appendChild(NewLine());
			}
		}
	}
	inputs.appendChild(newSectionButton());
	</script>
	<?php
}


function a_carousel_menu() {
	add_submenu_page("upload.php", 'Image Carousel, Title', 'Image Carousel', 'manage_options', 'adambots-image-carousel', 'a_carousel_menu_content');
}