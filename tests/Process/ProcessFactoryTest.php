<?php

namespace Cocur\Watchman\Process;

/**
 * ProcessFactoryTest
 *
 * @group unit
 */
class ProcessFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var ProcessFactoryTest */
    private $factory;

    public function setUp()
    {
        $this->factory = new ProcessFactory();
    }

    /**
     * @test
     * @covers Cocur\Watchman\Process\ProcessFactory::create()
     */
    public function create()
    {
        $this->assertInstanceOf('Symfony\Component\Process\Process', $this->factory->create('ls -al'));
    }
}
