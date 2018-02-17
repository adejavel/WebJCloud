<?php

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity()
 * @ORM\Table(name="validationuser")
 */
class ValidationUser
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $id_user;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $validUser;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $validAdmin;


    /**
     * @ORM\Column(type="string")
     */
    protected $keyuser;
    /**
     * @ORM\Column(type="string")
     */
    protected $keyAdmin;

    /**
     * @return mixed
     */
    public function getIdUser()
    {
        return $this->id_user;
    }

    /**
     * @param mixed $id_user
     */
    public function setIdUser($id_user)
    {
        $this->id_user = $id_user;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValidUser()
    {
        return $this->validUser;
    }

    /**
     * @param mixed $validUser
     */
    public function setValidUser($validUser)
    {
        $this->validUser = $validUser;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValidAdmin()
    {
        return $this->validAdmin;
    }

    /**
     * @param mixed $validAdmin
     */
    public function setValidAdmin($validAdmin)
    {
        $this->validAdmin = $validAdmin;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getKeyuser()
    {
        return $this->keyuser;
    }

    /**
     * @param mixed $keyuser
     */
    public function setKeyuser($keyuser)
    {
        $this->keyuser = $keyuser;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getKeyAdmin()
    {
        return $this->keyAdmin;
    }

    /**
     * @param mixed $keyAdmin
     */
    public function setKeyAdmin($keyAdmin)
    {
        $this->keyAdmin = $keyAdmin;
        return $this;
    }
}
