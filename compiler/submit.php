<?php
session_start();
if( $_FILES['file']['name'] != "" && isset($_POST['cry']) && $_POST['cry']==$_SESSION['sec'] && isset($_POST['language'])) 
{
	include("mycrypt.php");
	include("envVar.php");
	$_SESSION['sec']=myencrypt(myrand());
	include("db.php");
	$contest=$_SESSION['contest'];
	$problem=$_SESSION['problem'];
	$user=$_SESSION['uid'];
	$filename=$_FILES['file']['name'];
	$language=check_input($_POST['language']);
	$q=mysql_query("SELECT time_limit FROM problems WHERE contest='$contest' && pcode='$problem'") or die("error got");
	$r=mysql_fetch_array($q);
	$time_limit=$r[0];
	$pid=popen("python /home/krishna/online/Online-programming-judge/compiler/createUser.py $contest $pcode $user","r") or die("error");
	$ot=fread($pid,256);
	if($ot=="1"){
    move_uploaded_file($_FILES['file']["tmp_name"],pathtocontest.$contest."/".$problem."/".$user."/".$filename) or die("sry something went wrong!!!");
    $crypt=(string)time().myencrypt(myrand()).$_SESSION['uid'];
	auth_submit($crypt,$_COOKIE['PHPSESSID'],$contest,$problem,$filename);
	header("Location:/compile/runner.py?contest=$contest&problem=$problem&cry=$crypt&language=$language&filename=$filename&time_limit=$time_limit");
	}
	else{
		echo "something wrong with user name.";
		}
}
else
{
    die("No file specified!");
}
?>
