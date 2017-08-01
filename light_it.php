<?php
/**
 * Created by PhpStorm.
 * User: Dmytro.Shumeyko
 * Date: 8/1/2017
 * Time: 1:20 PM
 */
function validateBitcoinAddress($addresses)
{
    //check one by one
    foreach ($addresses as $address) {
        $message = "Address is valid!";
        try {
            //call validate function
            validate($address);
        } catch (\Exception $e) {
            $message = $e->getMessage();
        }
        echo "$address: $message\n";
    }
}

function validate($address)
{
    //prepare address for hashing
    $decoded = decode58($address);

    //double hash
    $hash1 = hash("sha256", substr($decoded, 0, 21), true);
    $hash2 = hash("sha256", $hash1, true);

    //check address
    if (substr_compare($decoded, $hash2, 21, 4)) {
        throw new \Exception("Invalid Bitcoin address!");
    }
    return true;
}

function decode58($input)
{
    //implement all valid chars
    $alphabet = "123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz";

    $chars = array_fill(0, 25, 0);

    for ($i = 0, $n = strlen($input); $i < $n; $i++) {
        //check if chars is valid
        if (($position = strpos($alphabet, $input[$i])) === false) {
            throw new \Exception("invalid character found - '" . $input[$i] . "'");
        }
        //prepare address for hashing
        $c = $position;
        for ($j = 25; $j--;) {
            $c += (int)(58 * $chars[$j]);
            $chars[$j] = (int)($c % 256);
            $c /= 256;
            $c = (int)$c;
        }
        if ($c != 0) {
            throw new \Exception("Address is too long!");
        }
    }

    //prepare and return result
    $result = "";
    foreach ($chars as $val) {
        $result .= chr($val);
    }

    return $result;
}
//prepare array with addresses
$addresses = [
    "1AGNa15ZQXAZUgFiqJ2i7Z2DPU2J6hW62i",
    "1AGNa15ZQXAZUgFiqJ2i7Z2DPU2J6hW62I",
    "1AGNa15ZQXAZUgFiqJ2i7Z2DPU2J6hW62iToLongAddress",
    "1AGNa15ZQXAZUgFiqJ2i7Z2DPU2J6hW62Y",
];
//run script
validateBitcoinAddress($addresses);