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

namespace AzureOSS\Storage\Tests\Functional\Queue;

use AzureOSS\Storage\Common\Internal\Resources;
use AzureOSS\Storage\Common\Models\CORS;
use AzureOSS\Storage\Common\Models\Logging;
use AzureOSS\Storage\Common\Models\Metrics;
use AzureOSS\Storage\Common\Models\RetentionPolicy;
use AzureOSS\Storage\Common\Models\ServiceProperties;
use AzureOSS\Storage\Queue\Models\CreateMessageOptions;
use AzureOSS\Storage\Queue\Models\CreateQueueOptions;
use AzureOSS\Storage\Queue\Models\ListQueuesOptions;
use AzureOSS\Storage\Tests\Framework\TestResources;

class QueueServiceFunctionalTestData
{
    //Needs to keep this value as low as possible to quicken the test
    //but if the test machine is slow, a small value will cause unexpected
    //failures. Default value: 20.
    public const INTERESTING_TTL = 20;
    public static $testUniqueId;
    public static $tempQueueCounter;
    public static $nonExistQueuePrefix;
    public static $testQueueNames;

    public static function setupData()
    {
        $rint = mt_rand(0, 1000000);
        self::$testUniqueId = 'qa-' . $rint . '-';
        self::$nonExistQueuePrefix = 'qa-' . ($rint + 1) . '-';
        self::$testQueueNames = [
            self::$testUniqueId . 'a1',
            self::$testUniqueId . 'a2',
            self::$testUniqueId . 'b1',
        ];
        self::$tempQueueCounter = 0;
    }

    public static function getInterestingQueueName()
    {
        return self::$testUniqueId . 'int-' . (self::$tempQueueCounter++);
    }

    public static function getSimpleMessageText()
    {
        return 'simple message text #' . (self::$tempQueueCounter++);
    }

    public static function getInterestingTimeoutValues()
    {
        $ret = [];
        array_push($ret, null);
        array_push($ret, -1);
        array_push($ret, 0);
        array_push($ret, 1);
        array_push($ret, -2147483648);
        array_push($ret, 2147483647);
        return $ret;
    }

    public static function getDefaultServiceProperties()
    {
        // This is the default that comes from the server.
        $rp = new RetentionPolicy();
        $l = new Logging();
        $l->setRetentionPolicy($rp);
        $l->setVersion('1.0');
        $l->setDelete(false);
        $l->setRead(false);
        $l->setWrite(false);

        $m = new Metrics();
        $m->setRetentionPolicy($rp);
        $m->setVersion('1.0');
        $m->setEnabled(false);
        $m->setIncludeAPIs(null);

        $sp = new ServiceProperties();
        $sp->setLogging($l);
        $sp->setHourMetrics($m);

        return $sp;
    }

    public static function getInterestingServiceProperties()
    {
        $ret = [];

        // This is the default that comes from the server.
        array_push($ret, self::getDefaultServiceProperties());

        $rp = new RetentionPolicy();
        $rp->setEnabled(true);
        $rp->setDays(10);

        $l = new Logging();
        $l->setRetentionPolicy($rp);
        // Note: looks like only v1.0 is available now.
        // http://msdn.microsoft.com/en-us/library/windowsazure/hh360996.aspx
        $l->setVersion('1.0');
        $l->setDelete(true);
        $l->setRead(true);
        $l->setWrite(true);

        $m = new Metrics();
        $m->setRetentionPolicy($rp);
        $m->setVersion('1.0');
        $m->setEnabled(true);
        $m->setIncludeAPIs(true);

        $c = CORS::create(TestResources::getCORSSingle());

        $sp = new ServiceProperties();
        $sp->setLogging($l);
        $sp->setHourMetrics($m);
        $sp->setCorses([$c]);

        array_push($ret, $sp);

        $rp = new RetentionPolicy();
        // The service does not accept setting days when enabled is false.
        $rp->setEnabled(false);
        $rp->setDays(null);

        $l = new Logging();
        $l->setRetentionPolicy($rp);
        // Note: looks like only v1.0 is available now.
        // http://msdn.microsoft.com/en-us/library/windowsazure/hh360996.aspx
        $l->setVersion('1.0');
        $l->setDelete(false);
        $l->setRead(false);
        $l->setWrite(false);

        $m = new Metrics();
        $m->setRetentionPolicy($rp);
        $m->setVersion('1.0');
        $m->setEnabled(true);
        $m->setIncludeAPIs(true);

        $csArray =
            TestResources::getServicePropertiesSample()[Resources::XTAG_CORS];
        $c0 = CORS::create($csArray[Resources::XTAG_CORS_RULE][0]);
        $c1 = CORS::create($csArray[Resources::XTAG_CORS_RULE][1]);

        $sp = new ServiceProperties();
        $sp->setLogging($l);
        $sp->setHourMetrics($m);
        $sp->setCorses([$c0, $c1]);

        array_push($ret, $sp);

        $rp = new RetentionPolicy();
        $rp->setEnabled(true);
        // Days has to be 0 < days <= 365
        $rp->setDays(364);

        $l = new Logging();
        $l->setRetentionPolicy($rp);
        // Note: looks like only v1.0 is available now.
        // http://msdn.microsoft.com/en-us/library/windowsazure/hh360996.aspx
        $l->setVersion('1.0');
        $l->setDelete(false);
        $l->setRead(false);
        $l->setWrite(false);

        $m = new Metrics();
        $m->setVersion('1.0');
        $m->setEnabled(false);
        $m->setIncludeAPIs(null);
        $m->setRetentionPolicy($rp);

        $csArray =
            TestResources::getServicePropertiesSample()[Resources::XTAG_CORS];
        $c0 = CORS::create($csArray[Resources::XTAG_CORS_RULE][0]);
        $c1 = CORS::create($csArray[Resources::XTAG_CORS_RULE][1]);

        $sp = new ServiceProperties();
        $sp->setLogging($l);
        $sp->setHourMetrics($m);
        $sp->setCorses([$c0, $c1]);

        array_push($ret, $sp);

        return $ret;
    }

