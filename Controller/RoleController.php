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
    public function listAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        try {
            if (!$this->get('security.context')->vote('ROLE_ADMIN')) {
                throw new Exception('Access denied.');
            }
            $roles = $em->getRepository('Equinoxe\AuthenticationBundle\Entity\Role')->findAll();
            $result = array('total'=>count($roles));
            $result['items'] = array();
            foreach($roles as $role) {
                $result['items'][] = array(
                    "uid" => $role->getUid(),
                    "name" => $role->getRole()
                );
            }
            $response = $this->createResponse(json_encode($result));
            return $response;
        } catch (\Exception $e) {
            return $this->createResponse(\json_encode(array("success"=>false, "error"=>$e->getMessage())));
        }
    }

    public function saveAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        try {
            if (!$this->get('security.context')->vote('ROLE_ADMIN')) {
                throw new \Exception("Access denied. Role ROLE_ADMIN required.");
            }
            if (isset($_POST['new'])) {
                
                //
                // Create role.
                //

                if (!isset($_POST['name']) || empty($_POST['name'])) {
                    throw new \Exception("The name of the role cannot be empty");
                }

                $role = new \Equinoxe\AuthenticationBundle\Entity\Role($_POST['name']);
                $em->persist($role);
                $em->flush();

            } else {

                //
                // Edit role.
                //

                $role = $em->find('Equinoxe\AuthenticationBundle\Entity\Role', $_POST['uid']);

                if (!$this->get('security.context')->vote('ROLE_ADMIN')) {
                    throw new \Exception("Access denied. Role ROLE_ADMIN required.");
                }
                $role->setRole($_POST['name']);

                $em->flush();
            }

            return $this->createResponse("{success: true}");
            
        } catch (\Exception $e) {
            return $this->createResponse("{success: false, error: '" . $e->getMessage() . "'}");
        }
    }

    public function deleteAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
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

            // Delete them.
            foreach ($roles as $uid) {
                $role = $em->find('Equinoxe\AuthenticationBundle\Entity\Role', $uid);
                if (!$role) {
                    throw new \Exception("Role with id $uid doesn't exist.");
                }
                $em->remove($role);
                $em->flush();
            }

            return $this->createResponse("{success: true}");

        } catch (\Exception $e) {
            return $this->createResponse("{success: false, error: '" . $e->getMessage() . "'}");
        }
    }
}
