<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="documents")
 */

class Document
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(type="string",nullable=true)
     */
    protected $name;

    /**
     * @ORM\Column(type="string",nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(type="string",nullable=true)
     */
    protected $url;

    /**
     * @ORM\Column(type="string",nullable=true)
     */
    protected $ext;

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @ORM\Column(type="integer")
     */
    protected $added;

    /**
     * @ORM\Column(type="string")
     */
    protected $folder;
    /**
     * @ORM\Column(type="string")
     */
    protected $year;

    /**
     * @ORM\Column(type="integer")
     */
    protected $user;

    /**
     * @ORM\Column(type="string")
     */
    protected $mime;

    /**
     * @ORM\Column(type="string")
     */
    protected $truetype;



    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return mixed
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param mixed $year
     */
    public function setYear($year)
    {
        $this->year = $year;
        return $this;
    }




    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    public function getId(){
        return $this->id;
    }
    public function getName(){
        return $this->name;
    }
    public function getDescription(){
        return $this-> description;
    }
    public function getFolder(){
        return $this->folder;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @param mixed $folder
     */
    public function setFolder($folder)
    {
        $this->folder = $folder;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAdded()
    {
        return $this->added;
    }

    /**
     * @param mixed $added
     */
    public function setAdded()
    {
        $this->added = time();
        return $this;
    }

    /**
     * @return mixed
     */
    public function getExt()
    {
        return $this->ext;
    }

    /**
     * @param mixed $ext
     */
    public function setExt($ext)
    {
        $this->ext = $ext;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMime()
    {
        return $this->mime;
    }

    /**
     * @param mixed $mime
     */
    public function setMime($mime)
    {
        $this->mime = $mime;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTruetype()
    {
        return $this->truetype;
    }

    /**
     * @param mixed $truetype
     */
    public function setTruetype($truetype)
    {
        $this->truetype = $truetype;
        return $this;
    }



}
