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

namespace Equinoxe\AuthenticationBundle\Entity;

use Symfony\Component\Security\Core\Role\RoleInterface;

/**
 * Default role class.
 *
 * @orm:Entity
 */
class Role implements RoleInterface
{
    /**
     * Unique Id for the database.
     *
     * @var integer
     * @orm:Id
     * @orm:Column(type="integer")
     * @orm:GeneratedValue(strategy="IDENTITY")
     */
    protected $uid;

    /**
     * Name of the role.
     *
     * @var string
     * @orm:Column(type="string")
     */
    protected $role;

    /**
     * Constructor.
     *
     * @param string $role The role name
     */
    public function __construct($role)
    {
        $this->role = (string) $role;
    }

    /**
     * {@inheritdoc}
     */
    public function getRole()
    {
        return $this->role;
    }

    public function __toString()
    {
        return $this->role;
    }

}
