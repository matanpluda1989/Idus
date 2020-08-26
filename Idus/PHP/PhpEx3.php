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
         * Insert all the arrays to one array by the order of rhe print
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
        echo $strHtml;

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

