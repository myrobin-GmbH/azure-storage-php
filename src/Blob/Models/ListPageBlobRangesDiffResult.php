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

namespace AzureOSS\Storage\Blob\Models;

use AzureOSS\Storage\Blob\Internal\BlobResources as Resources;
use AzureOSS\Storage\Common\Internal\Utilities;
use AzureOSS\Storage\Common\Models\RangeDiff;

/**
 * Holds result of calling listPageBlobRangesDiff wrapper
 *
 * @see      https://github.com/azure/azure-storage-php
 */
class ListPageBlobRangesDiffResult extends ListPageBlobRangesResult
{
    /**
     * Creates ListPageBlobRangesDiffResult object from $parsed response in array representation
     *
     * @param array $headers HTTP response headers
     * @param array $parsed  parsed response in array format.
     *
     * @internal
     *
     * @return ListPageBlobRangesDiffResult
     */
    public static function create(array $headers, array $parsed = null)
    {
        $result = new ListPageBlobRangesDiffResult();
        $headers = array_change_key_case($headers);

        $date = $headers[Resources::LAST_MODIFIED];
        $date = Utilities::rfc1123ToDateTime($date);
        $blobLength = (int) ($headers[Resources::X_MS_BLOB_CONTENT_LENGTH]);

        $result->setContentLength($blobLength);
        $result->setLastModified($date);
        $result->setETag($headers[Resources::ETAG]);

        if (null === $parsed) {
            return $result;
        }

        $parsed = array_change_key_case($parsed);

        $rawRanges = [];
        if (!empty($parsed[strtolower(Resources::XTAG_PAGE_RANGE)])) {
            $rawRanges = Utilities::getArray($parsed[strtolower(Resources::XTAG_PAGE_RANGE)]);
        }

        $pageRanges = [];
        foreach ($rawRanges as $value) {
            $pageRanges[] = new RangeDiff(
                (int) ($value[Resources::XTAG_RANGE_START]),
                (int) ($value[Resources::XTAG_RANGE_END])
            );
        }

        $rawRanges = [];
        if (!empty($parsed[strtolower(Resources::XTAG_CLEAR_RANGE)])) {
            $rawRanges = Utilities::getArray($parsed[strtolower(Resources::XTAG_CLEAR_RANGE)]);
        }

        foreach ($rawRanges as $value) {
            $pageRanges[] = new RangeDiff(
                (int) ($value[Resources::XTAG_RANGE_START]),
                (int) ($value[Resources::XTAG_RANGE_END]),
                true
            );
        }

        $result->setRanges($pageRanges);
        return $result;
    }
}