<?php

namespace ShepardBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ShepardBundle\Entity\Job;

class JobeetCleanupCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('ens:jobeet:cleanup')
            ->setDescription('Cleanup Jobeet database')
            ->addArgument('days', InputArgument::OPTIONAL, 'The email', 90);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $days = $input->getArgument('days');
        $em = $this->getContainer()->get('doctrine')->getManager();

        /** @var EntityManager $em $q */
        $q = $em->getRepository('ShepardBundle:Job')->createQueryBuilder('j')
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
        $nb = $em->getRepository('ShepardBundle:Job')->cleanup($days);
        $output->writeln(sprintf('Removed %d stale jobs', $nb));
    }
}
