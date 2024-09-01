<?php

function makeStdclass(array $values): \StdClass
{
    $result = new \StdClass();
    foreach ($values as $key => $value) {
        $result->$key = $value;
    }
    return $result;
}