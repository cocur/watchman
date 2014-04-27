<?php

namespace Cocur\Watchman;

use \Mockery as m;

/**
 * WatchmanTest
 *
 * @group unit
 */
class WatchmanTest extends \PHPUnit_Framework_TestCase
{
    /** @var Watchman */
    private $watchman;

    public function setUp()
    {
        $this->watchman = new Watchman();
    }

    /**
     * @test
     * @covers Cocur\Watchman\Watchman::setBinary()
     * @covers Cocur\Watchman\Watchman::getBinary()
     */
    public function setBinaryAndGetBinary()
    {
        $this->watchman->setBinary('/usr/bin/watchman');
        $this->assertEquals('/usr/bin/watchman', $this->watchman->getBinary());
    }

    /**
     * @test
     * @covers Cocur\Watchman\Watchman::__construct()
     * @covers Cocur\Watchman\Watchman::getProcessFactory()
     */
    public function getProcessFactory()
    {
        $this->assertInstanceOf('Cocur\Watchman\Process\ProcessFactory', $this->watchman->getProcessFactory());
    }

    /**
     * @test
     * @covers Cocur\Watchman\Watchman::setProcessFactory()
     * @covers Cocur\Watchman\Watchman::getProcessFactory()
     */
    public function setProcessFactoryAndGetProcessFactory()
    {
        $factory = m::mock('Cocur\Watchman\Process\ProcessFactory');

        $this->watchman->setProcessFactory($factory);
        $this->assertEquals($factory, $this->watchman->getProcessFactory());
    }

    /**
     * @test
     * @covers Cocur\Watchman\Watchman::watch()
     * @covers Cocur\Watchman\Watchman::runProcess()
     */
    public function watchIsSuccessful()
    {
        $process = $this->getProcessMock();
        $process->shouldReceive('run')->once();
        $process->shouldReceive('stop')->once();
        $process->shouldReceive('isSuccessful')->once()->andReturn(true);

        $factory = $this->getProcessFactoryMock();
        $factory->shouldReceive('create')->with('watchman watch /var/www/foo')->once()->andReturn($process);

        $this->watchman->setProcessFactory($factory);
        $this->assertTrue($this->watchman->watch('/var/www/foo'));
    }

    /**
     * @test
     * @covers Cocur\Watchman\Watchman::watch()
     * @covers Cocur\Watchman\Watchman::runProcess()
     * @expectedException \RuntimeException
     */
    public function watchReturnsError()
    {
        $process = $this->getProcessMock();
        $process->shouldReceive('run')->once();
        $process->shouldReceive('stop')->once();
        $process->shouldReceive('getErrorOutput')->once();
        $process->shouldReceive('isSuccessful')->once()->andReturn(false);

        $factory = $this->getProcessFactoryMock();
        $factory->shouldReceive('create')->with('watchman watch /var/www/foo')->once()->andReturn($process);

        $this->watchman->setProcessFactory($factory);
        $this->watchman->watch('/var/www/foo');
    }

    /**
     * @test
     * @covers Cocur\Watchman\Watchman::addTrigger()
     * @covers Cocur\Watchman\Watchman::runProcess()
     */
    public function addTriggerIsSuccessful()
    {
        $process = $this->getProcessMock();
        $process->shouldReceive('run')->once();
        $process->shouldReceive('stop')->once();
        $process->shouldReceive('isSuccessful')->once()->andReturn(true);

        $factory = $this->getProcessFactoryMock();
        $factory
            ->shouldReceive('create')
            ->with('watchman -- trigger /var/www/foo foobar *.js -- ls -al')
            ->once()
            ->andReturn($process);

        $this->watchman->setProcessFactory($factory);
        $this->assertTrue($this->watchman->addTrigger('/var/www/foo', 'foobar', '*.js', 'ls -al'));
    }

    /**
     * @return Cocur\Watchman\Process\ProcessFactory
     */
    protected function getProcessFactoryMock()
    {
        return m::mock('Cocur\Watchman\Process\ProcessFactory');
    }

    /**
     * @return Symfony\Component\Process\Process
     */
    protected function getProcessMock()
    {
        return m::mock('Symfony\Component\Process\Process');
    }
}
