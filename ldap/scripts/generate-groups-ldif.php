<?php

// Old legacy PHP script - un-used for the time being (Aug. 2021)

$source_file_path = $argv[1]; // We expect full path
$baseDN = $argv[2]; // The base DN that wil be used

// A well-known user that must be present
$wkUser = "uid=billy,ou=staff,ou=people,".$baseDN;

$firstRow = 2; // We skip CSV file headers
$row = 1;
date_default_timezone_set('Europe/Paris');
$currDate= date("Y-m-d, H:i ");
$limit = 120000;


$userTypeGroups = [];
$department = [];

if (($handle = fopen($source_file_path, "r")) !== FALSE) {
    echo "# Dummy groups for ".$baseDN." generated at ".$currDate." \n\n";

    $members = "";

    while ((($data = fgetcsv($handle, 1000, ",")) !== FALSE) && ($row <= $limit)){
        if($row < $firstRow) { $row++; continue; }
        $num = count($data);
        if ($data[5] != "" && !isset($userTypeGroups[$data[5]]))  $userTypeGroups[$data[5]] = $data[5];
        if ($data[6] != "" && !isset($department[$data[6]]))  $department[$data[6]] = $data[6];
        $row++;
    }

    // Create groups with the default well-known user as unique member
    foreach ($userTypeGroups as $groupName) {
        echo "dn: cn=".$groupName.",ou=groups,".$baseDN."\n";
        echo "changetype: add\n";
        echo "objectClass: groupOfNames\n";
        echo "objectClass: top\n";
        echo "cn: ".$groupName."\n";
        echo "member: ".$wkUser."\n";
        echo "\n"; 
    }

    // Create organisational units:
    foreach($department as $dept){
        echo "dn: ou=".$dept.",ou=people,".$baseDN."\n";
        echo "changetype: add\n";
        echo "objectClass: organizationalUnit\n";
        echo "objectClass: top\n";
        echo "ou: ".$dept."\n";
        echo "\n"; 
    }        
    fclose($handle); 
}

// 2nd Pass: 
if (($handle = fopen($source_file_path, "r")) !== FALSE) {
    
    $members = "";
    $row = 1;
    while ((($data = fgetcsv($handle, 1000, ",")) !== FALSE) && ($row <= $limit)){
 
        if($row < $firstRow) { $row++; continue; }
    
        $num = count($data);
        
        if (isset($departMent[$data[6]])) {
            $userDn = "uid=".$data[2].",ou=".$department[$data[6]].",ou=people,".$baseDN."\n";
        } else {
            $userDn = "uid=".$data[2].",ou=staff,ou=people,".$baseDN."\n";
        }
        
        echo "dn: ".$userDn;
        echo "changetype: add\n";
        echo "gidNumber: 0\n";
        echo "objectClass: inetOrgPerson\n";
        echo "objectClass: organizationalPerson\n";
        echo "objectClass: person\n";
        echo "objectClass: top\n";
        echo "objectClass: posixAccount\n";
        echo "uidNumber: ".$row."\n";
        echo "uid: ".$data[2]."\n";
        echo "homeDirectory: /home/".$data[2]."\n";
        echo "sn: ".$data[3]."\n";
        echo "cn: ".$data[4]." ".$data[3]."\n";
        echo "mail: ".$data[1]."\n";
        echo "employeeType: ".$data[5]."\n";        
        echo "displayName: ".$data[4]." ".$data[3]."\n";
        if (isset($userTypeGroups[$data[5]])) {
            echo "memberOf: cn=".$userTypeGroups[$data[5]].",ou=groups,".$baseDN."\n";
        }
        echo "userPassword:: UEBzc3cwcmQ=\n";
        echo "\n";

        if ( isset($userTypeGroups[$data[5]]) ) {
            echo "dn: cn=".$userTypeGroups[$data[5]].",ou=groups,".$baseDN."\n";
            echo "changetype: modify\n";
            echo "add: member\n";
            echo "member: ".$userDn;            
            echo "\n";
        }
       
        $row++;
    }

    fclose($handle); 
}