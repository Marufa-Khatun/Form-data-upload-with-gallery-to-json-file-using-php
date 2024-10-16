<?php

if(file_exists(__DIR__."/../app/functions.php")){
    require_once __DIR__."/../app/functions.php";
}else {
    echo "The required file does not exist.";
}



?>