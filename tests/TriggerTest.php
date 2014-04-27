<?php

namespace Cocur\Watchman;

use \Mockery as m;

/**
 * TriggerTest
 *
 * @group unit
 */
class TriggerTest extends \PHPUnit_Framework_TestCase
{
    /** @var Cocur\Watchman\Watch */
    private $watch;

    /** @var string */
    private $name;

    /** @var Trigger */
    private $trigger;

    /** @var array */
    private $data;

    public function setUp()
    {
        $this->watch = m::mock('Cocur\Watchman\Watch');
        $this->name = 'foobar';
        $this->data = ['name' => 'jsfiles', 'command' => ['ls', '-al']];
        $this->trigger = new Trigger($this->watch, $this->name, $this->data);
    }

    /**
     * @test
     * @covers Cocur\Watchman\Trigger::__construct()
     * @covers Cocur\Watchman\Trigger::getWatch()
     */
    public function getWatchReturnsWatch()
    {
        $this->assertEquals($this->watch, $this->trigger->getWatch());
    }

    /**
     * @test
     * @covers Cocur\Watchman\Trigger::__construct()
     * @covers Cocur\Watchman\Trigger::getName()
     */
    public function getNameReturnsName()
    {
        $this->assertEquals($this->name, $this->trigger->getName());
    }

    /**
     * @test
     * @covers Cocur\Watchman\Trigger::__construct()
     * @covers Cocur\Watchman\Trigger::getData()
     */
    public function getData()
    {
        $this->assertEquals($this->data, $this->trigger->getData());
    }

    /**
     * @test
     * @covers Cocur\Watchman\Trigger::delete()
     */
    public function deleteDeletesTrigger()
    {
        $this->watch->shouldReceive('deleteTrigger')->with('foobar')->once()->andReturn(true);

        $this->assertTrue($this->trigger->delete());
    }
}
