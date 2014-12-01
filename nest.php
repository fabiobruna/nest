<?php

require_once('./nest-api/nest.class.php');

// Your Nest username and password.
include('config.php');

// The timezone you're in.
// See http://php.net/manual/en/timezones.php for the possible values.
date_default_timezone_set('Europe/Amsterdam');

//Capture date and time of script execution
$runTime = $date = date('Y-m-d H:i:s');

//Zipcode where Nest is located; Will automate in future version
$runLoc = "2498DB";

// Here's how to use this class:

$nest = new Nest($username, $password);

//Used to return current outside temperature and away status
//Uncomment out the three lines below to view what getUserLocations returns [JSON Format]
$locations = $nest->getUserLocations();
//echo '<pre>';
print_r ($locations);
//echo '</pre>';

// echo "Devices list (thermostats):\n";
$devices_serials = $nest->getDevices();
// jlog($devices_serials);

//Used to return current inside temperature, current inside humidity, current mode, target temperature, time to target temperature, current heat state, current ac state
//Uncomment out the three lines below to view what getDeviceInfo returns [JSON Format]
$infos = $nest->getDeviceInfo();
//echo '<pre>';
//print_r ($infos);
//echo '</pre>';
// echo "----------\n\n";

if ($db = new SQLite3('nest.db')) {
    $q = @$db->query($query);
} else {
    die($err);
}

/* Helper functions */

function json_format($json) {
    $tab = "  ";
    $new_json = "";
    $indent_level = 0;
    $in_string = false;

    $json_obj = json_decode($json);

    if($json_obj === false)
        return false;

    $json = json_encode($json_obj);
    $len = strlen($json);

    for($c = 0; $c < $len; $c++)
    {
        $char = $json[$c];
        switch($char)
        {
            case '{':
            case '[':
                if(!$in_string)
                {
                    $new_json .= $char . "\n" . str_repeat($tab, $indent_level+1);
                    $indent_level++;
                }
                else
                {
                    $new_json .= $char;
                }
                break;
            case '}':
            case ']':
                if(!$in_string)
                {
                    $indent_level--;
                    $new_json .= "\n" . str_repeat($tab, $indent_level) . $char;
                }
                else
                {
                    $new_json .= $char;
                }
                break;
            case ',':
                if(!$in_string)
                {
                    $new_json .= ",\n" . str_repeat($tab, $indent_level);
                }
                else
                {
                    $new_json .= $char;
                }
                break;
            case ':':
                if(!$in_string)
                {
                    $new_json .= ": ";
                }
                else
                {
                    $new_json .= $char;
                }
                break;
            case '"':
                if($c > 0 && $json[$c-1] != '\\')
                {
                    $in_string = !$in_string;
                }
            default:
                $new_json .= $char;
                break;
        }
    }

    return $new_json;
}

function jlog($json) {
    if (!is_string($json)) {
        $json = json_encode($json);
    }
    echo json_format($json) . "\n";
}
