<?php

namespace Alunys\SymfonyTestBundle\Tests\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Command\Command;

class PhpUnitCommandTest extends TestCase
{

    public function testCommand()
    {
        $application = new Application(new \AppTestKernel());
        $command = $application->find('alunys:symfony-test-bundle:phpunit');

        $this->assertInstanceOf(Command::class, $command);
    }
}
