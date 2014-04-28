<?php

namespace Cocur\Watchman;

use \Mockery as m;

/**
 * WatchTest
 *
 * @group unit
 */
class WatchTest extends \PHPUnit_Framework_TestCase
{
    /** @var Cocur\Watchman\Watchman */
    private $watchman;

    /** @var string */
    private $root;

    /** @var Watch */
    private $watch;

    public function setUp()
    {
        $this->watchman = m::mock('Cocur\Watchman\Watchman');
        $this->root = '/var/www/foo';
        $this->watch = new Watch($this->watchman, $this->root);
    }

    /**
     * @test
     * @covers Cocur\Watchman\Watch::__construct()
     * @covers Cocur\Watchman\Watch::getWatchman()
     */
    public function getWatchman()
    {
        $this->assertEquals($this->watchman, $this->watch->getWatchman());
    }

    /**
     * @test
     * @covers Cocur\Watchman\Watch::__construct()
     * @covers Cocur\Watchman\Watch::getRoot()
     */
    public function getRoot()
    {
        $this->assertEquals($this->root, $this->watch->getRoot());
    }

    /**
     * @test
     * @covers Cocur\Watchman\Watch::delete()
     */
    public function deleteDeletesTheWatch()
    {
        $this->watchman->shouldReceive('deleteWatch')->with('/var/www/foo')->once()->andReturn(true);

        $this->assertTrue($this->watch->delete());
    }

    /**
     * @test
     * @covers Cocur\Watchman\Watch::addTrigger()
     */
    public function addTriggerAddsTriggerToWatch()
    {
        $this->watchman
            ->shouldReceive('addTrigger')
            ->with($this->root, 'foobar', '*.js', 'ls -al')
            ->once()
            ->andReturn('foobar');

        $this->assertEquals('foobar', $this->watch->addTrigger('foobar', '*.js', 'ls -al'));
    }

    /**
     * @test
     * @covers Cocur\Watchman\Watch::deleteTrigger()
     */
    public function deleteTriggerDeletesTriggerFromWatch()
    {
        $this->watchman->shouldReceive('deleteTrigger')->with($this->root, 'foobar')->once()->andReturn(true);

        $this->assertTrue($this->watch->deleteTrigger('foobar'));
    }

    /**
     * @test
     * @covers Cocur\Watchman\Watch::listTriggers()
     */
    public function listTriggersListTriggersFromWatch()
    {
        $this->watchman
            ->shouldReceive('listTriggers')
            ->with($this->root)
            ->once()
            ->andReturn([[ 'name' => 'jsfiles' ]]);

        $this->assertEquals('jsfiles', $this->watch->listTriggers()[0]['name']);
    }

    /**
     * @test
     * @covers Cocur\Watchman\Watch::getClock()
     */
    public function getClockReturnsClock()
    {
        $this->watchman->shouldReceive('getClock')->with($this->root)->once();

        $this->watch->getClock();
    }

    /**
     * @test
     * @covers Cocur\Watchman\Watch::find()
     */
    public function findFindsFilesThatMatchPattern()
    {
        $this->watchman->shouldReceive('find')->with($this->root, '*.scss')->once();

        $this->watch->find('*.scss');
    }
}
