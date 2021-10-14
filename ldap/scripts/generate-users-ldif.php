<?php

$source_file_path = $argv[1]; // We expect full path
$baseDN = $argv[2]; // The base DN that wil be used

$firstRow = 2; // We skip CSV file headers
$row = 1;
date_default_timezone_set('Europe/Paris');
$currDate= date("Y-m-d, H:i ");
$limit = 12000;

if (($handle = fopen($source_file_path, "r")) !== FALSE) {
    echo "# Sample users for ".$baseDN." generated at ".$currDate." \n\n";

    $members = "";
    $vipMembers  = "";
    while ((($data = fgetcsv($handle, 0, ",")) !== FALSE) && ($row <= $limit)){
        if($row < $firstRow) { $row++; continue; }
   
        $num = count($data);
        echo "dn: uid=".$data[2].",ou=".$data[5].",ou=people,".$baseDN."\n";
        echo "changetype: add\n";
        echo "gidNumber: 0\n";
        echo "objectClass: inetOrgPerson\n";
        echo "objectClass: organizationalPerson\n";
        echo "objectClass: person\n";
        echo "objectClass: top\n";
        echo "objectClass: posixAccount\n";
        echo "uidNumber: ".$data[0]."\n";
        echo "uid: ".$data[2]."\n";
        if ($data[3] != "Umlaut"){
            echo "homeDirectory: /home/".$data[2]."\n";
        }else{
            echo "homeDirectory: /home/".$data[3]."\n";
        }
        echo "sn: ".$data[3]."\n";
        echo "cn: ".$data[4]." ".$data[3]."\n";
        echo "givenName: ".$data[4]."\n";
        echo "mail: ".$data[1]."\n";
        echo "employeeType: ".$data[5]."\n";        
        echo "displayName: ".$data[4]." ".$data[3]."\n";
        echo "userPassword:: UEBzc3cwcmQ=\n";
        echo "\n";

        if ($data[6] != ""){
            $groups = explode("|", $data[6]);
            foreach($groups as $group)
            {
                if (!empty($group)){
                    echo "dn: ".$group."\n";
                    echo "changetype: modify\n";
                    echo "add: member\n";
                    echo "member: uid=".$data[2].",ou=".$data[5].",ou=people,".$baseDN."\n";
                    echo "\n";
                }
            }
        }

        // Prepare a list of users to add to the allusers group
        // Note that this list must *NOT* be empty, because groupOfNames class makes the member attribute mandatory. 
        // This means that it is not possible to create an empty group.
        if (($row < 1020) && ($data[5] !== "visitor") && ($row != 2)) {
            $members .= "member: uid=".$data[2].",ou=".$data[5].",ou=people,".$baseDN."\n";
        }

        // Also Prepare a list of VIP users
        if (($row < 5) && ($data[5] !== "visitor")) {
            $vipMembers .= "member: uid=".$data[2].",ou=".$data[5].",ou=people,".$baseDN."\n";
        }

        $row++;
    }

    fclose($handle);

    echo "dn: cn=All Users,ou=traversal,ou=groups,".$baseDN."\n";
    echo "changetype: add\n";
    echo "objectClass: groupOfNames\n";
    echo "objectClass: top\n";
    echo "cn: All Users\n";
    echo $members;
    echo "description: All additional users defined in the CSV file, except the second and visitors\n";
    echo "\n";

    echo "dn: cn=VIPs,ou=traversal,ou=groups,".$baseDN."\n";
    echo "changetype: add\n";
    echo "objectClass: groupOfNames\n";
    echo "objectClass: top\n";
    echo "cn: VIPs\n";
    echo  $vipMembers;
    echo "description: Another smaller group with only a few chosen employees of the Example organisation\n";
    echo "\n";

}