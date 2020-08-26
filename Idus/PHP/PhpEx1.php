<!DOCTYPE html>
<html>
    <head>
        <title>PHP Excercise</title>
    </head>
    <body>
        <!--
        <ul>
            <li>
                About us
                <ul>
                    <li>
                        Departments
                        <ul>
                            <li>Sales</li>
                            <li>Development</li>
                            <li>Support</li>
                        </ul>
                    </li>
                    <li>
                        Managment
                        <ul>
                            <li>CEO</li>
                            <li>CTO</li>
                        </ul>
                    </li>
                </ul>
            </li>
            <li>
                Services
                <ul>
                    <li>
                        Design
                        <ul>
                            <li>UX</li>
                            <li>UI</li>
                            <li>Graphic Design</li>
                        </ul>
                    </li>
                    <li>
                        Development
                        <ul>
                            <li>Frontend</li>
                            <li>Backend</li>
                            <li>Fullstack</li>
                        </ul>
                    </li>
                    <li>
                        Maintenance
                        <ul>
                            <li>Webmaster</li>
                            <li>Technical Support</li>
                        </ul>
                    </li>
                </ul>
            </li>
            <li>Contact us</li>
        </ul>
        -->
        <?php
            $api_url = "https://api.idus.dev/interview";
            $json = file_get_contents($api_url);
            $json = json_decode($json, true);

            $arrPar = array();
            $arrSon = array();
            $arrGrandSon = array();          

            foreach($json as $item) {
                $parId = $item['parent_id'];
                if($parId == 0)
                    array_push($arrPar, $item);
                else if($parId < 4)
                    array_push($arrSon, $item);
                //else if($parId == 2)
                    //array_push($arrSon2, $item);
                else
                    array_push($arrGrandSon, $item);
            }
            $arr1 = array();

            /*echo "par: </br>";
            print_r($arrPar);
            echo "</br></br> par sort: </br>";*/
            $arrPar = sortByPos($arrPar);
            //print_r($arrPar);

            /*echo "</br></br></br> son: </br>";
            print_r($arrSon);
            echo "</br></br> son sort: </br>";*/
            $arrSon = sortByPos($arrSon);
            //print_r($arrSon);

            /*echo "</br></br></br> son2: </br>";
            print_r($arrSon2);
            echo "</br></br> son2 sort: </br>";
            print_r(sortByPos($arrSon2));*/

            /*echo "</br></br></br> arrGrandSon: </br>";
            print_r($arrGrandSon);
            echo "</br></br> arrGrandSon sort: </br>";*/
            $arrGrandSon = sortByPos($arrGrandSon);
            //print_r($arrGrandSon);
            echo "</br></br>";


            $arrRes = array();
            $tempArr = array();
            $tempArr2 = array();

            $pos = 0; 
            $lenArrGra = count($arrGrandSon);
                
            for($i = 0; $i < $lenArrGra - 1; $i++) {
                if($i + 2 == $lenArrGra)
                    array_push($tempArr, $arrGrandSon[$i]); 
                if($arrGrandSon[$i]['parent_id'] == $arrGrandSon[$i + 1]['parent_id']) {
                    array_push($tempArr, $arrGrandSon[$i]); 
                }
                else {
                    if($arrGrandSon[$i]['parent_id'] == $arrGrandSon[$i - 1]['parent_id'])
                        array_push($tempArr, $arrGrandSon[$i]);    
                    $tempArr2 = array_slice($arrSon, $pos, 1, true);
                    array_push($tempArr2, $tempArr);             
                    array_push($arrRes, $tempArr2);
                    $pos++; 
                    $tempArr = array();
                    $tempArr2 = array();
                }
                
            }

            $tempArr2 = array_slice($arrSon, $pos, 1, true);
            array_push($tempArr2, $tempArr);
            $pos++;       
            array_push($arrRes, $tempArr2);

            print_r($arrRes);
            echo "</br></br>";


            $arrRes2 = $arrRes;
            $arrRes = array();
            $tempArr = array();
            $tempArr2 = array();

            $pos = 0; 
            $lenArrSon = count($arrRes2);

            for($i = 0; $i < $lenArrSon; $i++) {
                
                //print_r($arrRes2[$i]);
                echo "</br>";

                /***if($i + 2 == $lenArrSon)
                    array_push($tempArr, $arrRes2[$i]); 
                if($arrRes2[$i][0]['parent_id'] == $arrRes2[$i + 1][0]['parent_id']) {
                    array_push($tempArr, $arrRes2[$i]); 
                }
                else {
                    if($arrRes2[$i][0]['parent_id'] == $arrRes2[$i - 1][0]['parent_id'])
                        array_push($tempArr, $arrRes2[$i]);    
                    $tempArr2 = array_slice($arrPar, $pos, 1, true);
                    array_push($tempArr2, $tempArr);             
                    array_push($arrRes, $tempArr2);
                    $pos++; 
                    $tempArr = array();
                    $tempArr2 = array();
                }
                ***/
            }
            
            echo "</br></br>";
            //print_r($arrRes);

            /*while( (pow($base, $exp) + $sub) == $arrGrandSon[i]['position']) {
                array_push($tempArr, $arrGrandSon[i]);
                $i++;
                if( (pow($base, $exp) + $sub) != $arrGrandSon[i]['position']) {

                }
            }

            echo "<ul>";
            foreach($arrPar as $itemPar) {
                echo "<li>" . $itemPar['name'];
                foreach($arrSon as $itemSon) {
                    //echo "<ul>" . $itemSon['name'] . "</ul>";
                }              
                echo "</li>";

            }
            echo "</ul>";
            */

            function sortByPos($arr) {
                $posisions = array();
                foreach($arr as $pos => $item) {
                    $posisions[$pos] = $item['position'];
                }

                array_multisort($posisions, SORT_ASC, $arr);

                return $arr;              
            }

        ?>
    </body>
</html>

