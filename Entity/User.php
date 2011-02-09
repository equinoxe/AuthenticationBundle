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

use Symfony\Component\Security\Core\User\AdvancedAccountInterface;
use Symfony\Component\Security\Core\User\AccountInterface;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\Security\Encoder\PasswordEncoderInterface;

/**
 * Default user class.
 *
 * @orm:Entity
 */
class User implements AdvancedAccountInterface
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
     * The username.
     *
     * @var string
     * @orm:Column(type="string")
     */
    protected $username;

    /**
     * The password.
     *
     * @var string
     * @orm:Column(type="string", nullable="true")
     */
    protected $password;

    /**
     * The date when this account expires. Null to prevent expiration.
     *
     * @var \DateTime
     * @orm:Column(type="datetime", nullable="true")
     */
    protected $accountExpireDate = null;

    /**
     * The date when the credentials will expire. Null to prevent expiration.
     *
     * @var \DateTime
     * @orm:Column(type="datetime", nullable="true")
     */
    protected $credentialsExpireDate = null;

    /**
     * True if the account is not locked, false otherwise.
     *
     * @var boolean
     * @orm:Column(type="boolean")
     */
    protected $accountNonLocked = true;

    /**
     * True if the account is enabled, false otherwise.
     *
     * @var boolean
     * @orm:Column(type="boolean")
     */
    protected $enabled = true;

    /**
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @orm:ManyToMany(targetEntity="Equinoxe\AuthenticationBundle\Entity\Role")
     * @orm:JoinTable(name="user_role",
     *      joinColumns={@orm:JoinColumn(name="user_id", referencedColumnName="uid")},
     *      inverseJoinColumns={@orm:JoinColumn(name="role_id", referencedColumnName="uid")}
     *      )
     */
    protected $roles;


    function __construct($username = null, $password = null)
    {
        $this->roles = new ArrayCollection();
        $this->username = $username;
        $this->password = $password;
    }

    function isAccountNonExpired()
    {
        if ($this->accountExpireDate === null) {
            return true;
        }

        return (\time() < $this->accountExpireDate->getTimestamp());
    }

    function isAccountNonLocked()
    {
        return $this->accountNonLocked;
    }

    function isCredentialsNonExpired()
    {
        if ($this->credentialsExpireDate === null) {
            return true;
        }

        return (\time() < $this->credentialsExpireDate->getTimestamp());
    }

    function isEnabled()
    {
        return $this->enabled;
    }

    function enable()
    {
        $this->enabled = true;
    }

    function disable()
    {
        $this->enabled = false;
    }

    function __toString()
    {
        return $this->username;
    }

    function getRoles()
    {
        return $this->roles->toArray();
    }

    function addRole($role)
    {
        $this->roles->add($role);
    }
    
    function removeRole($role)
    {
        $this->roles->removeElement($role);
    }
    
    function hasRole($role)
    {
        if ($this->roles->indexOf($role) !== false)
        {
           return true;
        }
        return false;
    }

    function setRoles(ArrayCollection $roles)
    {
        $this->roles = $roles;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    function getPassword()
    {
        return $this->password;
    }

    function getSalt()
    {
        return $this->uid;
    }

    function setUsername($username)
    {
        $this->username = $username;
    }

    function getUsername()
    {
        return $this->username;
    }

    public function getUid()
    {
        return $this->uid;
    }

    public function setUid($uid)
    {
        $this->uid = $uid;
    }

    function eraseCredentials()
    {
        $this->password = null;
    }

    public function getAccountExpireDate()
    {
        return $this->accountExpireDate;
    }

    public function setAccountExpireDate(\DateTime $expireDate)
    {
        $this->accountExpireDate = $expireDate;
    }

    public function getCredentialsExpireDate()
    {
        return $this->credentialsExpireDate;
    }

    public function setCredentialsExpireDate(\DateTime $expireDate)
    {
        $this->credentialsExpireDate = $expireDate;
    }

    public function lock()
    {
        $this->accountNonLocked = false;
    }

    public function unlock()
    {
        $this->accountNonLocked = true;
    }

    public function equals(AccountInterface $account)
    {
        if ($account->getUid() == $this->getUid()) {
            return true;
        } else {
            return false;
        }
    }
}
