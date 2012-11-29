<?php
function userGet($r, $i)
{  $userName = array("John", "Amy", "Dan", "Elizabeth", "Mike", "Wil", "Ernest", "Albert", "Sue", "Freddy",
                     "Mary", "Tom", "Paul", "Amber", "Bibi", "Boris", "Cameron", "Cesar", "Carmen", "Ben",
                     "Amadeo", "Angela", "Betty", "Benny", "Brenda", "Christian", "Celia", "Franklin", "Fiona", "Felix",
                     "Amelia", "Chelsea", "David", "Donna", "Edison", "Erika", "Ginger", "Gilbert", "Heidi", "Hans",
                     "Andy", "Bruce", "Corinna", "Evan", "Austin", "Flavio", "Gaby", "Gally", "Harold", "Isabella");

   $user = array();

   for ($ii = 0; $ii <= 50 - 1; $ii++) {
     $user[] = array("ID" => $ii + 10, "NAME" => $userName[$ii], "AGE" => rand(20, 40), "BALANCE" => rand(100, 255));
   }

   return (array(count($user), array_slice($user, $i, $r)));
}

try {
  $option = $_POST["option"];

  switch ($option) {
    case "LST": $pageSize = $_POST["pageSize"];

                $limit = isset($_POST["limit"])? $_POST["limit"] : $pageSize;
                $start = isset($_POST["start"])? $_POST["start"] : 0;

                list($userNum, $user) = userGet($limit, $start);

                //echo "{success: " . true . ", resultTotal: " . count($user) . ", resultRoot: " . G::json_encode($user) . "}";
                echo G::json_encode(array("success" => true, "resultTotal" => $userNum, "resultRoot" => $user));
                break;
  }
} catch (Exception $e) {
  echo null;
}
?>