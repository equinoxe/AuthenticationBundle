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

        $role = $em->find('Equinoxe\AuthenticationBundle\Entity\Role', $_POST['uid']);
        try {
            if (!$this->get('security.context')->vote('ROLE_ADMIN')) {
                throw new \Exception("Access denied. Role ROLE_ADMIN required.");
            }
            $role->setRole($_POST['name']);

            $em->flush();
            return $this->createResponse("{success: true}");
        } catch (\Exception $e) {
            return $this->createResponse("{success: false, error: '" . $e->getMessage() . "'}");
        }
        
    }
}
