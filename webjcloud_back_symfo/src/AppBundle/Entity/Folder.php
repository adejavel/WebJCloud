<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="folders")
 */

class Folder
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\Column(type="string")
     */
    protected $description;

    /**
     * @ORM\Column(type="integer")
     */
    protected $updated;

    /**
     * @ORM\Column(type="integer")
     */
    protected $year;

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @ORM\Column(type="integer")
     */
    protected $user;
    /**
     * @ORM\Column(type="string")
     */
    protected $normalized;

    /**
     * @return mixed
     */
    public function getNormalized()
    {
        return $this->normalized;
    }

    /**
     * @param mixed $normalized
     */
    public function setNormalized($normalized)
    {
        $this->normalized = $normalized;
        return $this;
    }



    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDescription()
    {
        return $this->description;
    }
    public function getUpdated()
    {
        return $this->updated;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function setDescription($desc)
    {
        $this->description = $desc;
        return $this;
    }
    public function getYear()
    {
        return $this->year;
    }

    public function setYear($year)
    {
        $this->year = $year;
        return $this;
    }
    public function setUpdated()
    {
        $this->updated = time();
        return $this;
    }
}