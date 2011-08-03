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

use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Encoder\PasswordEncoderInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Default user class.
 *
 * @ORM\Entity
 */
class User implements AdvancedUserInterface
{
    /**
     * Unique Id for the database.
     *
     * @var integer
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $uid;

    /**
     * The username.
     *
     * @var string
     * @ORM\Column(type="string")
     */
    protected $username;

    /**
     * The password.
     *
     * @var string
     * @ORM\Column(type="string", nullable="true")
     */
    protected $password;

    /**
     * The date when this account expires. Null to prevent expiration.
     *
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable="true")
     */
    protected $accountExpireDate = null;

    /**
     * The date when the credentials will expire. Null to prevent expiration.
     *
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable="true")
     */
    protected $credentialsExpireDate = null;

    /**
     * True if the account is not locked, false otherwise.
     *
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    protected $accountNonLocked = true;

    /**
     * True if the account is enabled, false otherwise.
     *
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    protected $enabled = true;

    /**
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\ManyToMany(targetEntity="Equinoxe\AuthenticationBundle\Entity\Role")
     * @ORM\JoinTable(name="user_role",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="uid")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="uid")}
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

    public function equals(UserInterface $user)
    {
        if ($user->getUid() == $this->getUid()) {
            return true;
        } else {
            return false;
        }
    }
}
