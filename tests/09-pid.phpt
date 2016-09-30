--TEST--
pid test
--FILE--
<?php
require_once 'includes/init.php';
PDO_DataObject::debugLevel(1);
PDO_DataObject::config(array(
        'class_location' => __DIR__.'/includes/sample_classes/DataObjects_',
    // fake db..
        'database' => 'mysql://user:pass@localhost/gettest'
   // real db...
    //    'database' => 'mysql://root:@localhost/pman',
    //    'PDO' => 'PDO',        'proxy' => 'full',
));

echo "\n\n--------\n";
echo "get some pid's\n" ;

$company = PDO_DataObject::factory('Companies');
if ($company->get(12)) {
    echo "PID is : " . $company->pid();
}



echo "\n\n--------\n";
echo "get pid on object that does not support it..\n" 

$events = PDO_DataObject::factory('Events');
$events->limit(1);
$events->find(true);
//try {
    $pid $company->pid();
//} catch

?>
--EXPECT--
