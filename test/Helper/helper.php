<?php

namespace Pzn\BelajarPhpMvc\App {

    function header(string $value){
        echo $value;
    }
}

namespace Pzn\BelajarPhpMvc\Service {

    function setcookie(string $name, string $value){
        echo "$name: $value";
    }
}