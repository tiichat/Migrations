<?php
namespace Tiichat\Migrations\Test\TestCase\Shell\Task;

use Tiichat\Migrations\Shell\Task\TiiMigrationTask;
use Cake\TestSuite\TestCase;
use Cake\View\View;

class TiiMigrationTaskTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->Task = new TiiMigrationTask();
    }

    /**
     * @test
     */
    public function name()
    {
        $result = $this->Task->name();
        $this->assertEquals('tii_migration', $result);
    }
}
