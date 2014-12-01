<?php

require_once('./nest-api/nest.class.php');

// Your Nest username and password.
include('config.php');

// The timezone you're in.
// See http://php.net/manual/en/timezones.php for the possible values.
date_default_timezone_set('Europe/Amsterdam');

// Here's how to use this class:

$nest = new Nest($username, $password);

echo "Location information:\n";
$locations = $nest->getUserLocations();
jlog($locations);
echo "----------\n\n";

echo "Devices list (thermostats):\n";
$devices_serials = $nest->getDevices();
jlog($devices_serials);
echo "----------\n\n";

echo "Devices list (Nest Protect):\n";
$protects_serials = $nest->getDevices(DEVICE_TYPE_PROTECT);
jlog($protects_serials);
echo "----------\n\n";

echo "Device information:\n";
$infos = $nest->getDeviceInfo($devices_serials[0]);
jlog($infos);
echo "----------\n\n";

echo "Current temperature:\n";
printf("%.02f degrees %s\n", $infos->current_state->temperature, $infos->scale);
echo "----------\n\n";

sleep(1);

echo "Device information:\n";
$infos = $nest->getDeviceInfo();
jlog($infos);
echo "----------\n\n";

echo "Device schedule:\n";
// Returns as array, one element for each day of the week for which there has at least one scheduled event.
// Array keys are a textual representation of a day, three letters, as returned by `date('D')`. Array values are arrays of scheduled temperatures, including a time (in minutes after midnight), and a mode (one of the TARGET_TEMP_MODE_* defines).
$schedule = $nest->getDeviceSchedule();
jlog($schedule);
echo "----------\n\n";

echo "Device next scheduled event:\n";
$next_event = $nest->getNextScheduledEvent();
jlog($next_event);
echo "----------\n\n";

echo "Last 10 days energy report:\n";
$energy_report = $nest->getEnergyLatest();
jlog($energy_report);
echo "----------\n\n";

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
