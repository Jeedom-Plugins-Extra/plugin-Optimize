<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;

require_once('core/class/OptimizeRPi.class.php');
require_once('vendor/autoload.php');

class OptimizeRPiTest extends TestCase
{
    private $optRpi = null;

    private $configFileUrl = null;

    protected function setUp()
    {
        $this->optRpi = new OptimizeRPi();
        vfsStream::setup('boot');
        $this->configFileUrl = vfsStream::url('boot/config.txt');
        file_put_contents($this->configFileUrl, "a_param=a_value\n");

        config::$byKeyPluginData = array(
            'Optimize' => array('raspberry-config-file' => $this->configFileUrl)
        );
    }

    protected function tearDown()
    {
        unset($this->optRpi);
    }

    public function testIsRaspberryPi()
    {
        jeedom::$hardwareName = 'RPi';
        $this->assertTrue($this->optRpi->isRaspberryPi());
        jeedom::$hardwareName = 'Raspberry';
        $this->assertTrue($this->optRpi->isRaspberryPi());
        jeedom::$hardwareName = 'Raspberry Pi';
        $this->assertTrue($this->optRpi->isRaspberryPi());
        jeedom::$hardwareName = 'DIY';
        $this->assertFalse($this->optRpi->isRaspberryPi());
        jeedom::$hardwareName = 'Linux';
        $this->assertFalse($this->optRpi->isRaspberryPi());
        jeedom::$hardwareName = 'Other';
        $this->assertFalse($this->optRpi->isRaspberryPi());
    }

    public function testGetRatingWithGpuMemBest()
    {
        file_put_contents($this->configFileUrl, "gpu_mem=16\n", FILE_APPEND);
        $this->optRpi->canParseSystemConfigFile();
        $result = $this->optRpi->getRating();
        $this->assertEquals('ok', $result['gpu_mem']);
    }

    public function testGetRatingWithGpuMemBad()
    {
        file_put_contents($this->configFileUrl, "gpu_mem=4\n", FILE_APPEND);
        $this->optRpi->canParseSystemConfigFile();
        $result = $this->optRpi->getRating();
        $this->assertEquals('warn', $result['gpu_mem']);
    }

    public function testGetRatingWithGpuMemNoData()
    {
        $this->optRpi->canParseSystemConfigFile();
        $result = $this->optRpi->getRating();
        $this->assertEquals('warn', $result['gpu_mem']);
    }

    public function testGetRatingWithL2CacheBest()
    {
        file_put_contents($this->configFileUrl, "disable_l2cache=0\n", FILE_APPEND);
        $this->optRpi->canParseSystemConfigFile();
        $result = $this->optRpi->getRating();
        $this->assertEquals('ok', $result['l2_cache']);
    }

    public function testGetRatingWithL2CacheBad()
    {
        file_put_contents($this->configFileUrl, "disable_l2cache=1\n", FILE_APPEND);
        $this->optRpi->canParseSystemConfigFile();
        $result = $this->optRpi->getRating();
        $this->assertEquals('warn', $result['l2_cache']);
    }

    public function testGetRatingWithL2CacheNoData()
    {
        $this->optRpi->canParseSystemConfigFile();
        $result = $this->optRpi->getRating();
        $this->assertEquals('warn', $result['l2_cache']);
    }

    public function testGetRatingWithBothBest()
    {
        file_put_contents($this->configFileUrl, "gpu_mem=16\n", FILE_APPEND);
        file_put_contents($this->configFileUrl, "disable_l2cache=0\n", FILE_APPEND);
        $this->optRpi->canParseSystemConfigFile();
        $result = $this->optRpi->getRating();
        $this->assertEquals('ok', $result['l2_cache']);
        $this->assertEquals('ok', $result['gpu_mem']);
    }

    public function testGetRatingWithBothBad()
    {
        file_put_contents($this->configFileUrl, "gpu_mem=2\n", FILE_APPEND);
        file_put_contents($this->configFileUrl, "disable_l2cache=1\n", FILE_APPEND);
        $this->optRpi->canParseSystemConfigFile();
        $result = $this->optRpi->getRating();
        $this->assertEquals('warn', $result['l2_cache']);
        $this->assertEquals('warn', $result['gpu_mem']);
    }

    public function testGetRatingWithOnlyOneGood()
    {
        file_put_contents($this->configFileUrl, "gpu_mem=16\n", FILE_APPEND);
        file_put_contents($this->configFileUrl, "disable_l2cache=1\n", FILE_APPEND);
        $this->optRpi->canParseSystemConfigFile();
        $result = $this->optRpi->getRating();
        $this->assertEquals('ok', $result['gpu_mem']);
        $this->assertEquals('warn', $result['l2_cache']);
    }

    public function testGetRatingWithoutData()
    {
        $this->optRpi->canParseSystemConfigFile();
        $result = $this->optRpi->getRating();
        $this->assertEquals('warn', $result['gpu_mem']);
        $this->assertEquals('warn', $result['l2_cache']);
    }
}
