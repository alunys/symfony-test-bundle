<?php

namespace Alunys\SymfonyTestBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class PhpUnitCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('alunys:symfony-test-bundle:phpunit')
            ->setDescription('CLaunche php unit')
            ->setHelp('This command allows you to launch phpunit...')
            ->addArgument('phpunit_arguments', InputArgument::OPTIONAL, 'PhpUnit arguments');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $kernel = $this->getApplication()->getKernel();
        $process = new Process($kernel->getVendorDir() . '/bin/phpunit ' . $input->getArgument('phpunit_arguments'));
        $process->run(function ($type, $buffer) use ($output) {
            $output->write($buffer);
        });
    }
}
