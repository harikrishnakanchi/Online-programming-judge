<?php
function get_output($cmd) {
    $descriptorspec = array(0 => array('pipe', 'r'),     // stdin
                            1 => array('pipe', 'w'),     // stdout
                            2 => array('pipe', 'w'));    // stderr

    $process = proc_open($cmd, $descriptorspec, $pipes);
    $output = '';
    if (is_resource($process)) {
        fwrite($pipes[0], 'some std input can be here');    // not necessary
        fclose($pipes[0]);

        $output = stream_get_contents($pipes[1]);
        $err = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        proc_close($process);
    }
    return $output;
}
session_start();
include("mycrypt.php");
$gotallinputs=0;
if(isset($_POST['contest']) && isset($_POST['cry']) && isset($_POST['title']) && isset($_POST['pblm']) && isset($_POST['time']) && isset($_POST['inputs']) && isset($_POST['outputs']) && isset($_POST['pcode'])){
	$gotallinputs=1;
	}
if (isset($_SESSION['sec']) && $gotallinputs==1 && $_POST['cry']==$_SESSION['sec']){
	include("db.php");
	$_SESSION['sec']=myencrypt(myrand());
	$contest=check_input($_POST['contest']);
	$title=check_input($_POST['title']);
	$pblm=mysql_real_escape_string(htmlspecialchars($_POST['pblm']));
	$t=check_input($_POST['time']);
	$pcode=check_input($_POST['pcode']);
	$inputs=mysql_real_escape_string(htmlspecialchars($_POST['inputs']));
	$outputs=mysql_real_escape_string(htmlspecialchars($_POST['outputs']));
		mysql_query("INSERT INTO problems (contest,problem_title,pcode,problem,time_limit,inputs,outputs) VALUES ('$contest','$title','$pcode','$pblm','$t','$inputs','$outputs')") or die(mysql_error());
		$q=mysql_query("SELECT * FROM contests WHERE name='$contest'");
		$r=mysql_fetch_array($q);
		$temp=$r['num'];
		$temp=$temp+1;
		mysql_query("UPDATE contests SET num=$temp WHERE name='$contest'")or die(mysql_error());
		$fn1=myencrypt(myrand());
		$f=fopen("/tmp/".$fn1,"w");
		fwrite($f,$_POST['inputs']);
		fclose($f);
		$fn2=myencrypt(myrand());
		$f=fopen("/tmp/".$fn2,"w");
		fwrite($f,$_POST['outputs']);
		fclose($f);
		$contest=escapeshellarg($contest);
		$pcode=escapeshellarg($pcode);
		$ot=get_output("python /home/krishna/online/Online-programming-judge/compiler/createProblems.py $contest $pcode $fn1 $fn2");
		//$pid=popen("python /home/krishna/online/Online-programming-judge/compiler/createProblems.py $contest $pcode $fn1 $fn2","r") or die("error");
		//$ot=fread($pid,256);
		echo gettype($ot);
		if ($ot=="one"){echo "Successfully inserted problem";}
		else{echo "miss";}
		//pclose($pid);
		//header("Location:/compile/createProblems.py?contest=$contest&title=$pcode&cry=$crypt&inputs=".urlencode($_POST['inputs'])."&outputs=".urlencode($_POST['outputs']));
	}
else{
		header("Location:index.php?err=3");
	}
?>
