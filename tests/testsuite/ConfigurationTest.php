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

require_once('../../core/class/jeedom.class.php');

class ConfigurationTest extends TestCase
{
    public $dataStorage;

    protected function setUp()
    {
        JeedomVars::$isConnected = true;
    }

    protected function tearDown()
    {
    }

    public function testNotConnected()
    {
        JeedomVars::$isConnected = false;
        ob_start();
        include(dirname(__FILE__) . '/../plugin_info/configuration.php');
        ob_get_clean();
        // Le fichier exécute die(), cette partie ne doit normalement pas être exécutée
        $this->assertTrue(false);

    }

    public function testWithUserConnectedOnDIY()
    {
        jeedom::$hardwareName = 'DIY';
        ob_start();
        include(dirname(__FILE__) . '/../plugin_info/configuration.php');
        $content = ob_get_clean();
        $actions = MockedActions::get();
        $this->assertCount(1, $actions);
        $this->assertEquals('include_file', $actions[0]['action']);
        $this->assertEquals('authentification', $actions[0]['content']['name']);
        $this->assertContains('<form', $content);
        $this->assertContains('"minify"', $content);
        $this->assertNotContains('raspberry-config-file', $content);
    }

    public function testWithUserConnectedOnRaspberry()
    {
        jeedom::$hardwareName = 'RPi';
        ob_start();
        include(dirname(__FILE__) . '/../plugin_info/configuration.php');
        $content = ob_get_clean();
        $actions = MockedActions::get();
        $this->assertCount(1, $actions);
        $this->assertEquals('include_file', $actions[0]['action']);
        $this->assertEquals('authentification', $actions[0]['content']['name']);
        $this->assertContains('<form', $content);
        $this->assertContains('"minify"', $content);
        $this->assertContains('raspberry-config-file', $content);
    }
}
