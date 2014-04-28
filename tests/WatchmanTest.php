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
     * @covers Cocur\Watchman\Watchman::addWatch()
     * @covers Cocur\Watchman\Watchman::runProcess()
     */
    public function addWatchIsSuccessful()
    {
        $process = $this->getProcessMock();
        $process->shouldReceive('run')->once();
        $process->shouldReceive('stop')->once();
        $process->shouldReceive('isSuccessful')->once()->andReturn(true);
        $process->shouldReceive('getOutput')->once()->andReturn($this->getFixtures('watch-success.json'));

        $factory = $this->getProcessFactoryMock();
        $factory->shouldReceive('create')->with('watchman watch /var/www/foo')->once()->andReturn($process);

        $this->watchman->setProcessFactory($factory);
        $this->assertEquals('/var/www/foo', $this->watchman->addWatch('/var/www/foo')->getRoot());
    }

    /**
     * @test
     * @covers Cocur\Watchman\Watchman::addWatch()
     * @covers Cocur\Watchman\Watchman::runProcess()
     * @expectedException \RuntimeException
     */
    public function addWatchReturnsError()
    {
        $process = $this->getProcessMock();
        $process->shouldReceive('run')->once();
        $process->shouldReceive('stop')->once();
        $process->shouldReceive('getErrorOutput')->once();
        $process->shouldReceive('isSuccessful')->once()->andReturn(false);

        $factory = $this->getProcessFactoryMock();
        $factory->shouldReceive('create')->with('watchman watch /var/www/foo')->once()->andReturn($process);

        $this->watchman->setProcessFactory($factory);
        $this->watchman->addWatch('/var/www/foo');
    }

    /**
     * @test
     * @covers Cocur\Watchman\Watchman::listWatches()
     * @covers Cocur\Watchman\Watchman::runProcess()
     */
    public function listWatchesIsSuccessful()
    {
        $process = $this->getProcessMock();
        $process->shouldReceive('run')->once();
        $process->shouldReceive('stop')->once();
        $process->shouldReceive('isSuccessful')->once()->andReturn(true);
        $process->shouldReceive('getOutput')->once()->andReturn($this->getFixtures('watch-list-success.json'));

        $factory = $this->getProcessFactoryMock();
        $factory->shouldReceive('create')->with('watchman watch-list')->once()->andReturn($process);

        $this->watchman->setProcessFactory($factory);
        $this->assertEquals('/var/www/foo', $this->watchman->listWatches()[0]->getRoot());
    }

    /**
     * @test
     * @covers Cocur\Watchman\Watchman::deleteWatch()
     * @covers Cocur\Watchman\Watchman::runProcess()
     */
    public function deleteWatchIsSuccessful()
    {
        $process = $this->getProcessMock();
        $process->shouldReceive('run')->once();
        $process->shouldReceive('stop')->once();
        $process->shouldReceive('isSuccessful')->once()->andReturn(true);
        $process->shouldReceive('getOutput')->once()->andReturn($this->getFixtures('watch-del-success.json'));

        $factory = $this->getProcessFactoryMock();
        $factory->shouldReceive('create')->with('watchman watch-del /var/www/foo')->once()->andReturn($process);

        $this->watchman->setProcessFactory($factory);
        $this->assertTrue($this->watchman->deleteWatch('/var/www/foo'));
    }

    /**
     * @test
     * @covers Cocur\Watchman\Watchman::deleteWatch()
     * @covers Cocur\Watchman\Watchman::runProcess()
     */
    public function deleteWatchCausesError()
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
            $this->watchman->deleteWatch('/var/www/foo');
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
     * @covers Cocur\Watchman\Watchman::addTrigger()
     * @covers Cocur\Watchman\Watchman::runProcess()
     */
    public function addTriggerIsSuccessful()
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
        $this->assertEquals(
            'foobar',
            $this->watchman->addTrigger('/var/www/foo', 'foobar', '*.js', 'ls -al')->getName()
        );
    }

    /**
     * @test
     * @covers Cocur\Watchman\Watchman::addTrigger()
     * @covers Cocur\Watchman\Watchman::runProcess()
     */
    public function addTriggerWithWatchObjectIsSuccessful()
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

        $watch = m::mock('Cocur\Watchman\Watch');
        $watch->shouldReceive('getRoot')->once()->andReturn('/var/www/foo');

        $this->watchman->setProcessFactory($factory);
        $this->assertEquals(
            'foobar',
            $this->watchman->addTrigger($watch, 'foobar', '*.js', 'ls -al')->getName()
        );
    }

    /**
     * @test
     * @covers Cocur\Watchman\Watchman::listTriggers()
     * @covers Cocur\Watchman\Watchman::runProcess()
     */
    public function listTriggersIsSuccessful()
    {
        $process = $this->getProcessMock();
        $process->shouldReceive('run')->once();
        $process->shouldReceive('stop')->once();
        $process->shouldReceive('isSuccessful')->once()->andReturn(true);
        $process->shouldReceive('getOutput')->once()->andReturn($this->getFixtures('trigger-list-success.json'));

        $factory = $this->getProcessFactoryMock();
        $factory->shouldReceive('create')->with('watchman trigger-list /var/www/foo')->once()->andReturn($process);

        $this->watchman->setProcessFactory($factory);
        $trigger = $this->watchman->listTriggers('/var/www/foo')[0];
        $this->assertEquals('jsfiles', $trigger->getName());
        $this->assertEquals(['ls', '-al'], $trigger->getData()['command']);
    }

    /**
     * @test
     * @covers Cocur\Watchman\Watchman::listTriggers()
     * @covers Cocur\Watchman\Watchman::runProcess()
     */
    public function listTriggersWithWatchObjectIsSuccessful()
    {
        $process = $this->getProcessMock();
        $process->shouldReceive('run')->once();
        $process->shouldReceive('stop')->once();
        $process->shouldReceive('isSuccessful')->once()->andReturn(true);
        $process->shouldReceive('getOutput')->once()->andReturn($this->getFixtures('trigger-list-success.json'));

        $factory = $this->getProcessFactoryMock();
        $factory->shouldReceive('create')->with('watchman trigger-list /var/www/foo')->once()->andReturn($process);

        $watch = m::mock('Cocur\Watchman\Watch');
        $watch->shouldReceive('getRoot')->once()->andReturn('/var/www/foo');

        $this->watchman->setProcessFactory($factory);
        $trigger = $this->watchman->listTriggers($watch)[0];

        $this->assertEquals('jsfiles', $trigger->getName());
        $this->assertEquals(['ls', '-al'], $trigger->getData()['command']);
    }

    /**
     * @test
     * @covers Cocur\Watchman\Watchman::deleteTrigger()
     * @covers Cocur\Watchman\Watchman::runProcess()
     */
    public function deleteTriggerIsSuccessful()
    {
        $process = $this->getProcessMock();
        $process->shouldReceive('run')->once();
        $process->shouldReceive('stop')->once();
        $process->shouldReceive('isSuccessful')->once()->andReturn(true);
        $process->shouldReceive('getOutput')->once()->andReturn($this->getFixtures('trigger-del-success.json'));

        $factory = $this->getProcessFactoryMock();
        $factory
            ->shouldReceive('create')
            ->with('watchman trigger-del /var/www/foo jsfiles')
            ->once()
            ->andReturn($process);

        $this->watchman->setProcessFactory($factory);
        $this->assertTrue($this->watchman->deleteTrigger('/var/www/foo', 'jsfiles'));
    }

    /**
     * @test
     * @covers Cocur\Watchman\Watchman::deleteTrigger()
     * @covers Cocur\Watchman\Watchman::runProcess()
     */
    public function deleteTriggerWithWatchObjectIsSuccessful()
    {
        $process = $this->getProcessMock();
        $process->shouldReceive('run')->once();
        $process->shouldReceive('stop')->once();
        $process->shouldReceive('isSuccessful')->once()->andReturn(true);
        $process->shouldReceive('getOutput')->once()->andReturn($this->getFixtures('trigger-del-success.json'));

        $factory = $this->getProcessFactoryMock();
        $factory
            ->shouldReceive('create')
            ->with('watchman trigger-del /var/www/foo jsfiles')
            ->once()
            ->andReturn($process);

        $watch = m::mock('Cocur\Watchman\Watch');
        $watch->shouldReceive('getRoot')->once()->andReturn('/var/www/foo');

        $this->watchman->setProcessFactory($factory);
        $this->assertTrue($this->watchman->deleteTrigger($watch, 'jsfiles'));
    }

    /**
     * @test
     * @covers Cocur\Watchman\Watchman::shutdownServer()
     * @covers Cocur\Watchman\Watchman::runProcess()
     */
    public function shutdownServerIsSuccessful()
    {
        $process = $this->getProcessMock();
        $process->shouldReceive('run')->once();
        $process->shouldReceive('stop')->once();
        $process->shouldReceive('isSuccessful')->once()->andReturn(true);
        $process->shouldReceive('getOutput')->once()->andReturn(null);

        $factory = $this->getProcessFactoryMock();
        $factory->shouldReceive('create')->with('watchman shutdown-server')->once()->andReturn($process);

        $this->watchman->setProcessFactory($factory);
        $this->watchman->shutdownServer();
    }

    /**
     * @test
     * @covers Cocur\Watchman\Watchman::getSockname()
     * @covers Cocur\Watchman\Watchman::runProcess()
     */
    public function getSocknameIsSuccessful()
    {
        $process = $this->getProcessMock();
        $process->shouldReceive('run')->once();
        $process->shouldReceive('stop')->once();
        $process->shouldReceive('isSuccessful')->once()->andReturn(true);
        $process->shouldReceive('getOutput')->once()->andReturn($this->getFixtures('get-sockname-success.json'));

        $factory = $this->getProcessFactoryMock();
        $factory->shouldReceive('create')->with('watchman get-sockname')->once()->andReturn($process);

        $this->watchman->setProcessFactory($factory);
        $this->assertEquals('/var/folders/.watchman.florian', $this->watchman->getSockname());
    }

    /**
     * @test
     * @covers Cocur\Watchman\Watchman::getClock()
     * @covers Cocur\Watchman\Watchman::runProcess()
     */
    public function getClockIsSuccessful()
    {
        $process = $this->getProcessMock();
        $process->shouldReceive('run')->once();
        $process->shouldReceive('stop')->once();
        $process->shouldReceive('isSuccessful')->once()->andReturn(true);
        $process->shouldReceive('getOutput')->once()->andReturn($this->getFixtures('clock-success.json'));

        $factory = $this->getProcessFactoryMock();
        $factory->shouldReceive('create')->with('watchman clock /var/www/foo')->once()->andReturn($process);

        $this->watchman->setProcessFactory($factory);
        $this->assertEquals('c:1398642060:75924:1:12', $this->watchman->getClock('/var/www/foo'));
    }

    /**
     * @test
     * @covers Cocur\Watchman\Watchman::getClock()
     * @covers Cocur\Watchman\Watchman::runProcess()
     */
    public function getClockWithWatchIsSuccessful()
    {
        $process = $this->getProcessMock();
        $process->shouldReceive('run')->once();
        $process->shouldReceive('stop')->once();
        $process->shouldReceive('isSuccessful')->once()->andReturn(true);
        $process->shouldReceive('getOutput')->once()->andReturn($this->getFixtures('clock-success.json'));

        $factory = $this->getProcessFactoryMock();
        $factory->shouldReceive('create')->with('watchman clock /var/www/foo')->once()->andReturn($process);

        $watch = m::mock('Cocur\Watchman\Watch');
        $watch->shouldReceive('getRoot')->once()->andReturn('/var/www/foo');

        $this->watchman->setProcessFactory($factory);
        $this->assertEquals('c:1398642060:75924:1:12', $this->watchman->getClock($watch));
    }

    /**
     * @test
     * @covers Cocur\Watchman\Watchman::find()
     * @covers Cocur\Watchman\Watchman::runProcess()
     */
    public function findIsSuccessful()
    {
        $process = $this->getProcessMock();
        $process->shouldReceive('run')->once();
        $process->shouldReceive('stop')->once();
        $process->shouldReceive('isSuccessful')->once()->andReturn(true);
        $process->shouldReceive('getOutput')->once()->andReturn($this->getFixtures('find-success.json'));

        $factory = $this->getProcessFactoryMock();
        $factory->shouldReceive('create')->with('watchman find /var/www/foo *.scss')->once()->andReturn($process);

        $this->watchman->setProcessFactory($factory);
        $this->assertEquals('sass/main.scss', $this->watchman->find('/var/www/foo', '*.scss')[0]['name']);
    }

    /**
     * @test
     * @covers Cocur\Watchman\Watchman::find()
     * @covers Cocur\Watchman\Watchman::runProcess()
     */
    public function findWithWatchObjectIsSuccessful()
    {
        $process = $this->getProcessMock();
        $process->shouldReceive('run')->once();
        $process->shouldReceive('stop')->once();
        $process->shouldReceive('isSuccessful')->once()->andReturn(true);
        $process->shouldReceive('getOutput')->once()->andReturn($this->getFixtures('find-success.json'));

        $factory = $this->getProcessFactoryMock();
        $factory->shouldReceive('create')->with('watchman find /var/www/foo *.scss')->once()->andReturn($process);

        $watch = m::mock('Cocur\Watchman\Watch');
        $watch->shouldReceive('getRoot')->once()->andReturn('/var/www/foo');

        $this->watchman->setProcessFactory($factory);
        $this->assertEquals('sass/main.scss', $this->watchman->find($watch, '*.scss')[0]['name']);
    }

    /**
     * @test
     * @covers Cocur\Watchman\Watchman::log()
     * @covers Cocur\Watchman\Watchman::runProcess()
     */
    public function logIsSuccessful()
    {
        $process = $this->getProcessMock();
        $process->shouldReceive('run')->once();
        $process->shouldReceive('stop')->once();
        $process->shouldReceive('isSuccessful')->once()->andReturn(true);
        $process->shouldReceive('getOutput')->once()->andReturn($this->getFixtures('log-success.json'));

        $factory = $this->getProcessFactoryMock();
        $factory->shouldReceive('create')->with('watchman log debug "Foobar"')->once()->andReturn($process);

        $this->watchman->setProcessFactory($factory);
        $this->assertTrue($this->watchman->log('debug', 'Foobar'));
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
