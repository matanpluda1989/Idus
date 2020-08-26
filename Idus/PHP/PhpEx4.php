<!DOCTYPE html>
<html>
    <head>
        <title>PHP Excercise</title>
    </head>
    <body>
    
        <?php
            //Api url which contain the data to display
            $api_url = "https://api.idus.dev/interview";

            //Global variables to the main array and his length
            $resArr = array();
            $lenRes = 0;
            
            //Global variable to count items which were initilzed with 'level'
            $cntItems = 0;

            //Global variables to link item to the next
            $linkFirst = 0;
            $linkLast = 0;
            $linkNext = 0;
            
            //Excute the main function with the api
            main($api_url); 
        ?>

    </body>
</html>

<?php

    //The function input nested array and field and sort it by the field
    function sortByField($arr, $field) {
        $posisions = array();
        foreach($arr as $pos => $item) {
            $posisions[$pos] = $item[$field];
        }

        array_multisort($posisions, SORT_ASC, $arr);

        return $arr;              
    }

    /*parentId = 0,
    i = 0:
        linkNext = resArr[0]['link'] = 21
        resArr[0]['link'] = 0
        resArr[0]['link'] = 21
    i = 1:
        linkNext = resArr[0]['link'] = 21
        resArr[0]['link'] = 1
        resArr[1]['link'] = 21
    i = 2:
        linkNext = resArr[0]['link'] = 21
        resArr[0]['link'] = 2
        resArr[2]['link'] = 21
    */

    //The function update the fields 'level' and 'link' by the level in the tree
    function updateLevelLink($level, $parentId , $arr) {
        $GLOBALS['linkLast'] = $parentId;
        for($i = 1; $i < $GLOBALS['lenRes']; $i++) {
            if($GLOBALS['resArr'][$i]['parent_id'] == $parentId) {
                $GLOBALS['resArr'][$i]['level'] = $level;                  
                
                $GLOBALS['linkNext'] = $GLOBALS['resArr'][$GLOBALS['linkLast']]['link'];
                $GLOBALS['resArr'][$GLOBALS['linkLast']]['link'] = $GLOBALS['resArr'][$i]['id'];
                $GLOBALS['resArr'][$i]['link'] = $GLOBALS['linkNext'] /*$GLOBALS['resArr'][$GLOBALS['linkLast']]['link']*/ ;
                $GLOBALS['linkLast'] = $GLOBALS['resArr'][$i]['id'] - 1;
                

                array_push($arr, $GLOBALS['resArr'][$i]);
                $GLOBALS['cntItems']++;
            }
        }   
        return $arr;
    }

    function updateLevelLink1($level, $parentId , $arr) {
        for($i = 0; $i < $GLOBALS['lenRes']; $i++) {
            if($GLOBALS['resArr'][$i]['parent_id'] == $parentId) {
                $GLOBALS['resArr'][$i]['level'] = $level;
                array_push($arr, $GLOBALS['resArr'][$i]);
                $GLOBALS['cntItems']++;
            }
        }   
        return $arr;
    }

    //Main function
    function main($api_url) {

        //Input the json from the api, convert it to array and sort it by 'position' field
        $inputArr = file_get_contents($api_url);
        $inputArr = json_decode($inputArr, true);
        $inputArr = sortByField($inputArr, 'position');

        //Adding fields 'level' and 'link' to each nested array and initialte it to -1
        $lenItem = strlen(json_encode($inputArr[0]));
        foreach($inputArr as $item) {
            $itemTemp = json_encode($item);
            $lenItem = strlen($itemTemp);   
            $itemTemp = substr($itemTemp, 0, $lenItem - 1) . ',"level":"-1","link":"-1"' . '}';
            $itemTemp = json_decode($itemTemp, true);
            array_push($GLOBALS['resArr'], $itemTemp);
        }

        //Initialte the global variable $lenRes 
        $GLOBALS['lenRes'] = count($GLOBALS['resArr']);

        //Updating the 'level' and 'link' fields in $resArr
        $GLOBALS['resArr'][0]['link'] = $GLOBALS['lenRes'];

        $cntLevels = 0;
        $flag = true;
        $tempArr = array();
        $prevTempArr = array();
        while($flag) {
            $tempArr = array();
            if($GLOBALS['cntItems'] == 0) {              
                $tempArr = updateLevelLink($cntLevels, 0, array());
                $prevTempArr = $tempArr;
                echo "</br></br>";
                print_r(json_encode($GLOBALS['resArr']));
            }
            else {
                foreach($prevTempArr as $item) {
                    $parentId = $item['id'];
                    $tempArr = updateLevelLink($cntLevels, $parentId, $tempArr); 
                } 
                echo "</br></br>";
                print_r(json_encode($GLOBALS['resArr']));
                $prevTempArr = $tempArr;
            }
            if($prevTempArr == null)
                $flag = false;
            $cntLevels++;
        } 
        $cntLevels--;

        //Sort the array by the link
        //$GLOBALS['resArr'] = sortByField($GLOBALS['resArr'], 'link');

        //print_r(json_encode($GLOBALS['resArr']));
        exit;
        

        /**
         * Insert all the arrays to one array by the order of the print
         */ //check if possible to add break
        $printArr = array();
        $iter1 = 0;
        $iter2 = 0;
        $lenArr1 = count($levelInnerArr);
        $lenArr2 = count($levelInner2Arr);
        foreach($level0Arr as $item0) {
            $id = $item0['id'];
            array_push($printArr, $item0);  
            while($iter1 < $lenArr1 && $id == $levelInnerArr[$iter1]['parent_id']) {
                $id1 = $levelInnerArr[$iter1]['id'];
                array_push($printArr, $levelInnerArr[$iter1]);
                $iter1++;
                while($iter2 < $lenArr2 && $id1 == $levelInner2Arr[$iter2]['parent_id']) {  
                    array_push($printArr, $levelInner2Arr[$iter2]);
                    $iter2++;
                }
            }
        }
        
        /**
         * Print arrRes to html
         */
        $lenArr = count($printArr);
        $liOp = "<li>";
        $liCl = "</li>";
        $ulOp = "<ul>";
        $ulCl = "</ul>";
        
        $strHtml = $ulOp . $liOp . $printArr[0]['name'];
        for ($i = 1; $i < $lenArr - 1; $i++) { 
            $currItemLevel = $printArr[$i]['level'];
            $nextItemLevel = $printArr[$i + 1]['level'];
            $prevItemLevel = $printArr[$i - 1]['level'];
            $name = $printArr[$i]['name'];

            if($currItemLevel == 0) {
                if ($prevItemLevel == 2) {
                    $strHtml .= $ulCl . $liCl . $ulCl . $liCl . $liOp . $name;
                }
            }
            else if ($currItemLevel == 1) {
                if ($prevItemLevel == 0) {
                    $strHtml .= $ulOp . $liOp . $name;
                }
                else if ($prevItemLevel == 2) {
                    $strHtml .= $liCl . $ulCl . $liCl . $liOp . $name;
                }
            }
            else if ($currItemLevel == 2) {
                if ($prevItemLevel == 1) {
                    $strHtml .= $ulOp . $liOp . $name;
                }
                else if ($prevItemLevel == 2) {
                    $strHtml .= $liCl . $liOp . $name;
                }

            }      
        }

        $currItemLevel = $printArr[$lenArr - 1]['level'];
        $prevItemLevel = $printArr[$lenArr - 2]['level'];
        $name = $printArr[$lenArr - 1]['name'];
        if($currItemLevel  == 0) {
            if ($prevItemLevel == 1) {
                
            }
            else if($prevItemLevel == 2) {
                $strHtml .= $ulCl . $liCl . $ulCl . $liCl . $liOp . $name . $liCl;
            }
        }
        else if ($currItemLevel == 1) {

        }
        else if ($currItemLevel == 2) {

        }

        $strHtml .= $ulCl;
        //echo $strHtml;

        function printHtml($arr, $str) {
            $liOp = "<li>";
            $liCl = "</li>";
            $ulOp = "<ul>";
            $ulCl = "</ul>";
            
            $GLOBALS['strHtml'] = $str;
            if(count($arr) == 1) {
                $GLOBALS['strHtml'] += $ulOp . $arr['name'] . $ulCl;
            }
        }

        
    }
?>

