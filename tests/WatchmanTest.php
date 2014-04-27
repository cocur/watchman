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
        $process->shouldReceive('getOutput')->once()->andReturn($this->getFixtures('watch-success.json'));

        $factory = $this->getProcessFactoryMock();
        $factory->shouldReceive('create')->with('watchman watch /var/www/foo')->once()->andReturn($process);

        $this->watchman->setProcessFactory($factory);
        $this->assertEquals('/var/www/foo', $this->watchman->watch('/var/www/foo'));
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
     * @covers Cocur\Watchman\Watchman::watchList()
     * @covers Cocur\Watchman\Watchman::runProcess()
     */
    public function watchListIsSuccessful()
    {
        $process = $this->getProcessMock();
        $process->shouldReceive('run')->once();
        $process->shouldReceive('stop')->once();
        $process->shouldReceive('isSuccessful')->once()->andReturn(true);
        $process->shouldReceive('getOutput')->once()->andReturn($this->getFixtures('watch-list-success.json'));

        $factory = $this->getProcessFactoryMock();
        $factory->shouldReceive('create')->with('watchman watch-list')->once()->andReturn($process);

        $this->watchman->setProcessFactory($factory);
        $this->assertEquals('/var/www/foo', $this->watchman->watchList()[0]);
    }

    /**
     * @test
     * @covers Cocur\Watchman\Watchman::watchDelete()
     * @covers Cocur\Watchman\Watchman::runProcess()
     */
    public function watchDeleteIsSuccessful()
    {
        $process = $this->getProcessMock();
        $process->shouldReceive('run')->once();
        $process->shouldReceive('stop')->once();
        $process->shouldReceive('isSuccessful')->once()->andReturn(true);
        $process->shouldReceive('getOutput')->once()->andReturn($this->getFixtures('watch-del-success.json'));

        $factory = $this->getProcessFactoryMock();
        $factory->shouldReceive('create')->with('watchman watch-del /var/www/foo')->once()->andReturn($process);

        $this->watchman->setProcessFactory($factory);
        $this->assertTrue($this->watchman->watchDelete('/var/www/foo'));
    }

    /**
     * @test
     * @covers Cocur\Watchman\Watchman::watchDelete()
     * @covers Cocur\Watchman\Watchman::runProcess()
     */
    public function watchDeleteCausesError()
    {
        $process = $this->getProcessMock();
        $process->shouldReceive('run')->once();
        $process->shouldReceive('stop')->once();
        $process->shouldReceive('isSuccessful')->once()->andReturn(true);
        $process->shouldReceive('getOutput')->once()->andReturn($this->getFixtures('watch-del-error.json'));

        $factory = $this->getProcessFactoryMock();
        $factory->shouldReceive('create')->with('watchman watch-del /var/www/foo')->once()->andReturn($process);

        $this->watchman->setProcessFactory($factory);

        try {
            $this->watchman->watchDelete('/var/www/foo');
            $this->assertTrue(false);
        } catch (\RuntimeException $e) {
            $this->assertTrue(true);
            $this->assertEquals(
                'unable to resolve root /var/www/foo/: directory /var/www/foo is not watched',
                $e->getMessage()
            );
        }
    }

    /**
     * @test
     * @covers Cocur\Watchman\Watchman::trigger()
     * @covers Cocur\Watchman\Watchman::runProcess()
     */
    public function triggerIsSuccessful()
    {
        $process = $this->getProcessMock();
        $process->shouldReceive('run')->once();
        $process->shouldReceive('stop')->once();
        $process->shouldReceive('isSuccessful')->once()->andReturn(true);
        $process->shouldReceive('getOutput')->once()->andReturn($this->getFixtures('trigger-success.json'));

        $factory = $this->getProcessFactoryMock();
        $factory
            ->shouldReceive('create')
            ->with('watchman -- trigger /var/www/foo foobar *.js -- ls -al')
            ->once()
            ->andReturn($process);

        $this->watchman->setProcessFactory($factory);
        $this->assertEquals('foobar', $this->watchman->trigger('/var/www/foo', 'foobar', '*.js', 'ls -al'));
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

    protected function getFixtures($name)
    {
        return file_get_contents(sprintf('%s/fixtures/%s', __DIR__, $name));
    }
}
