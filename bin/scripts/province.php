<?php

require 'database.php';

$province_name = array ("Alberta","British Columbia","Manitoba",'Saskatchewan','Quebec','Nova Scotia','Newfoundland','New Brunsiwck','Yukon','Nunavut','Prince Edward Island','Ontario','Northwest Territories');
$province_code = array ("AB","BC","MB",'SK','QC','NS','NL','NB','YT','NU','PEI','ON','NT');

$provinces = array_combine($province_code, $province_name);

$count = 0;

foreach($provinces as $code => $name) {
    try {
        $db->insertAsDict('provinces_ca', compact('name', 'code'));
        $count++;
    } catch (Exception $e) {
        echo $e->getMessage(), EOL;
    }
}

echo "$count DONE\n";
