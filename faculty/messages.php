<?php
if(isset($_GET['seccreated'])){
	echo '<p class="message">Message: Section created "'.$_GET['seccreated'].'"</p>"';
}
if(isset($_GET['secedited'])){
	echo '<p class="message">Message: Section edited "'.$_GET['secedited'].'"</p>"';
}
if(isset($_GET['secdeleted'])){
	echo '<p class="message">Message: Section deleted "'.$_GET['secdeleted'].'"</p>"';
}
if(isset($_GET['error']) && $_GET['error'] == "addstunosec"){
	echo '<p class="message">Student could not be added, please try again</p>';
}
if(isset($_GET['error']) && $_GET['error'] == "sqlerror"){
	echo '<p class="message">Operation could not be performed</p>';
}
if(isset($_GET['success']) && $_GET['success'] == "added"){
	echo '<p class="message">'.$_GET['existing'].$_GET['aid'].' '.$_GET['name'].' added to section</p>';
}
if(isset($_GET['success']) && $_GET['success'] == "removedstudents"){
	echo '<p class="message">Selected students were removed from section</p>';
}
if(isset($_GET['success']) && $_GET['success'] == "classcreated"){
	echo '<p class="message">Class created on '.$_GET['date'].'</p>';
}
if(isset($_GET['success']) && $_GET['success'] == "classedited"){
	echo '<p class="message">Class edited</p>';
}
if(isset($_GET['success']) && $_GET['success'] == "classdeleted"){
	echo '<p class="message">Class deleted</p>';
}
if(isset($_GET['success']) && $_GET['success'] == "profupdated"){
	echo '<p class="message">Profile successfully updated</p>';
}
if(isset($_GET['success']) && $_GET['success'] == "passchanged"){
	echo '<p class="message">Password successfully updated</p>';
}
if(isset($_GET['error']) && $_GET['error'] == "alreadyexists"){
	echo '<p class="message">'.$_GET['aid'].' '.$_GET['name'].' is already in this section</p>';
}