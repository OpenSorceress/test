<?php
/**
 * Gearman server functionality with (very) rudimentary status monitoring.
 */

$arr = array(3, 4, 9, 14, 15, 19, 28, 37, 47, 50, 54, 56, 59, 61, 70, 73, 78, 81, 92, 95, 97, 99);
$max = pow(2, count($arr));
$q = $max / 4;
$qs = array($q, $q*2+1, $q*2+$q+1, $max);
$ks = array(0, $qs[0], $qs[1], $qs[2]);

$client = new GearmanClient();
$client->addServer();
$client->setOptions(GEARMAN_CLIENT_NON_BLOCKING);

for ($x = 1; $x <= 4; $x++) {

	$task = "task{$x}";
	$q = "q{$x}";
	$job = "job{$x}";
	$status = "status{$x}";

	$$q = json_encode(array("arr"=>$arr, "start"=>$ks[$x-1], "end"=> $qs[$x-1]));
	$$task = $client->addTaskBackground("churn", $$q);
	$$job = $$task->jobHandle();
	var_dump($client->jobStatus($$job));
	$$status = $client->addTaskStatus($$job);

}

echo "Fetching...\n";
$start = microtime(true);

$totaltime = number_format(microtime(true) - $start, 2);


$results[] = $client->runTasks();

echo "Got user info in: $totaltime seconds:\n";
var_dump($results);
