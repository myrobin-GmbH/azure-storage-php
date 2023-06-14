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

namespace AzureOSS\Storage\Queue\Models;

use AzureOSS\Storage\Common\Models\AccessPolicy;
use AzureOSS\Storage\Queue\Internal\QueueResources;

/**
 * Holds access policy elements
 *
 * @see      https://github.com/azure/azure-storage-php
 */
class QueueAccessPolicy extends AccessPolicy
{
    /**
     * Get the valid permissions for the given resource.
     *
     * @return array
     */
    public static function getResourceValidPermissions()
    {
        return QueueResources::ACCESS_PERMISSIONS[
            QueueResources::RESOURCE_TYPE_QUEUE
        ];
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct(QueueResources::RESOURCE_TYPE_QUEUE);
    }
}