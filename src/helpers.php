<?php
    if(!$ALLOW_HELPER){
        header("Location: index.php");
    }

    /*
        returns only the most recent version of each item for each ID.
        Items are elements from products.csv.
        If there are IDs 0.0, 1.0, 2.0, 2.1, 2.2, 3.0, 3.1, it 
        will return 0.0, 1.0, 2.2, 3.1
    */
    function get_latest_items($products_file_path){
        return get_latest_items_with_offset($products_file_path, 0);
    }

    /*
        returns only the most recent version of each item for each ID minus the offset.
        Items are elements from products.csv.
        If there are IDs 0.0, 1.0, 2.0, 2.1, 2.2, 3.0, 3.1, it 
        will return 0.0, 1.0, 2.2, 3.1 for offset 0 and
        null, null, 2.1, 3.0 for offset 1
    */
    function get_latest_items_with_offset($products_file_path, $offset){
        $requested_items = array();
        $handle = fopen($products_file_path, "r");
        if($handle){
            fgets($handle);
            $prev_items = array();
            array_push($prev_items, fgetcsv($handle));
            while(($arr = fgetcsv($handle)) !== false){
                if(intval($prev_items[0][0]) !== intval($arr[0])){
                    if(count($prev_items) > $offset){
                        array_push($requested_items, $prev_items[count($prev_items)-$offset-1]);
                    }
                    else{
                        array_push($requested_items, null);
                    }
                    $prev_items = array();
                }
                array_push($prev_items, $arr);
            }
            if(count($prev_items) > $offset){
                array_push($requested_items, $prev_items[count($prev_items)-$offset-1]);
            }
            else{
                array_push($requested_items, null);
            }
            fclose($handle);
        }
        return $requested_items;
    }

    // convert date String to PHP DateTime object
    function to_php_date($my_date){
        if($my_date == ""){
            return null;
        }
        $day = substr($my_date, 0, 2);
        $month = substr($my_date, 3, 2);
        $year = substr($my_date, 6, 4);
        $time = substr($my_date, 11);
        return new DateTime($year . "-" . $month . "-" . $day . " " . $time);
    }

    // test whether a date is between two others
    function is_between($test, $start, $end){
        $res = true;
        if($start != null){
            $res = $res && $start <= $test;
        }
        if($end != null){
            $res = $res && $test <= $end;
        }
        return $res;
    }

    // parse Pfand-String and return it in an array
    function resolve_pfand($p){
        $ret = array();
        if($p != ""){
            $pfand_splitted = explode(";", $p);
            foreach($pfand_splitted as $ps){
                $pfand_instance_splitted = explode("x", $ps);
                $tmp = array(intval($pfand_instance_splitted[0]), floatval($pfand_instance_splitted[1]));
                array_push($ret, $tmp);
            }
        }
        return $ret;
    }

    // parse Order-String and return it in an array
    function resolve_order($o){
        $ret = array();
        if($o != ""){
            $order_splitted = explode(";", $o);
            foreach($order_splitted as $os){
                $drink_type_split = explode(":", $os);
                $kasten_split = explode("_", $drink_type_split[1]);
                $tmp = array(floatval($drink_type_split[0]), intval($kasten_split[0]), intval($kasten_split[1]));
                array_push($ret, $tmp);
            }
        }
        return $ret;
    }

    // generate a salted password hash that can be put in the data/config.ini file
    function generate_password_hash($clear_text){
        if(defined("CRYPT_BLOWFISH") && CRYPT_BLOWFISH){
            $hash = password_hash($clear_text, PASSWORD_DEFAULT);
            echo $hash;
        }
    }
?>
