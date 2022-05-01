<?php
$old = array(
    "name" => "Bruno",
    "lastname" => "Garcia",
    "age" => 300, //changed
    "address" => array(
        "street" => "moon",
        "number" => 1000, //changed
        "zip" => "9999999999"
    ),
    "one" => array(
        "two_one" => array(
            "third_one" => "no",
            "third_two" => "no", //changed
        ),
        "two_two" => "no"
    ),
    "x" => array(
        "y1" => array(
            "z1" => "no",
            "z2" => "no",
        ),
        "y2" => "no" //changed
    ),
    "any" => "any"
);

$new = array(
    "name" => "Bruno",
    "lastname" => "Garcia",
    "age" => 28, //changed
    "address" => array(
        "street" => "moon",
        "number" => 50, //changed
        "zip" => "9999999999"
    ),
    "one" => array(
        "two_one" => array(
            "third_one" => "no",
            "third_two" => "yes", //changed
        ),
        "two_two" => "no"
    ),
    "x" => array(
        "y1" => array(
            "z1" => "no",
            "z2" => "no"
        ),
        "y2" => "yes" //changed
    ),
    "any" => "any"
);

function array_changed($old, $new)
{
    $out = array();
    try {
        $loop = function ($old, $new) use (&$loop) {
            if (count($old) === count($new)) {
                $final = array();
                foreach ($old as $k => $v) {
                    if (!is_array($v) && $v != $new[$k]) {
                        $final[$k] = $new[$k];
                    }
                    if (is_array($v)) {
                        $child = $loop($v, $new[$k]);
                        if (count($child)) $final[$k] = $child;
                    }
                }
                return $final;
            } else {
                throw new Exception(__LINE__ . " - all arrays must be the same size.");
            }
        };

        if (is_array($new) && is_array($old)) {
            if (count($old)) {
                if (count($new)) {
                    $out = $loop($old, $new);
                    return $out;
                }
                if (!count($new)) throw new Exception(__LINE__ . " - argument 2(new) is empty");
            }
            if (!count($old)) throw new Exception(__LINE__ . " - argument 1(old) is empty");
        } else {
            throw new Exception(__LINE__ . " - Argument 1(old) and argument 2(new) needs to be array.");
        }
    } catch (Exception $e) {
        echo "Exception: ".$e->getMessage();
    }
}

print_r(array_changed($old, $new));
