<?php


// Your Nest username and password.
include('config.php');

require_once($pad.'nest-api/nest.class.php');

// The timezone you're in.
// See http://php.net/manual/en/timezones.php for the possible values.
date_default_timezone_set('Europe/Amsterdam');

//Capture date and time of script execution
$runTime = $date = date('Y-m-d H:i:s');

//Zipcode where Nest is located; Will automate in future version. Nu in config.
// $runLoc = "";

// Here's how to use this class:

$nest = new Nest($username, $password);

//Used to return current outside temperature and away status
//Uncomment out the three lines below to view what getUserLocations returns [JSON Format]
$locations = $nest->getUserLocations();
// echo '<pre>';
//  print_r ($locations);
// echo '</pre>';

// weather data is retreived with postal code from nest, and does not work in NL(?). City name however does work

$weather = $nest->getWeather($runLoc);
// echo '<pre>';
//  print_r ($weather);
// echo '</pre>';

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

$query = 'INSERT INTO nest (log_datetime, location, outside_temp, outside_humidity, away_status, current_temp, current_humidity, temp_mode, target_temp, time_to_target, heat_on, ac_on) VALUES ("'.$runTime.'", "'.$runLoc.'", "'.$weather->outside_temperature.'", "'.$weather->outside_humidity.'", "'.$locations[0]->away.'", "'.$infos->current_state->temperature.'", "'.$infos->current_state->humidity.'", "'.$infos->current_state->mode.'", "'.$infos->target->temperature.'", "'.$infos->target->time_to_target.'","'.$infos->current_state->heat.'","'.$infos->current_state->ac.'")';

if ($db = new SQLite3($pad.'nest.db')) {
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
