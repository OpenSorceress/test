<?php
/**
 * Gearman worker functionality; instantiates the Heap object.
 */

require('./index.php');
$worker = new GearmanWorker();
$worker->addServer();
$worker->addFunction("churn", function (GearmanJob $job) {
	$workload = $job->workload();
	echo "Received job: " . $job->handle() . "\n\r";
	echo "Workload: $workload\n";
	$result = new Heap($workload);
	echo "Result: " . json_encode($result) . "\n\r";
	return json_encode($result);
});
while ($worker->work());

