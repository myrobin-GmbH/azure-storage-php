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

namespace AzureOSS\Storage\Table\Internal;

use AzureOSS\Storage\Common\Internal\Utilities;
use AzureOSS\Storage\Table\Internal\TableResources as Resources;

/**
 * Reads and writes MIME for batch API.
 *
 * @ignore
 *
 * @see      https://github.com/azure/azure-storage-php
 */
class MimeReaderWriter implements IMimeReaderWriter
{
    /**
     * Given array of MIME parts in raw string, this function converts them into MIME
     * representation.
     *
     * @param array $bodyPartContents The MIME body parts.
     *
     * @return array Returns array with two elements 'headers' and 'body' which
     *               represents the MIME message.
     */
    public function encodeMimeMultipart(array $bodyPartContents)
    {
        $count = count($bodyPartContents);
        $mimeType = Resources::MULTIPART_MIXED_TYPE;
        $batchGuid = Utilities::getGuid();
        $batchId = sprintf('batch_%s', $batchGuid);
        $contentType1 = ['content_type' => "$mimeType"];
        $changeSetGuid = Utilities::getGuid();
        $changeSetId = sprintf('changeset_%s', $changeSetGuid);
        $contentType2 = ['content_type' => "$mimeType; boundary=$changeSetId"];
        $options = [
            'encoding' => 'binary',
            'content_type' => Resources::HTTP_TYPE,
        ];

        $eof = "\r\n";

        $result = [];
        $result['body'] = Resources::EMPTY_STRING;
        $result['headers'] = [];

        $batchBody = &$result['body'];
        $batchHeaders = &$result['headers'];

        $batchHeaders['Content-Type'] = $mimeType . "; boundary=\"$batchId\"";

        $batchBody .= '--' . $batchId . $eof;
        $batchBody .= "Content-Type: $mimeType; boundary=\"$changeSetId\"" . $eof;

        $batchBody .= $eof;
        for ($i = 0; $i < count($bodyPartContents); ++$i) {
            $batchBody .= '--' . $changeSetId . $eof;

            $batchBody .= 'Content-Transfer-Encoding: binary' . $eof;
            $batchBody .= 'Content-Type: ' . Resources::HTTP_TYPE . $eof;

            $batchBody .= $eof . $bodyPartContents[$i] . $eof;
        }
        $batchBody .= '--' . $changeSetId . '--' . $eof;
        $batchBody .= $eof;
        $batchBody .= '--' . $batchId . '--' . $eof;

        return $result;
    }

    /**
     * Parses given mime HTTP response body into array. Each array element
     * represents a change set result.
     *
     * @param string $mimeBody The raw MIME body result.
     *
     * @return array
     */
    public function decodeMimeMultipart($mimeBody)
    {
        // Find boundary
        $boundaryRegex = '~boundary=(changesetresponse_.*)~';
        preg_match($boundaryRegex, $mimeBody, $matches);

        $boundary = trim($matches[1]);

        // Split the requests
        $requests = explode('--' . $boundary, $mimeBody);

        // Get the body of each request
        $result = [];

        // The first and last element are not request
        for ($i = 1; $i < count($requests) - 1; ++$i) {
            // Split the request header and body
            preg_match("/^.*?\r?\n\r?\n(.*)/s", $requests[$i], $matches);
            $result[] = $matches[1];
        }

        return $result;
    }
}