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

/**
 * Batch parameter names.
 *
 * @see      https://github.com/azure/azure-storage-php
 */
class BatchOperationParameterName
{
    public const BP_TABLE = 'table';
    public const BP_ENTITY = 'entity';
    public const BP_PARTITION_KEY = 'PartitionKey';
    public const BP_ROW_KEY = 'RowKey';
    public const BP_ETAG = 'etag';

    /**
     * Validates if $paramName is already defined.
     *
     * @param string $paramName The batch operation parameter name.
     *
     * @internal
     *
     * @return bool
     */
    public static function isValid($paramName)
    {
        switch ($paramName) {
            case self::BP_TABLE:
            case self::BP_ENTITY:
            case self::BP_PARTITION_KEY:
            case self::BP_ROW_KEY:
            case self::BP_ETAG:
                return true;
            default:
                return false;
        }
    }
}