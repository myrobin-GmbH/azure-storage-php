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
use AzureOSS\Storage\Common\Internal\Validate;
use AzureOSS\Storage\Common\LocationMode;

/**
 * Provides functionality and data structure for continuation token.
 *
 * @see      https://github.com/azure/azure-storage-php
 */
class ContinuationToken
{
    private $location;

    public function __construct(
        $location = ''
    ) {
        $this->setLocation($location);
    }

    /**
     * Setter for location
     *
     * @param string $location the location to be set.
     */
    public function setLocation($location)
    {
        Validate::canCastAsString($location, 'location');
        Validate::isTrue(
            $location == LocationMode::PRIMARY_ONLY
            || $location == LocationMode::SECONDARY_ONLY
            || $location == '',
            sprintf(
                Resources::INVALID_VALUE_MSG,
                'location',
                LocationMode::PRIMARY_ONLY . ' or ' . LocationMode::SECONDARY_ONLY
            )
        );

        $this->location = $location;
    }

    /**
     * Getter for location
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }
}