<?php
/*				
//	(C) 2021 DalekIRC Services
\\				
//			pathweb.org
\\				
//	GNU GENERAL PUBLIC LICENSE
\\							v3
//				
\\				
//				
\\	Title:		NickServ
//				
\\	Desc:		Provides the bare essentials for
//				pseudoclient NickServ, the
\\				Nickname Registration Service.
//				
\\				
//				
\\	Version:	1
//				
\\	Author:		Valware
//				
*/

// NickServ configuration
include "class.php";
include "nickserv.conf";
include "modules.conf";


// Spawn nickserv on server connect
hook::func("connect", function($u){
		global $nickserv,$ns;
		
		// spawn client with $ns
		$ns = new Client($nickserv['nick'],$nickserv['ident'],$nickserv['hostmask'],$nickserv['uid'],$nickserv['gecos']);
		
});


hook::func("privmsg", function($u){
	
	global $ns,$nickserv;
	if (strpos($u['dest'],"@") !== false){
		$n = explode("@",$u['dest']);
		$dest = $n[0];
	}
	else { $dest = $u['dest']; }
	
	
	if (strtolower($dest) == strtolower($ns->nick) || $dest == $nickserv['uid']){ 
		nickserv::run("privmsg", array(
			"msg" => $u['parv'],
			"nick" => $u['nick'])
		);
			
	}
	
});
hook::func("preconnect", function($u){
	
	global $sql;
	
	$query = "CREATE TABLE IF NOT EXISTS dalek_accounts (
		id int NOT NULL AUTO_INCREMENT,
		timestamp varchar(12) NOT NULL,
		display varchar(255) NOT NULL,
		email varchar(255) NOT NULL,
		pass varchar(255) NOT NULL,
		PRIMARY KEY (id)
	)";
	$sql::query($query);
	
	$query = "CREATE TABLE IF NOT EXISTS dalek_account_settings (
		id int NOT NULL AUTO_INCREMENT,
		account varchar(255) NOT NULL,
		setting_key varchar(255) NOT NULL,
		setting_value varchar(255) NOT NULL,
		PRIMARY KEY (id)
	)";
	$sql::query($query);	
});

	
	