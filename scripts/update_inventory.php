#!/usr/bin/php
<?PHP
###########################################################################################################
###> New x php -> scripts/update_inventory.php  -> Initial creation user => eric => 2022-02-20_20:06:20 ###
###########################################################################################################
###>  New version of update_inventory.
###>  This version generates yaml intead of ini
###>  
###>  Run it at the end of the deployment, at least after host groups/vars are populated
###>
# ###  >>  Simple play to run it pass in different inventory json.  Only handles one argument currently.
#    - name: Update inventory yaml file inventory/inventory.yml
#      shell: |
#        /usr/bin/php /path-to-scripts/update_inventory.php groups
#
###>  Leveraging newly created hosts grouped with add_host:
#
#    - name: Register MysQL host to groups ec2hosts and mysql
#      add_host:
#        hostname: "{{ ec2_db['instances'][0]['network_interfaces'][0]['association']['public_ip'] }}"
#        groups:
#          - ec2hosts
#          - mysql
#      register: groups
# [eric - 2024-01-31-00:37:31]#> Added document merge
#####################################################################
#_#>
#
###> CLI colors
# $Red='\e[0;31m'; $BRed='\e[1;31m'; $BIRed='\e[1;91m'; $Gre='\e[0;32m'; $BGre='\e[1;32m'; $BBlu='\e[1;34m'; $BWhi='\e[1;37m'; $RCol='\e[0m';
function merge_inv($a,$b){
 //   print_r($a);
 //   print_r($b);
    foreach ($a as $k => $v){
	$ag[]=$k;
    }
    foreach ($b as $k => $v){
	$bg[]=$k;
    }
    $g=array_merge($ag,$bg);
    $g=array_unique($g);
    foreach($g as $k => $v){
	if((is_array($a[$v]['hosts']))&&(is_array($b[$v]['hosts']))){
	    $m[$v]['hosts']=array_merge($a[$v]['hosts'],$b[$v]['hosts']);
//	    $m[$v]['hosts']=array_unique($m[$v]['hosts']);
	}elseif(is_array($a[$v]['hosts'])){
	    $m[$v]['hosts']=$a[$v]['hosts'];
	}elseif(is_array($b[$v])){
            $m[$v]['hosts']=$b[$v]['hosts'];
        }
        if((is_array($a[$v]['vars']))&&(is_array($b[$v]['vars']))){
            $m[$v]['vars']=array_merge($a[$v]['vars'],$b[$v]['vars']);
//            $m[$v]['vars']=array_unique($m[$v]['vars']);
        }elseif(is_array($a[$v]['vars'])){
            $m[$v]['vars']=$a[$v]['vars'];
        }elseif(is_array($b[$v])){
            $m[$v]['vars']=$b[$v]['vars'];
        }
    }
	
    print_r($m);
    return $m;
}

$__dir__=__dir__;
$yaml_inventory_file='/home/eric/dep-1/rwi-wp/playbook/inventory/inventory.yml';  ###> Original inventory yaml

###> Recieve and tune up the groups far json
$in=$argv[1];  ###>  Bring in groups var arg
$j=explode('{',$in);  ###> Getting rid of any junk along with, this may not be necessary
$s=explode('}',$j[1]);  ###> Trailing as well
$json='{'.$s[0].'}';  ###> put it back together
$json=str_replace("'",'"',$json);  ###> Necessary!! Big issue I ran into, The playbook output groups double quotes, passes single as arg, decode doesn't like single


$dump= json_decode($json, true);  ###> turn the json into array

###> Reorganize the json array dump as inventory yaml
$new=array();  
foreach($dump as $key => $value){
	$t='';
        $tmp= array();
	for($i=0;$i<count($value);$i++){
		$t='';
		$t= array ('ansible_host' => $value[$i]);
		if($value[$i]!='' && $value[$i]!='0:'){
			$tmp['hosts'][$value[$i]] = $t;
		}
	}
	$new[$key]=$tmp;
}

###> Reading yaml inventory into array <********************************
$data=yaml_parse_file($yaml_inventory_file, 0);   ###>   This is the second array
###> end of reading yaml section  <***************************************

###> Run the integration function to  Merge the two multi-demensional arrays
$inv=merge_inv($data,$new);


$inv=array_filter($inv);  // Attempt strip the empty elements

###> Turn the new merged array into inventory yaml
$yml=yaml_emit($inv, YAML_UTF8_ENCODING, YAML_ANY_BREAK);
$yml=preg_replace('/    0: \~\n/','',$yml);  // strage elements generated between the merge and yaml_emit ['   0: ~'].

###> Backup the original inventory file
$d=date('Y-m-d');
copy($yaml_inventory_file, $yaml_inventory_file."_backup_".$d);

###> recreate the inventory file
$yHandle = fopen($yaml_inventory_file,'w');

###> 
if(!fwrite($yHandle,$yml)){
	echo "FAILURE: Error to write the new inventory file.";
} 
fclose($yHandle);

?>
