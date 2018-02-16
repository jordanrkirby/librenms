<?php
/**
 * CollaborationSensor.php
 *
 * Collaboration Sensors
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

namespace LibreNMS\Device;

use LibreNMS\OS;

class CollaborationSensor extends Sensor
{
    protected static $name = 'Collaboration Sensor';
    protected static $table = 'collaboration_sensors';
    protected static $data_name = 'collaboration-sensor';

    private $collaboration_server_ip;

    /**
     * Sensor constructor. Create a new sensor to be discovered.
     *
     * @param string $type Class of this sensor, must be a supported class
     * @param int $device_id the device_id of the device that owns this sensor
     * @param array|string $oids an array or single oid that contains the data for this sensor
     * @param string $subtype the type of sensor an additional identifier to separate out sensors of the same class, generally this is the os name
     * @param int|string $index the index of this sensor, must be stable, generally the index of the oid
     * @param string $description A user visible description of this sensor, may be truncated in some places (like graphs)
     * @param int|float $current The current value of this sensor, will seed the db and may be used to guess limits
     * @param int $multiplier a number to multiply the value(s) by
     * @param int $divisor a number to divide the value(s) by
     * @param string $aggregator an operation to combine multiple numbers. Supported: sum, avg
     * @param int $collaboration_server_id The id of the Collaboration Server in the collaboration sensor this belongs to (generally used for clusters)
     * @param int|float $high_limit Alerting: Maximum value
     * @param int|float $low_limit Alerting: Minimum value
     * @param int|float $high_warn Alerting: High warning value
     * @param int|float $low_warn Alerting: Low warning value
     * @param int|float $entPhysicalIndex The entPhysicalIndex this sensor is associated, often a port
     * @param int|float $entPhysicalMeasured the table to look for the entPhysicalIndex, for example 'ports' (maybe unused)
     */
    public function __construct(
        $type,
        $device_id,
        $oids,
        $subtype,
        $index,
        $description,
        $current = null,
        $multiplier = 1,
        $divisor = 1,
        $aggregator = 'sum',
        $collaboration_server_id = null,
        $high_limit = null,
        $low_limit = null,
        $high_warn = null,
        $low_warn = null,
        $entPhysicalIndex = null,
        $entPhysicalMeasured = null
    ) {
        $this->collaboration_server_ip = $collaboration_server_id;
        parent::__construct(
            $type,
            $device_id,
            $oids,
            $subtype,
            $index,
            $description,
            $current,
            $multiplier,
            $divisor,
            $aggregator,
            $high_limit,
            $low_limit,
            $high_warn,
            $low_warn,
            $entPhysicalIndex,
            $entPhysicalMeasured
        );
    }

    protected function toArray()
    {
        $sensor = parent::toArray();
        $sensor['collaboration_server_id'] = $this->collaboration_server_ip;
        return $sensor;
    }

    public static function runDiscovery(OS $os)
    {
        foreach (self::getTypes() as $type => $descr) {
            static::discoverType($os, $type);
        }
    }

    /**
     * Return a list of valid types with metadata about each type
     * $class => array(
     *  'short' - short text for this class
     *  'long'  - long text for this class
     *  'unit'  - units used by this class 'dBm' for example
     *  'icon'  - font awesome icon used by this class
     * )
     * @param bool $valid filter this list by valid types in the database
     * @param int $device_id when filtering, only return types valid for this device_id
     * @return array
     */
    public static function getTypes($valid = false, $device_id = null)
    {
        // Add new types here
        // FIXME I'm really bad with icons, someone please help!
        static $types = array(            
            'ccmRegisteredPhones' => array(
                'short' => 'Handsets',
                'long' => 'Registered Handsets',
                'unit' => '',
                'icon' => 'tablet',
            ),
        );

        if ($valid) {
            $sql = 'SELECT `sensor_class` FROM `collaboration_sensors`';
            $params = array();
            if (isset($device_id)) {
                $sql .= ' WHERE `device_id`=?';
                $params[] = $device_id;
            }
            $sql .= ' GROUP BY `sensor_class`';

            $sensors = dbFetchColumn($sql, $params);
            return array_intersect_key($types, array_flip($sensors));
        }

        return $types;
    }

    protected static function getDiscoveryInterface($type)
    {
        return str_to_class($type, 'LibreNMS\\Interfaces\\Discovery\\Sensors\\Collaboration') . 'Discovery';
    }

    protected static function getDiscoveryMethod($type)
    {
        return 'discoverCollaboration' . str_to_class($type);
    }

    protected static function getPollingInterface($type)
    {
        return str_to_class($type, 'LibreNMS\\Interfaces\\Polling\\Sensors\\Collaboration') . 'Polling';
    }

    protected static function getPollingMethod($type)
    {
        return 'pollCollaboration' . str_to_class($type);
    }

    
}
