<?php
/**
 * This file is part of the Symfony package.
 *
 * @package    Core
 * @subpackage Main
 * @author     Timon Rapp <rapp@equinoxe.info>
 * @copyright  Equinoxe GmbH <info@equinoxe.de>
 * @license    GPL V3
 */

namespace Equinoxe\AuthenticationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Controller for showing and editing Roles.
 *
 * @author Timon Rapp <rapp@equinoxe.info>
 */
class RoleController extends Controller
{

    /**
     * Controller for the / Action.
     *
     * @return Response The content of the view
     */
    public function listAction($_format)
    {
        $simpleOutput = $this->get('equinoxe.simpleoutput');
        try {
            if (!$this->get('security.context')->vote('ROLE_ADMIN')) {
                throw new Exception('Access denied.');
            }
            $em = $this->get('doctrine.orm.entity_manager');
            $roles = $em->getRepository('Equinoxe\AuthenticationBundle\Entity\Role')->findAll();
            $result = array('total'=>count($roles));
            $result['items'] = array();
            foreach($roles as $role) {
                $result['items'][] = array(
                    "uid" => $role->getUid(),
                    "name" => $role->getRole()
                );
            }

            return $this->createResponse($simpleOutput->convert($result, $_format));
        } catch (\Exception $e) {
            return $this->createResponse($simpleOutput->convert($response, $_format));
        }
    }

    public function saveAction()
    {
        try {
            if (!$this->get('security.context')->vote('ROLE_ADMIN')) {
                throw new \Exception("Access denied. Role ROLE_ADMIN required.");
            }

            if (!isset($_POST['name']) || empty($_POST['name'])) {
                throw new \Exception("The name of the role cannot be empty");
            }
            $em = $this->get('doctrine.orm.entity_manager');

            if (isset($_POST['new'])) {
                
                //
                // Create role.
                //

                $role = new \Equinoxe\AuthenticationBundle\Entity\Role($_POST['name']);
                $em->persist($role);
            } else {

                //
                // Edit role.
                //

                $role = $em->find('Equinoxe\AuthenticationBundle\Entity\Role', $_POST['uid']);

                if (!$this->get('security.context')->vote('ROLE_ADMIN')) {
                    throw new \Exception("Access denied. Role ROLE_ADMIN required.");
                }
                $role->setRole($_POST['name']);
            }

             $em->flush();

            return $this->createResponse("{success: true}");
            
        } catch (\Exception $e) {
            return $this->createResponse("{success: false, error: '" . $e->getMessage() . "'}");
        }
    }

    public function deleteAction($_format)
    {
        try {
            // Check rights.
            if (!$this->get('security.context')->vote('ROLE_ADMIN')) {
                throw new \Exception("Access denied. Role ROLE_ADMIN required.");
            }

            // Are roles supplied?
            if (!isset($_POST['roles']) || empty($_POST['roles'])) {
                throw new \Exception("No roles supplied.");
            }

            // Parse them to an array.
            $roles = array($_POST['roles']);
            if (\strstr($_POST['roles'], ',')) {
                $roles = \explode(',', $_POST['roles']);
            }

            $em = $this->get('doctrine.orm.entity_manager');

            // Delete them.
            foreach ($roles as $uid) {
                $role = $em->find('Equinoxe\AuthenticationBundle\Entity\Role', $uid);
                if (!$role) {
                    throw new \Exception("Role with id $uid doesn't exist.");
                }
                $em->remove($role);
                $em->flush();
            }

            $response = array("success" => true);
            return $this->createResponse($simpleOutput->convert($response, $_format));

        } catch (\Exception $e) {
            $response = array("success" => false, "error" => $e->getMessage());
            return $this->createResponse($simpleOutput->convert($response, $_format));
        }
    }
}
