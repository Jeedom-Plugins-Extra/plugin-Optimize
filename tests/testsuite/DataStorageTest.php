<?php

use PHPUnit\Framework\TestCase;

require_once('../../core/class/DB.class.php');
require_once('core/class/DataStorage.class.php');

class DataStorageTest extends TestCase
{
    public $dataStorage;

    private $testTableName = 'test_DB';

    private $realTableName;

    protected function setUp()
    {
        DB::init();
        $this->dataStorage = new DataStorage($this->testTableName);
        $this->realTableName = 'data_' . $this->testTableName;
    }

    protected function tearDown()
    {
        MockedActions::clear();
    }

    public function testIsDataTableExistsWithEmptyDatabase()
    {
        DB::setAnswer(null);
        $this->assertFalse($this->dataStorage->isDataTableExists());
        $actions = MockedActions::get();
        $this->assertCount(1, $actions);
        $this->assertEquals('query_execute', $actions[0]['action']);
        $this->assertEquals('SHOW TABLES LIKE ?', $actions[0]['content']['query']);
        $this->assertEquals(array($this->realTableName), $actions[0]['content']['data']);
    }

    public function testIsDataTableExistsWithCreatedTable()
    {
        DB::setAnswer(array('Tables_in_jeedom (data_test_DB)' => 'data_test_DB'));
        $this->assertTrue($this->dataStorage->isDataTableExists());
        $actions = MockedActions::get();
        $this->assertCount(1, $actions);
        $this->assertEquals('query_execute', $actions[0]['action']);
        $this->assertEquals('SHOW TABLES LIKE ?', $actions[0]['content']['query']);
        $this->assertEquals(array($this->realTableName), $actions[0]['content']['data']);
    }

    public function testCreateDataTableWithEmptyDatabase()
    {
        $this->dataStorage->createDataTable();
        $actions = MockedActions::get();
        $this->assertCount(2, $actions);
        $this->assertEquals('query_execute', $actions[0]['action']);
        $this->assertEquals('query_execute', $actions[1]['action']);
        $this->assertContains('CREATE TABLE', $actions[1]['content']['query']);
        $this->assertContains($this->realTableName, $actions[1]['content']['query']);
    }

    public function testCreateDataTableWithCreatedTable()
    {
        DB::setAnswer(array('Tables_in_jeedom (data_test_DB)' => 'data_test_DB'));
        $this->dataStorage->createDataTable();
        $actions = MockedActions::get();
        $this->assertCount(1, $actions);
        $this->assertEquals('query_execute', $actions[0]['action']);
        $this->assertContains('SHOW TABLES', $actions[0]['content']['query']);
        $this->assertEquals(array($this->realTableName), $actions[0]['content']['data']);
    }

    public function testDropDataTable()
    {
        $this->dataStorage->dropDataTable();
        $actions = MockedActions::get();
        $this->assertCount(1, $actions);
        $this->assertEquals('query_execute', $actions[0]['action']);
        $this->assertContains('DROP TABLE', $actions[0]['content']['query']);
        $this->assertContains($this->realTableName, $actions[0]['content']['query']);
    }

    public function testDeleteData()
    {
        $this->dataStorage->deleteData('test');
        $actions = MockedActions::get();
        $this->assertCount(1, $actions);
        $this->assertEquals('query_execute', $actions[0]['action']);
        $this->assertContains('DELETE FROM', $actions[0]['content']['query']);
        $this->assertContains($this->realTableName, $actions[0]['content']['query']);
        $this->assertEquals(array('test'), $actions[0]['content']['data']);
    }

    public function testGetRawDataWithoutData()
    {
        $result = $this->dataStorage->getRawData('a_code');
        $this->assertNull($result);
        $actions = MockedActions::get();
        $this->assertCount(1, $actions);
        $this->assertEquals('query_execute', $actions[0]['action']);
        $this->assertContains($this->realTableName, $actions[0]['content']['query']);
    }

