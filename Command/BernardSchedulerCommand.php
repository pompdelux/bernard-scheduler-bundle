<?php /* vim: set sw=4: */

namespace Pompdelux\BernardSchedulerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class BernardSchedulerCommand
 *
 * @package Pompdelux\BernardSchedulerBundle\Command
 */
class BernardSchedulerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('bernard:scheduler')
            ->addOption('interval', null, InputOption::VALUE_OPTIONAL, 'Seconds between every run in the scheduler', 10)
            ->setDescription('Scheduler job for attaching scheduler functionality to Bernard');
    }

    /**
     * executes the job
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get('pdl.bernard_scheduler.runner')->run($input->getOption('interval'));
    }
}
