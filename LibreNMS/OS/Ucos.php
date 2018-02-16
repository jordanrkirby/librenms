<?php
/**
 * Ucos.php
 *
 * Cisco Unified Communications OS
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

namespace LibreNMS\OS;

use LibreNMS\Device\CollaborationSensor;
use LibreNMS\Interfaces\Discovery\ProcessorDiscovery;
use LibreNMS\Interfaces\Discovery\Sensors\CollaborationRegistedHandsetsDiscovery;
use LibreNMS\Interfaces\Polling\Sensors\CollaborationRegisteredHandsetsPolling;

use LibreNMS\OS;

class Unifi extends OS implements
    ProcessorDiscovery,
    CollaborationRegistedHandsetsDiscovery,
    CollaborationRegisteredHandsetsPolling
{
    use OS\Traits\FrogfootResources;


    /**
     * Returns an array of LibreNMS\Device\Sensor objects that have been discovered
     *
     * @return array
     */
    public function discoverCollaborationRegisteredHandsets()
    {
        $ccmGlobal_oids = snmpwalk_cache_oid($this->getDevice(), 'ccmGlobalInfo', array(), 'CISCO-CCM-MIB');
        if (empty($ccmGlobal)) {
            return array();
        }

        $sensors = array();

        $sensors[] = new CollaborationSensor(
            'ccmRegisteredPhones',
            $this->getDeviceId(),
            '.1.3.6.1.4.1.9.9.156.1.5.5',
            'ucos',
            $index,
            "Registered Phones",
            $entry['ccmRegisteredPhones'],
            1
        );
            
        
        return $sensors;
    }

  
}
