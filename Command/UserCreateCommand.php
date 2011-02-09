<?php
/**
 * This file is part of the Flexiflow package.
 *
 * @package    Core
 * @subpackage Main
 * @author     Timon Rapp <rapp@equinoxe.info>
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

use Equinoxe\AuthenticationBundle\Entity\User;
use Equinoxe\AuthenticationBundle\Entity\Role;

/**
 * Acts as daemon process and handles all active triggers.
 */
class UserCreateCommand extends Command
{
    /**
     * Configures the command and its help.
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('flexiflow:user:create')
             ->addArgument('username', InputArgument::REQUIRED, 'Username')
             ->addArgument('role', InputArgument::REQUIRED, 'Roles')
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
            $pw = 'changeme';
            
            $user = new User($options['username']);
            $em->persist($user);

            if (!$role = $em->getRepository("Equinoxe\AuthenticationBundle\Entity\Role")->findOneBy(array('role' => $options['role']))) {
                throw new \Exception("Role " . $options['role'] . " not found.");
            }

            $user->addRole($role);
            $em->flush();

            $passwordEncoder = $this->container->get('security.password_encoder');

            $password = $passwordEncoder->encodePassword($pw, $user->getSalt());
            $user->setPassword($password);
            $em->flush();
            
            $output->write('User ' . $options['username'] . ' created with password ' . $pw  . "\n");
        } catch (\Exception $e) {
            $output->write('User ' . $options['username'] . ' was NOT created.' . "\n\n" . $e->getMessage());
            echo "\n\n";
            echo $e->getTraceAsString();
        }
    }
}
