<?php

define (awsAccessKey,"YOUR_ACCESS_KEY");
define (awsSecretKey,"YOUR_SECRET_KEY");
define (bucketName , "YOUR_BUCKET_NAME");




function rename_file($oldFileName,$newFileName) {
	$s3 = new S3(awsAccessKey,awsSecretKey);
	$result = false;
	 if (S3::copyObject(bucketName, $oldFileName, bucketName, $newFileName, S3::ACL_PRIVATE)) {
        
			if(S3::deleteObject("login_app_bucket",$oldFileName))
				$result = true;
			else
				$result = false;
		
    } else {
       $result = false;
    }
	
	return $result;
}

function replace_file($oldFile,$newFile) {
	$s3 = new S3(awsAccessKey,awsSecretKey);
	$result = false;
	 if (S3::copyObject(bucketName, $oldFile, bucketName, $newFile, S3::ACL_PRIVATE)) {
        
			
				if(S3::deleteObject(bucketName,$oldFile))
				$result = true;
			else
				$result = false;
			
		
    } else {
       $result = false;
    }
	
	return $result;

}

//get number of pages in the pdf file
function get_pdf_pages($fileName) {

		if(strlen($fileName)  < 1) return 0;

		$s3 = new S3(awsAccessKey,awsSecretKey);
		$url = $s3->getAuthenticatedUrl(bucketName,$fileName,3600);
	
		if(empty($url)) return 0;
				
		$pdftext = file_get_contents($url);
		$num = preg_match_all("/\/Page\W/", $pdftext, $dummy);
		
		return $num;
}

//convert size 
function convert_size($size) {
	
	$base = log($size) / log(1024);
	$suffix = array("", "k", "M", "G", "T");
	$suff = $suffix[floor($base)];
		
		return round(pow(1024, $base - floor($base)),2) . $suff;
		
}

function delete_whole_directory($directory) {

	if(strlen($directory) <1) return;
	
	$s3 = new S3(awsAccessKey,awsSecretKey);
	
	$bucket_contents = $s3->getBucket(bucketName);
	
	foreach($bucket_contents as $file) {
	
		$filename = $file['name'];
		
		$directory_part = substr($filename,0,strpos($filename,"/"));
		
		if($directory === $directory_part) {
		
			if(!(S3::deleteObject(bucketName,$filename)))
				echo "file couldn't be delete:".$filename;
		
		}
	
	}
	
	
	
}
?>