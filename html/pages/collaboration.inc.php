<?php
/**
 * collaboration.inc.php
 *
 * Collaboration Sensors table view
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    LibreNMS
 * @link       http://librenms.org
 * @copyright  2017 Tony Murray
 * @author     Tony Murray <murraytony@gmail.com>
 */

$pagetitle[] = "Collaboration";

use LibreNMS\Device\CollaborationSensor;

$sensors = dbFetchColumn('SELECT `sensor_class` FROM `collaboration_sensors` GROUP BY `sensor_class`');
$valid_collaboration_types = array_intersect_key(CollaborationSensor::getTypes(), array_flip($sensors));

$class = $vars['metric'];
if (!$class) {
    $class = key($valid_collaboration_types);  // get current type in array (should be the first)
}
if (!$vars['view']) {
    $vars['view'] = "nographs";
}


$link_array = array('page' => 'collaboration');

$linkoptions = '<span style="font-weight: bold;">Wireless</span> &#187; ';
$sep = '';
foreach ($valid_collaboration_types as $type => $details) {
    $linkoptions .= $sep;
    if ($class == $type) {
        $linkoptions .= '<span class="pagemenu-selected">';
    }

    $linkoptions .= generate_link($details['short'], $link_array, array('metric'=> $type, 'view' => $vars['view']));

    if ($class == $type) {
        $linkoptions .= '</span>';
    }

    $sep = ' | ';
}
unset($sep);

$displayoptions = '';
if ($vars['view'] == "graphs") {
    $displayoptions .= '<span class="pagemenu-selected">';
}
$displayoptions .= generate_link("Graphs", $link_array, array("metric"=> $class, "view" => "graphs"));
if ($vars['view'] == "graphs") {
    $displayoptions .= '</span>';
}

$displayoptions .= ' | ';

if ($vars['view'] != "graphs") {
    $displayoptions .= '<span class="pagemenu-selected">';
}

$displayoptions .= generate_link("No Graphs", $link_array, array("metric"=> $class, "view" => "nographs"));

if ($vars['view'] != "graphs") {
    $displayoptions .= '</span>';
}

if (isset($valid_collaboration_types[$class])) {
    $graph_type = 'collaboration_' . $class;
    $unit = $valid_collaboration_types[$class]['unit'];
    $pagetitle[] = "Collaboration :: ".$class;
    include $config['install_dir'] . '/html/pages/collaboration/sensors.inc.php';
} else {
    echo("No sensors of type " . $class . " found.");
}