    public static function getInterestingMetadata()
    {
        $ret = self::getNiceMetadata();

        // Some metadata that HTTP will not like.
        $metadata = ['<>000' => '::::value'];
        array_push($ret, $metadata);

        return $ret;
    }

    public static function getNiceMetadata()
    {
        $ret = [];

        array_push($ret, null);

        $metadata = [];
        array_push($ret, $metadata);

        $metadata = [
            'key' => 'value',
            'foo' => 'bar',
            'baz' => 'boo'];
        array_push($ret, $metadata);

        return $ret;
    }

    public static function getInterestingCreateQueueOptions()
    {
        $ret = [];

        $options = new CreateQueueOptions();
        array_push($ret, $options);

        $options = new CreateQueueOptions();
        $options->setTimeout(10);
        array_push($ret, $options);

        $options = new CreateQueueOptions();
        $options->setTimeout(-10);
        array_push($ret, $options);

        $options = new CreateQueueOptions();
        $metadata = [];
        $metadata['foo'] = 'bar';
        $metadata['foo2'] = 'bar2';
        $metadata['foo3'] = 'bar3';
        $options->setMetadata($metadata);
        $options->setTimeout(10);
        array_push($ret, $options);

        $options = new CreateQueueOptions();
        $metadata = ['foo' => 'bar'];
        $options->setMetadata($metadata);
        $options->setTimeout(-10);
        array_push($ret, $options);

        return $ret;
    }

    public static function getSimpleCreateMessageOptions()
    {
        $ret = new CreateMessageOptions();
        $ret->setTimeout(4);
        $ret->setTimeToLiveInSeconds(1000);
        $ret->setVisibilityTimeoutInSeconds(self::INTERESTING_TTL);
        return $ret;
    }

    public static function getInterestingListQueuesOptions()
    {
        $ret = [];

        $options = new ListQueuesOptions();
        array_push($ret, $options);

        $options = new ListQueuesOptions();
        $options->setMaxResults(2);
        $options->setMaxResults('2');
        array_push($ret, $options);

        $options = new ListQueuesOptions();
        $options->setTimeout(10);
        array_push($ret, $options);

        $options = new ListQueuesOptions();
        $options->setTimeout(-10);
        array_push($ret, $options);

        $options = new ListQueuesOptions();
        $options->setPrefix(self::$nonExistQueuePrefix);
        array_push($ret, $options);

        $options = new ListQueuesOptions();
        $options->setPrefix(self::$testUniqueId);
        array_push($ret, $options);

        $options = new ListQueuesOptions();
        // Cannot set Marker to arbitrary values. Must only use if the previous request returns a NextMarker.
        //            $options->setMarker('abc');
        // So, add logic in listQueuesWorker to loop and setMarker if there is a NextMarker.
        $options->setMaxResults(2);
        $options->setPrefix(self::$testUniqueId);
        $options->setTimeout(10);
        array_push($ret, $options);

        $options = new ListQueuesOptions();
        $options->setMaxResults(3);
        $options->setPrefix(self::$testUniqueId);
        $options->setTimeout(10);
        array_push($ret, $options);

        $options = new ListQueuesOptions();
        $options->setMaxResults(4);
        $options->setPrefix(self::$testUniqueId);
        $options->setTimeout(10);
        array_push($ret, $options);

        return $ret;
    }
}
