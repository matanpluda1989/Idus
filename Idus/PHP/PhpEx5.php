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

    function orderArr($arr, $parent) {
        for ($i = 0; $i < count($arr); $i++) { 
            if($parent == $arr[$i]['parent_id']) {
                echo "<ul><li>" . $arr[$i]['name'];
                $arr[$i]['parent_id'] = -1;
                orderArr($arr, $arr[$i]['id']);
            }  
        }
        echo "</li></ul>";
    }

    //Main function
    function main($api_url) {

        //Input the json from the url, convert it to array and sort it by the 'position' field
        $inputArr = file_get_contents($api_url);
        $inputArr = json_decode($inputArr, true);
        $inputArr = sortByField($inputArr, 'position');

        orderArr($inputArr, 0);
    }

?>

