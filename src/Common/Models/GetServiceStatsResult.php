<?php

/**
 * LICENSE: The MIT License (the "License")
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * https://github.com/azure/azure-storage-php/LICENSE
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * PHP version 5
 *
 * @see      https://github.com/azure/azure-storage-php
 */

namespace AzureOSS\Storage\Common\Models;

use AzureOSS\Storage\Common\Internal\Resources;
use AzureOSS\Storage\Common\Internal\Utilities;

/**
 * Result from calling get service stats REST wrapper.
 *
 * @see      https://github.com/azure/azure-storage-php
 */
class GetServiceStatsResult
{
    private $status;
    private $lastSyncTime;

    /**
     * Creates object from $parsedResponse.
     *
     * @internal
     *
     * @param array $parsedResponse XML response parsed into array.
     *
     * @return \AzureOSS\Storage\Common\Models\GetServiceStatsResult
     */
    public static function create(array $parsedResponse)
    {
        $result = new GetServiceStatsResult();
        if (Utilities::arrayKeyExistsInsensitive(
            Resources::XTAG_GEO_REPLICATION,
            $parsedResponse
        )) {
            $geoReplication = $parsedResponse[Resources::XTAG_GEO_REPLICATION];
            if (Utilities::arrayKeyExistsInsensitive(
                Resources::XTAG_STATUS,
                $geoReplication
            )) {
                $result->setStatus($geoReplication[Resources::XTAG_STATUS]);
            }

            if (Utilities::arrayKeyExistsInsensitive(
                Resources::XTAG_LAST_SYNC_TIME,
                $geoReplication
            )) {
                $lastSyncTime = $geoReplication[Resources::XTAG_LAST_SYNC_TIME];
                $result->setLastSyncTime(Utilities::convertToDateTime($lastSyncTime));
            }
        }

        return $result;
    }

    /**
     * Gets status of the result.
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Gets the last sync time.
     *
     * @return \DateTime
     */
    public function getLastSyncTime()
    {
        return $this->lastSyncTime;
    }

    /**
     * Sets status of the result.
     */
    protected function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Sets the last sync time.
     */
    protected function setLastSyncTime(\DateTime $lastSyncTime)
    {
        $this->lastSyncTime = $lastSyncTime;
    }
}