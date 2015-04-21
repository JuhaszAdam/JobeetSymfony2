<?php

namespace jobeet\MyBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use jobeet\MyBundle\Entity\Job;

class JobeetCleanupCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('ens:jobeet:cleanup')
            ->setDescription('Cleanup Jobeet database')
            ->addArgument('days', InputArgument::OPTIONAL, 'The email', 90);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $days = $input->getArgument('days');

        $em = $this->getContainer()->get('doctrine')->getManager();

        // cleanup Lucene index
        $index = Job::getLuceneIndex();

        $q = $em->getRepository('MyBundle:Job')->createQueryBuilder('j')
            ->where('j.expires_at < :date')
            ->setParameter('date', date('Y-m-d'))
            ->getQuery();

        $jobs = $q->getResult();
        foreach ($jobs as $job) {
            if ($hit = $index->find('pk:' . $job->getId())) {
                $index->delete($hit->id);
            }
        }

        $index->optimize();

        $output->writeln('Cleaned up and optimized the job index');

        // Remove stale jobs
        $nb = $em->getRepository('MyBundle:Job')->cleanup($days);

        $output->writeln(sprintf('Removed %d stale jobs', $nb));
    }
}
