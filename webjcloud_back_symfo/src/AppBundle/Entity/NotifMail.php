<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="notifsmail")
 */

class NotifMail
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
    protected $folder_id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $last_notif;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
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
     * @return mixed
     */
    public function getFolderId()
    {
        return $this->folder_id;
    }

    /**
     * @param mixed $folder_id
     */
    public function setFolderId($folder_id)
    {
        $this->folder_id = $folder_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastNotif()
    {
        return $this->last_notif;
    }

    /**
     * @param mixed $last_notif
     */
    public function setLastNotif()
    {
        $this->last_notif = time();
        return $this;
    }

    public function isTime(){
        if (time()-$this->getLastNotif()>=60*30){
            return true;
        }
        return false;
    }






}