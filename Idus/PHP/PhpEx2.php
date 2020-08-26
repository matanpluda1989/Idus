<!DOCTYPE html>
<html>
    <head>
        <title>PHP Excercise</title>
    </head>
    <body>
    
        <?php
            $resArr = array();
            $lenRes = 0;
            main();
        ?>

    </body>
</html>

<?php

    function sortByPos($arr) {
        $posisions = array();
        foreach($arr as $pos => $item) {
            $posisions[$pos] = $item['position'];
        }

        array_multisort($posisions, SORT_ASC, $arr);

        return $arr;              
    }

    function updateLevel($level, $parentId , $arr) {
        for($i = 0; $i < $GLOBALS['lenRes']; $i++) {
            if($GLOBALS['resArr'][$i]['parent_id'] == $parentId) {
                $GLOBALS['resArr'][$i]['level'] = $level;
                array_push($arr, $GLOBALS['resArr'][$i]);
            }
        }   
        return $arr;
    }

    function updateLevel1($level, $parentId) {
        $arr = array();
        for($i = 0; $i < $GLOBALS['lenRes']; $i++) {
            if($GLOBALS['resArr'][$i]['parent_id'] == $parentId) {
                $GLOBALS['resArr'][$i]['level'] = $level;
                array_push($arr, $GLOBALS['resArr'][$i]);
            }
        }   
        return $arr;
    }

    function main() {
        $api_url = "https://api.idus.dev/interview";
        $inputArr = file_get_contents($api_url);
        $inputArr = json_decode($inputArr, true);

        $inputArr = sortByPos($inputArr);

        $lenItem = strlen(json_encode($inputArr[0]));
        

        foreach($inputArr as $item) {
            $itemTemp = json_encode($item);
            $lenItem = strlen($itemTemp);
            
            $itemTemp = substr($itemTemp, 0, $lenItem - 1) . ',"level":"-1"' . '}';
            $itemTemp = json_decode($itemTemp, true);
            array_push($GLOBALS['resArr'], $itemTemp);
        }

        $GLOBALS['lenRes'] = count($GLOBALS['resArr']);

        $level0Arr = array();
        $levelInnerArr = array(); 
        $levelInner2Arr = array(); 

        $level0Arr = updateLevel(0, 0, $level0Arr);      
        
        foreach($level0Arr as $item) {
            $parentId = $item['id'];
            $levelInnerArr = updateLevel(1, $parentId , $levelInnerArr);  
        }    

        foreach($levelInnerArr as $item) {
            $parentId = $item['id'];
            $levelInner2Arr = updateLevel(2, $parentId, $levelInner2Arr);
        }


        /**
         * Insert all the arrays of each level to one nested array
         * arrRes - nested array 
         */
        $arrRes = array();
        $tempArr = array();
        $tempArr2 = array();
        $len1 = count($levelInnerArr);
        $len2 = count($levelInner2Arr);
        $i = 0;
        $j = 0;
        while($i < $len1) {
            $tempArr = array();
            while($levelInner2Arr[$j]['parent_id'] == $levelInnerArr[$i]['id']) {
                array_push($tempArr, $levelInner2Arr[$j]);
                $j++;
                if($j == $len2 - 1){
                    array_push($tempArr, $levelInner2Arr[$j]);
                    break;
                }     
            }
            $tempArr2 = array_slice($levelInnerArr, $i, 1, true);
            array_push($tempArr2, $tempArr);             
            array_push($arrRes, $tempArr2);
            $i++;
        }


        $tempResArr = $arrRes; //Array level 1 
        $arrRes = array();
        $tempArr = array();
        $tempArr2 = array();
        $len1 = count($level0Arr);
        $len2 = count($tempResArr);
        $i = 0;
        $j = 0;
        while($i < $len1) {
            $tempArr = array();
            while($tempResArr[$j][$j]['parent_id'] == $level0Arr[$i]['id']) {
                array_push($tempArr, $tempResArr[$j]);
                $j++;
                if($j == $len2 - 1){
                    array_push($tempArr, $tempResArr[$j]);
                    break;
                }     
            }
            $tempArr2 = array_slice($level0Arr, $i, 1, true);
            array_push($tempArr2, $tempArr);             
            array_push($arrRes, $tempArr2);
            $i++;
        }

        //print_r($arrRes);
        echo json_encode($arrRes);
        echo "</br></br></br>";
        /**
         * Print arrRes to html
         */
        /*
        for($i = 0; $i < count($arrRes); $i++) {
            $arr = $arrRes[$i];
            echo "<li>" . $arrRes[$i][$i]['name'];
            foreach($arr[$i+1] as $item) {
                echo "<ul>" . $item['name'] . "</ul>";
            }
            echo "</li>";    
        }*/


        /*
        for($i = 0; $i < count($arrRes); $i++) {;
            $arr = $arrRes[$i];
            echo "<li>" . $arr[$i]['name'];
            foreach ($arr as $item) {
                //echo $item[0][0]['name'] . "</br>";
            }
            echo "</li>";    
        }

        
        $arr = $arrRes[0][0]['name']; //About us
        print_r($arr);
        echo "</br>";
        
            $arr = $arrRes[0][1][0][0]['name']; //Department
            print_r($arr);
            echo "</br>";
        
                $arr = $arrRes[0][1][0][1][0]['name']; //sales
                print_r($arr);
                echo "</br>";
                $arr = $arrRes[0][1][0][1][1]['name']; //development
                print_r($arr);
                echo "</br>";
                $arr = $arrRes[0][1][0][1][2]['name']; //support
                print_r($arr);
                echo "</br>";

            $arr = $arrRes[0][1][0][0]['name']; //Managment
            print_r($arr);
            echo "</br>";
            
            $arr = $arrRes[0][2][0][1][1]['name']; //CEO
            print_r($arr);
            echo "</br>";
            $arr = $arrRes[0][2][0][1][2]['name']; //CTO
            print_r($arr);
            echo "</br>";
            */

    }
?>

