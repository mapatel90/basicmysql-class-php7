<?php
declare(strict_types=1);

// Include MySQL class
require_once('inc/basicsqli.class.php');
// Include database connection
require_once('inc/global.inc.php');

function showProfile() {
	
	global $DB;	

	if ($result = $DB->query("SELECT id, name, age, number FROM profile")){
		
		$size = $result->size();
		
		if (!defined('TABLE')) define ('TABLE',['id','name','age','number']);
		
		while ($sqlReg=$result->fetch()) {
		
				echo("<p>Candidate number ".$sqlReg[TABLE[0]]." named ".$sqlReg[TABLE[1]].
						" with ".$sqlReg[TABLE[2]]." years old with mobile ".$sqlReg[TABLE[3]].
						" may sit on the table and wait.</p>");
			
		}
		
	}
}


showProfile();

?>