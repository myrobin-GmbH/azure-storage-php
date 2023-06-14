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

namespace AzureOSS\Storage\File\Models;

use AzureOSS\Storage\Common\Internal\Validate;
use AzureOSS\Storage\Common\LocationMode;
use AzureOSS\Storage\Common\Models\ServiceOptions;
use AzureOSS\Storage\File\Internal\FileResources as Resources;

/**
 * File service options.
 *
 * @see      https://github.com/azure/azure-storage-php
 */
class FileServiceOptions extends ServiceOptions
{
    public function setLocationMode($locationMode)
    {
        Validate::canCastAsString($locationMode, 'locationMode');
        Validate::isTrue(
            $locationMode == LocationMode::PRIMARY_ONLY,
            Resources::FILE_LOCATION_IS_PRIMARY_ONLY
        );

        $this->locationMode = $locationMode;
    }
}