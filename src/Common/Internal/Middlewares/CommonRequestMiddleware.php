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

namespace AzureOSS\Storage\Common\Internal\Middlewares;

use AzureOSS\Storage\Common\Internal\Authentication\IAuthScheme;
use AzureOSS\Storage\Common\Internal\Resources;
use AzureOSS\Storage\Common\Middlewares\MiddlewareBase;
use Psr\Http\Message\RequestInterface;

/**
 * CommonRequestMiddleware is the middleware used to add the necessary headers
 * and to sign the request with provided authentication scheme. This middleware
 * is by default applied to each of the request.
 *
 * @ignore
 *
 * @see      https://github.com/azure/azure-storage-php
 */
class CommonRequestMiddleware extends MiddlewareBase
{
    private $authenticationScheme;
    private $headers;
    private $msVersion;
    private $userAgent;

    /**
     * Creates CommonRequestMiddleware with the passed scheme and headers to
     * be added.
     *
     * @param IAuthScheme $authenticationScheme The authentication scheme.
     * @param string      $storageAPIVersion    Azure Storage Service API version,
     *                                          like '2016-05-31'.
     * @param string      $serviceSDKVersion    Like '1.0.1' or '1.2.0'.
     * @param array       $headers              The headers to be added.
     */
    public function __construct(
        IAuthScheme $authenticationScheme = null,
        $storageAPIVersion,
        $serviceSDKVersion,
        array $headers = []
    ) {
        $this->authenticationScheme = $authenticationScheme;
        $this->msVersion = $storageAPIVersion;
        $this->userAgent = self::getUserAgent($serviceSDKVersion);
        $this->headers = $headers;
    }

    /**
     * Add the provided headers, the date, then sign the request using the
     * authentication scheme, and return it.
     *
     * @param RequestInterface $request un-signed request.
     *
     * @return RequestInterface
     */
    protected function onRequest(RequestInterface $request)
    {
        $result = $request;

        //Adding headers.
        foreach ($this->headers as $key => $value) {
            $headers = $result->getHeaders();
            if (!array_key_exists($key, $headers)) {
                $result = $result->withHeader($key, $value);
            }
        }

        //rewriting version and user-agent.
        $result = $result->withHeader(
            Resources::X_MS_VERSION,
            $this->msVersion
        );
        $result = $result->withHeader(
            Resources::USER_AGENT,
            $this->userAgent
        );

        //Adding date.
        $date = gmdate(Resources::AZURE_DATE_FORMAT, time());
        $result = $result->withHeader(Resources::DATE, $date);

        //Adding client request-ID if not specified by the user.
        if (!$result->hasHeader(Resources::X_MS_CLIENT_REQUEST_ID)) {
            $result = $result->withHeader(Resources::X_MS_CLIENT_REQUEST_ID, \uniqid());
        }
        //Sign the request if authentication scheme is not null.
        return $this->authenticationScheme == null ?
            $request : $this->authenticationScheme->signRequest($result);
    }

    /**
     * Gets the user agent string used in request header.
     *
     * @return string
     */
    private static function getUserAgent($serviceSDKVersion)
    {
        // e.g. User-Agent: Azure-Storage/1.0.1-1.1.1 (PHP 5.5.32)/WINNT
        return 'Azure-Storage/' . $serviceSDKVersion . '-' .
            Resources::COMMON_SDK_VERSION .
            ' (PHP ' . PHP_VERSION . ')' . '/' . php_uname('s');
    }
}