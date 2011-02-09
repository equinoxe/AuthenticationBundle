<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PasswordEncoderFactory
 *
 * @author equinoxe
 */

namespace Equinoxe\AuthenticationBundle\Service;

use Symfony\Component\Security\Core\Encoder\BasePasswordEncoder;

class PasswordEncoder extends BasePasswordEncoder
{

    /**
     * {@inheritdoc}
     */
    public function encodePassword($raw, $salt)
    {
        return \sha1($this->mergePasswordAndSalt($raw, $salt));
    }

    /**
     * {@inheritdoc}
     */
    public function isPasswordValid($encoded, $raw, $salt)
    {
        $pass2 = \sha1($this->mergePasswordAndSalt($raw, $salt));
        return $this->comparePasswords($encoded, $pass2);
    }
}
