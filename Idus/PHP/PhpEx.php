<!DOCTYPE html>
<html>
    <head>
        <title>PHP Excercise</title>
    </head>
    <body>
    
        <?php
            //Input url which contains the data to display
            $api_url = "https://api.idus.dev/interview";

            //Global variables of the main array and it's length
            $resArr = array();
            $lenRes = 0;
            
            //Global items counter
            $cntItems = 0;

            //Global array which will printed in html
            $printArr = array();
            
            //Excute the main function with the input url
            main($api_url); 
        ?>

    </body>
</html>

<?php

    //The function sort the input nested array by the field name
    function sortByField($arr, $field) {
        $posisions = array();
        foreach($arr as $pos => $item) {
            $posisions[$pos] = $item[$field];
        }

        array_multisort($posisions, SORT_ASC, $arr);

        return $arr;              
    }

    //The function update the field 'level' by the level in the tree
    function updateLevel($level, $parentId , $arr) {
        for($i = 0; $i < $GLOBALS['lenRes']; $i++) {
            if($GLOBALS['resArr'][$i]['parent_id'] == $parentId) {
                $GLOBALS['resArr'][$i]['level'] = $level;                  
                array_push($arr, $GLOBALS['resArr'][$i]);
                $GLOBALS['cntItems']++;
            }
        }   
        return $arr;
    }

    //Recursive function order the levels arrays into one array
    function orderArr($arrLevels, $level, $id) {
        if($level == count($arrLevels))
            return;  
        $level++;
        foreach($arrLevels[$level] as $item) {
            if($id == $item['parent_id']) {
                array_push($GLOBALS['printArr'], $item);
                if($level < count($arrLevels) - 1)
                    orderArr($arrLevels, $level, $item['id']);
            }  
        }
    }

    //Function that creates html tags according to the order of the items
    function printHtml($currLevel, $prevLevel, $name) {
        $liOp = "<li>";
        $liCl = "</li>";
        $ulOp = "<ul>";
        $ulCl = "</ul>";
        
        $strSt = $liCl;
        $strMid = $ulCl . $liCl;
        $strEnd = $liOp . $name;

        $diff = $prevLevel - $currLevel;
        if($diff == -1)
            return $ulOp . $liOp . $name;
        
        $i = 0;
        $strMid1 = "";
        while($i < $diff) {
            $strMid1 .= $strMid;
            $i++;
        }
        
        return $strSt . $strMid1 . $strEnd; 
    }

    //Main function
    function main($api_url) {

        //Input the json from the url, convert it to array and sort it by the 'position' field
        $inputArr = file_get_contents($api_url);
        $inputArr = json_decode($inputArr, true);
        $inputArr = sortByField($inputArr, 'position');

        //Adding new 'level' field to each nested array and initilate it to -1
        $lenItem = strlen(json_encode($inputArr[0]));
        foreach($inputArr as $item) {
            $itemTemp = json_encode($item);
            $lenItem = strlen($itemTemp);   
            $itemTemp = substr($itemTemp, 0, $lenItem - 1) . ',"level":"-1"' . '}';
            $itemTemp = json_decode($itemTemp, true);
            array_push($GLOBALS['resArr'], $itemTemp);
        }

        //Initialte the global variable $lenRes 
        $GLOBALS['lenRes'] = count($GLOBALS['resArr']);

        //Update the 'level' field in main array and create new array which contains levels arrays
        $cntLevels = 0;
        $flag = true;
        $arrLevels = array();
        $tempArr = array();
        while($flag) {
            $arrLevels[$cntLevels] = array();
            if($GLOBALS['cntItems'] == 0) {              
                $arrLevels[$cntLevels] = updateLevel($cntLevels, 0, array());
                $tempArr = $arrLevels[$cntLevels];
            }
            else {
                foreach($tempArr as $item) {
                    $parentId = $item['id'];
                    $arrLevels[$cntLevels] = updateLevel($cntLevels, $parentId, $arrLevels[$cntLevels]); 
                } 
                $tempArr = $arrLevels[$cntLevels];
            }
            if($tempArr == null)
                $flag = false;
            $cntLevels++;
        } 
        $cntLevels--;

        //Delete the empty last item from array levels
        $tempArr = array();
        $len = count($arrLevels);
        for($i = 0; $i < $len - 1; $i++) {
            $tempArr[$i] = $arrLevels[$i];
        }
        $arrLevels = array();
        $arrLevels = $tempArr;

        /**
         * Insert all the arrays into one array using recursive function
         */ 
        $level = 0;
        $id = $arrLevels[$level][0]['id'];
        foreach($arrLevels[$level] as $item) {
            $id = $item['id'];
            array_push($GLOBALS['printArr'], $item);
            orderArr($arrLevels, $level, $id);
        }
        
        /**
         * Print arrRes to html
         */
        $lenArr = count($GLOBALS['printArr']);
        $liOp = "<li>";
        $liCl = "</li>";
        $ulOp = "<ul>";
        $ulCl = "</ul>";
        $strHtml = $ulOp . $liOp . $GLOBALS['printArr'][0]['name'];
        for ($i = 1; $i < $lenArr - 1; $i++) { 
            $currItemLevel = $GLOBALS['printArr'][$i]['level'];
            $prevItemLevel = $GLOBALS['printArr'][$i - 1]['level'];
            $name = $GLOBALS['printArr'][$i]['name'];
            
            $strHtml .= printHtml($currItemLevel, $prevItemLevel, $name);
        }

        $currItemLevel = $GLOBALS['printArr'][$lenArr - 1]['level'];
        $prevItemLevel = $GLOBALS['printArr'][$lenArr - 2]['level'];
        $name = $GLOBALS['printArr'][$lenArr - 1]['name'];
        $strHtml .= printHtml($currItemLevel, $prevItemLevel, $name);

        $strHtml .= $ulCl;
        echo $strHtml;
   
    }

?>

