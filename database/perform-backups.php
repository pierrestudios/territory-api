<?php 

	$envConfig = [];
	
	function getEnvConf($key='') {	
		global $envConfig;
		if($key && count($envConfig) > 0) {
			return $envConfig[$key];	
		}
		
		$handle = @fopen("/var/www/html/APPS/territory-api/.env", "r");
		if ($handle) {
		    while (($buffer = fgets($handle, 4096)) !== false) {
			    getEnvKey($buffer);
		    }
		    if (!feof($handle)) {
		        echo "Error: unexpected fgets() fail\n";
		    }
			fclose($handle);
	    }
	    	    
	    if($key && count($envConfig) > 0) {
			return $envConfig[$key];	
		}
	} 
	
	function getEnvValue($string='', $key='') {
		$string_=explode("=", $string);
		if($key && $string_[1]) 
			return $string_[1];
	}
	
	function getEnvKey($lineStr='') {
		global $envConfig;
		$lineStr = str_replace("\n", "", $lineStr);
		$string_=explode("=", $lineStr);
		if(count($string_) > 1 && $string_[0] && $string_[1])
			$envConfig[$string_[0]] = $string_[1];
	}

	function performDBBackup() {
		$path='/var/www/html/APPS/territory-api/database/backups/';
		$filename='db-backup-'.date('m-d-Y', time()).'.sql';
	   	$output = array();
		$return_var = -1;
		$dbn = getEnvConf('DB_DATABASE');
		$dbu = getEnvConf('DB_USERNAME');
		$dbp = getEnvConf('DB_PASSWORD');
		$command='mysqldump --user='.$dbu.' --password='.$dbp.' --host=localhost '.$dbn.' > '.$path.$filename.' --skip-add-drop-table ';
		$last_line = exec($command, $output, $return_var);
		// var_dump($command);
		
		if ($return_var === 0) {
			// success
			echo 'success: ';
			mail_attachment($filename, $path, $mailto='webdevsolutionsstudio@gmail.com', $from_mail='mailer@webdevstudio.net', $from_name='Territory Api', $replyto='mailer@webdevstudio.net', $subject='Territory Api Database Backup', $message='Database backup for '. date('m-d-Y', time()));
		} else {
			// fail or other exceptions
			echo 'failed: ';
			var_dump($output);
		}
	}
	
	function mail_attachment($filename, $path, $mailto, $from_mail, $from_name, $replyto, $subject, $message) {
		 $file = $path.$filename;
		 $file_size = filesize($file);
		 $handle = fopen($file, "r");
		 $content = fread($handle, $file_size);
		 fclose($handle);
		 $content = chunk_split(base64_encode($content));
		 $uid = md5(uniqid(time()));
		 $header = "From: ".$from_name." <".$from_mail.">\r\n";
		 $header .= "Reply-To: ".$replyto."\r\n";
		 $header .= "MIME-Version: 1.0\r\n";
		 $header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";
		 $header .= "This is a multi-part message in MIME format.\r\n";
		 $header .= "--".$uid."\r\n";
		 $header .= "Content-type:text/plain; charset=iso-8859-1\r\n";
		 $header .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
		 $header .= $message."\r\n\r\n";
		 $header .= "--".$uid."\r\n";
		 $header .= "Content-Type: application/octet-stream; name=\"".$filename."\"\r\n"; // use different content types here
		 $header .= "Content-Transfer-Encoding: base64\r\n";
		 $header .= "Content-Disposition: attachment; filename=\"".$filename."\"\r\n\r\n";
		 $header .= $content."\r\n\r\n";
		 $header .= "--".$uid."--";
	 	if (mail($mailto, $subject, "", $header)) {
	 		echo "mail send ... OK";  
	 	} else {
	 		echo "mail send ... ERROR!";
		}
	}
	
	performDBBackup();