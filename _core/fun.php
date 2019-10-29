<?php
/**
* @author      John Graham <iampapagray@gmail.com> Enderr Studios
* @copyright   Copyright (C), 2018 John Graham
* @license     MIT LICENSE (https://opensource.org/licenses/MIT)
*              Refer to the LICENSE file distributed within the package.
*              
* @todo functions and error handling
* @category  functions
*
*/
require_once 'init.php';
$url = APP_URL;

//connect to database
function connectDB(){
  $con = new mysqli(HOST, USER, PASS, HOST_DB);
  // check connection
  if ($con->connect_error) {
    trigger_error('Database connection failed: '  . $con->connect_error, E_USER_ERROR);
  }else{
    return $con;
  }
}

// escaping html special characters
function h($s) {
    return htmlspecialchars($s, ENT_QUOTES, 'utf-8');
}

// returns true if $needle is a substring of $haystack
function contains($needle, $haystack){
    return strpos($haystack, $needle) !== false;
}

// returns all registered users
function getAllUsers(){
	$con =  connectDB();
	$query = "SELECT COUNT(`id`) FROM `userx`";
	$stmt = $con->prepare($query);
	if($stmt === false) {
		trigger_error('Wrong SQL: ' . $query . ' Error: ' . $con->error, E_USER_ERROR);
	}
	$stmt->execute();
	$stmt->bind_result($num);
	while ($stmt->fetch()) {
		return $num;
	}
}

// returns all registered users past
function getAllYesterday(){
	$con =  connectDB();
	$query = "SELECT COUNT(`id`) FROM `userx` WHERE DATE(`created_at`) <> DATE(NOW())";
	$stmt = $con->prepare($query);
	if($stmt === false) {
		trigger_error('Wrong SQL: ' . $query . ' Error: ' . $con->error, E_USER_ERROR);
	}
	$stmt->execute();
	$stmt->bind_result($num);
	while ($stmt->fetch()) {
		return $num;
	}
}

// returns all active users
function getActiveUsers(){
	$con =  connectDB();
	$query = "SELECT COUNT(`id`) FROM `userx` WHERE `active` = 1";
	$stmt = $con->prepare($query);
	if($stmt === false) {
		trigger_error('Wrong SQL: ' . $query . ' Error: ' . $con->error, E_USER_ERROR);
	}
	$stmt->execute();
	$stmt->bind_result($num);
	while ($stmt->fetch()) {
		return $num;
	}
}

// returns past active users
function getActiveYesterday(){
	$con =  connectDB();
	$query = "SELECT COUNT(`id`) FROM `userx` WHERE `active` = 1 AND DATE(`created_at`) <> DATE(NOW())";
	$stmt = $con->prepare($query);
	if($stmt === false) {
		trigger_error('Wrong SQL: ' . $query . ' Error: ' . $con->error, E_USER_ERROR);
	}
	$stmt->execute();
	$stmt->bind_result($num);
	while ($stmt->fetch()) {
		return $num;
	}
}

//returns change in percentage
function getChange($type){
	if($type == "all"){
		$old = getAllYesterday();
		$new = getAllUsers();
	}elseif ($type == "active") {
		$new = getActiveUsers();
		$old = getActiveYesterday();
	}/*elseif ($type == "payouts") {
		# code...
	}*/
	
	$change = ( ($new - $old) / $old ) * 100;
	if(($new - $old) == 0){
	  return '<span class="stats-small__percentage stats-small__percentage--increase">
	    0% </span>';
	}else{
	  if($change < 0){
	    return '<span class="stats-small__percentage stats-small__percentage--decrease">
	      '.$change.'% </span>';
	  }else{
	    return '<span class="stats-small__percentage stats-small__percentage--increase">
	      '.$change.'% </span>';
	  }
	}
}

// returns all downlines of user
function getTotalDownlines($username){
	$con =  connectDB();
	$query = "SELECT COUNT(`ref_id`) FROM `referrals` WHERE `ref_username` = ?";
	$stmt = $con->prepare($query);
	if($stmt === false) {
		trigger_error('Wrong SQL: ' . $query . ' Error: ' . $con->error, E_USER_ERROR);
	}
	$stmt->bind_param('s', $username);
	$stmt->execute();
	$stmt->bind_result($num);
	while ($stmt->fetch()) {
		return $num;
	}
}

// returns all referral requests not confirmed or rejected
function fetchRefRequests($username){
	$state = 0;
	$con =  connectDB();
	$query = "SELECT `username` FROM `referrals` WHERE `ref_username` = ? AND `active` = ? ";
	$stmt = $con->prepare($query);
	if($stmt === false) {
		trigger_error('Wrong SQL: ' . $query . ' Error: ' . $con->error, E_USER_ERROR);
	}
	$stmt->bind_param('si', $username, $state);
	$stmt->execute();
	$stmt->bind_result($newUser);
	while ($stmt->fetch()) {
		echo '
			<div class="blog-comments__item d-flex p-3 ">
			  <div class="blog-comments__content justify-content-center">
			    <div class="blog-comments__meta text-muted ">
			      <a class="text-secondary" href="#">'.getUserName($newUser).'</a> 
			      <span class="text-success"><i class="material-icons">trending_flat</i></span>
			      <a class="text-secondary" href="#">'.$newUser.'</a>
			    </div>
			    <div class="blog-comments__actions ">
			      <div class="btn-group btn-group-sm ">
			        <button type="button" class="btn btn-white" id="accept_'.$newUser.'">
			          <span class="text-success"><i class="material-icons">check</i>
			          </span> Approve </button>
			        <button type="button" class="btn btn-white" id="reject_'.$newUser.'">
			          <span class="text-danger"><i class="material-icons">clear</i>
			          </span> Reject </button>
			      </div>
			    </div>
			  </div>
			</div>
		';
	}
}