    public function testGetRawDataWithData()
    {
        DB::setAnswer(array('data' => 'something'));
        $result = $this->dataStorage->getRawData('a_code');
        $this->assertEquals('something', $result);
        $actions = MockedActions::get();
        $this->assertCount(1, $actions);
        $this->assertEquals('query_execute', $actions[0]['action']);
        $this->assertContains($this->realTableName, $actions[0]['content']['query']);
    }

    public function testIsDataExistsWithoutData()
    {
        $result = $this->dataStorage->isDataExists('a_code');
        $this->assertFalse($result);
        $actions = MockedActions::get();
        $this->assertCount(1, $actions);
        $this->assertEquals('query_execute', $actions[0]['action']);
        $this->assertContains($this->realTableName, $actions[0]['content']['query']);
    }

    public function testIsDataExistsWithData()
    {
        DB::setAnswer(array('data' => 'something'));
        $result = $this->dataStorage->isDataExists('a_code');
        $this->assertTrue($result);
        $actions = MockedActions::get();
        $this->assertCount(1, $actions);
        $this->assertEquals('query_execute', $actions[0]['action']);
        $this->assertContains($this->realTableName, $actions[0]['content']['query']);
    }

    public function testAddRawData()
    {
        $this->dataStorage->addRawData('a_code', 'something');
        $actions = MockedActions::get();
        $this->assertCount(1, $actions);
        $this->assertEquals('query_execute', $actions[0]['action']);
        $this->assertContains('INSERT INTO', $actions[0]['content']['query']);
        $this->assertContains($this->realTableName, $actions[0]['content']['query']);
    }

    public function testUpdateRawData()
    {
        $this->dataStorage->updateRawData('a_code', 'something');
        $actions = MockedActions::get();
        $this->assertCount(1, $actions);
        $this->assertEquals('query_execute', $actions[0]['action']);
        $this->assertContains('UPDATE', $actions[0]['content']['query']);
        $this->assertContains($this->realTableName, $actions[0]['content']['query']);
    }

    public function testStoreRawDataWithoutData()
    {
        $this->dataStorage->storeRawData('a_code', 'something');
        $actions = MockedActions::get();
        $this->assertCount(2, $actions);
        $this->assertEquals('query_execute', $actions[0]['action']);
        $this->assertEquals('query_execute', $actions[1]['action']);
        $this->assertContains('INSERT INTO', $actions[1]['content']['query']);
        $this->assertContains($this->realTableName, $actions[1]['content']['query']);
    }

    public function testStoreRawDataWithData()
    {
        DB::setAnswer(array('data' => 'something'));
        $this->dataStorage->storeRawData('a_code', 'something');
        $actions = MockedActions::get();
        $this->assertCount(2, $actions);
        $this->assertEquals('query_execute', $actions[0]['action']);
        $this->assertEquals('query_execute', $actions[1]['action']);
        $this->assertContains('UPDATE', $actions[1]['content']['query']);
        $this->assertContains($this->realTableName, $actions[1]['content']['query']);
    }

    public function testStoreJsonData()
    {
        $this->dataStorage->storeJsonData('a_code', array('something' => 'is_that'));
        $actions = MockedActions::get();
        $this->assertCount(2, $actions);
        $this->assertEquals('query_execute', $actions[0]['action']);
        $this->assertEquals('query_execute', $actions[1]['action']);
        $this->assertContains('INSERT INTO', $actions[1]['content']['query']);
        $this->assertEquals(array('a_code', '{"something":"is_that"}'), $actions[1]['content']['data']);
    }

    public function testGetJsonData()
    {
        DB::setAnswer(array('data' => '{"something":"is_that"}'));
        $result = $this->dataStorage->getJsonData('a_code');
        $actions = MockedActions::get();
        $this->assertCount(1, $actions);
        $this->assertEquals('query_execute', $actions[0]['action']);
        $this->assertContains('SELECT', $actions[0]['content']['query']);
        $this->assertEquals(array('something' => 'is_that'), $result);
    }
}
