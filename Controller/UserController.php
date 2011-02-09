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
 * Controller for main Flexiflow tasks.
 *
 * @author Timon Rapp <rapp@equinoxe.info>
 */
class UserController extends Controller
{

    /**
     * Controller for the / Action.
     *
     * @return Response The content of the view
     */
    public function listAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');

        $users = $em->getRepository('Equinoxe\AuthenticationBundle\Entity\User')->findAll();
        $result = array('total'=>1);
        $result['items'] = array();
        foreach($users as $user) {
            $roles = array();

            foreach($user->getRoles() as $role) {
                $roles[] = $role->getRole();
            }

            $result['items'][] = array(
                "uid" =>$user->getUid(),
                "userName" =>$user->getUsername(),
                "roles" => $roles
            );
        }

        if ($this->get('security.context')->vote('ROLE_ADMIN')) {
            $response = $this->createResponse(json_encode($result));
            return $response;
        } else {
            return $this->createResponse("{success: false, error: 'Access denied.'}");
        }
    }

    public function saveAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');

        $user = $em->find('Equinoxe\AuthenticationBundle\Entity\User', $_POST['uid']);
        try {
            if (!$this->get('security.context')->vote('ROLE_ADMIN')) {
                throw new \Exception("Access denied. Role ROLE_ADMIN required.");
            }
            $user->setUsername($_POST['userName']);

            $roles = new ArrayCollection();
            foreach($_POST['role'] as $roleName=>$notInteresting) {
                if (!$role = $em->getRepository('Equinoxe\AuthenticationBundle\Entity\Role')->findOneBy(array('role' => $roleName))) {
                    throw new \Exception("Specified role " . $roleName . " not found.");
                }
                $roles->add($role);
            }
            $user->setRoles($roles);
            $em->flush();
            return $this->createResponse("{success: true}");
        } catch (\Exception $e) {
            return $this->createResponse("{success: false, error: '" . $e->getMessage() . "'}");
        }
        
    }
}
