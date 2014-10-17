<?php

namespace Ydle\HubBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Ydle\HubBundle\Command\BaseCommand as BaseCommand;
use Ydle\HubBundle\Entity\NodeData;

class NodeDataInsertCommand extends BaseCommand
{
    /**
     * Configure the console command
     */
    protected function configure()
    {
        $this
            ->setName('ydle:nodedata:insert')
            ->setDescription('Insert data for a node')
            ->addArgument('node', InputArgument::REQUIRED, 'Code of the node')
            ->addArgument('type', InputArgument::REQUIRED, 'Id of the data type')
            ->addArgument('data', InputArgument::OPTIONAL, 'data to insert')
            ->addOption('autopopulate', null, InputOption::VALUE_NONE, 'If defined, data will be created automatically for this node')
        ;
    }

    /**
     * Execute the current console command
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $autopopulate = $input->getOption('autopopulate');
        $typeId = $input->getArgument('type');
        $nodeCode = $input->getArgument('node');
        $this->em = $this->getContainer()->get('doctrine')->getManager();

        if(null === ($type = $this->getNodeTypeManager()->find($typeId))){
            $output->writeln('<error>Unknown node type</error>');
            return;
	}
        if(null === ($node = $this->getNodeManager()->findOneBy(array('code' => $nodeCode)))){
            $output->writeln('<error>Unknown node</error>');
            return;
	}
        
        if($autopopulate) {
            $startDate = 1409582400 ;
            $currDate = $startDate;
            $now = time();
            $currData = 1500;
            // Initialize first data
            switch($type->getUnit()){
                case '%':
                    $currData = mt_rand(4000,6000);
                break;
            }

            // Populate data 
            while ($currDate < $now) {
                $nodeData = new NodeData();
                $cleanDate = \DateTime::createFromFormat("U", $currDate);
        
                switch($type->getUnit()){
                    case '°C':
                        if(date("H", $currDate) == 0) { $currData = mt_rand(1450, 1650); }
                        else {
                            $currData += mt_rand(10,50);
                        }
                    break;
                    case '%':
                        if(date("H", $currDate) == 0 || $currData >= 10000) { $currData = mt_rand(4000, 6000); }
                        else { $currData += mt_rand(10, 50); }
                    break;
                }

                $nodeData->setType($type);
                $nodeData->setNode($node);
                $nodeData->setCreated($cleanDate);
                $nodeData->setData($currData);
                $this->em->persist($nodeData);
                $this->em->flush();
                $currDate += 1000;
            }
        }
    }
}
