--TEST--
link Test 
--FILE--
<?php
require_once 'includes/init.php';

PDO_DataObject::config(array(
        'class_location' => __DIR__.'/includes/sample_classes/DataObjects_',
    // fake db..
   
        'database' => 'mysql://user:pass@localhost/inserttest',
        'class_prefix' => 'DataObjects_',
));

PDO_DataObject::debugLevel(0);
 
// these need the links to calculate the join..
echo "\n---\nDefault Columns\n";
PDO_DataObject::factory('joinerb')
    ->load(1)
    ->link()
);

 
?>
--EXPECT--
---
Default Columns
__construct==["mysql:dbname=inserttest;host=localhost","user","pass",[]]
setAttribute==[3,2]
array (
  'childa_id' => 'childa:ca_id',
  'childc_id' => 'childa:ca_id',
)
---
 After setting this instance
array (
  'childb_id' => 'childb:cb_id',
)
---
 new instance
array (
  'childa_id' => 'childa:ca_id',
  'childc_id' => 'childa:ca_id',
)
alan@alandesks