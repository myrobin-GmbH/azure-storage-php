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

namespace AzureOSS\Storage\Table\Models;

use AzureOSS\Storage\Common\Internal\Validate;
use AzureOSS\Storage\Table\Internal\TableResources as Resources;

/**
 * Holds constant and logic for accept JSON content type.
 *
 * @see      https://github.com/azure/azure-storage-php
 */
class AcceptJSONContentType
{
    public const NO_METADATA = Resources::JSON_NO_METADATA_CONTENT_TYPE;
    public const MINIMAL_METADATA = Resources::JSON_MINIMAL_METADATA_CONTENT_TYPE;
    public const FULL_METADATA = Resources::JSON_FULL_METADATA_CONTENT_TYPE;

    public static function validateAcceptContentType($contentType)
    {
        Validate::isTrue(
            $contentType == self::NO_METADATA
            || $contentType == self::MINIMAL_METADATA
            || $contentType == self::FULL_METADATA,
            Resources::INVALID_ACCEPT_CONTENT_TYPE
        );
    }
}