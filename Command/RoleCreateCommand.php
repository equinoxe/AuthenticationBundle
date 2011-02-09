<?php
/**
 * This file is part of the Flexiflow package.
 *
 * @package    Core
 * @subpackage Main
 * @author     Thilo Hille<hille@equinoxe.info>
 * @copyright  Equinoxe GmbH <info@equinoxe.de>
 * @license    GPL V3
 */

namespace Equinoxe\AuthenticationBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Equinoxe\AuthenticationBundle\Entity\Role;

/**
 * Acts as daemon process and handles all active triggers.
 */
class RoleCreateCommand extends Command
{
    /**
     * Configures the command and its help.
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('flexiflow:role:create')
             ->addArgument('rolename', InputArgument::REQUIRED, 'Rolename')
        ;
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return integer 0 if everything went fine, or an error code
     *
     * @throws \LogicException When this abstract class is not implemented
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $options = $input->getArguments();
        $em = $this->container->get('doctrine.orm.entity_manager');
        try {
            if ($em->getRepository("Equinoxe\AuthenticationBundle\Entity\Role")->findOneBy(array('role' => $options['rolename']))) {
                throw new \Exception("Role " . $options['rolename'] . " already exists.");
            }
        
            $role = new Role($options['rolename']);
            $em->persist($role);
            $em->flush();
            $output->write('Role ' . $options['rolename'] . " created\n");
        } catch (\Exception $e) {
            $output->write('Role ' . $options['rolename'] . ' was NOT created.' . "\n\n" . $e->getMessage());
            echo "\n\n";
            echo $e->getTraceAsString();
        }
    }
}
