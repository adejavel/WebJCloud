<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Folder;
use AppBundle\Entity\AuthToken;
use AppBundle\Entity\User;

class FolderController extends Controller
{
    /**
     * @Route("/folders", name="folders_list")
     * @Method({"GET"})
     */
    public function getFoldersAction(Request $request)
    {
        $folders = $this->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:Folder')
            ->findBy(array(),array('year'=>'desc'));

        /* @var $folders Folder[] */

        $formatted = [];
        foreach ($folders as $folder) {
            $formatted[] = [
                'id' => $folder->getId(),
                'name' => $folder->getName(),
                'description' => $folder->getDescription(),
                'updated'=>$folder->getUpdated(),
                'normalized'=>$folder->getNormalized(),
                'year'=>$folder->getYear(),
            ];
        }

        return new JsonResponse($formatted);
    }


    /**
     * @Route("/folders/year/{year}", name="folder_one")
     * @Method({"GET"})
     */
    public function getFolderByYearAction(Request $request)
    {
        $folders = $this->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:Folder')
            ->findBy(array('year' => $request->get('year')),array('updated'=>'desc'));

        $token = $this->get('security.token_storage')->getToken();
        /* @var $token AuthToken */
        $user = $token -> getUser();
        /* @var $user User */
        $user_id = $user->getId();

        /* @var $folders Folder[] */

        $formatted = [];
        foreach ($folders as $folder) {
            if ($folder->getUser()==$user_id){
                $isUser = true;
            }
            else {
                $isUser=false;
            }
            $formatted[] = [
                'id' => $folder->getId(),
                'name' => $folder->getName(),
                'description' => $folder->getDescription(),
                'updated'=>$folder->getUpdated(),
                'normalized'=>$folder->getNormalized(),
                'year'=>$folder->getYear(),
                'user'=>$isUser
            ];
        }

        return new JsonResponse($formatted);
    }
    /**
     * @Route("/folders/delete/{doc_id}", name="folder_delete")
     * @Method({"GET"})
     */
    public function getFolderDeleteAction(Request $request)
    {
        $token = $this->get('security.token_storage')->getToken();
        /* @var $token AuthToken */
        $user = $token -> getUser();
        /* @var $user User */
        $user_id = $user->getId();

        $doc = $this->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:Folder')
            ->findOneBy(array('id' => $request->get('doc_id'),'user'=>$user_id));

        if ($doc){
            /* @var $doc Folder */
            try {
                $logger = $this->get('logger');
                $logger->info(strval($user_id));
                $logger->info(strval($request->get('doc_id')));
                $year = $doc->getYear();
                $lien = $doc->getNormalized();
                $em = $this->get('doctrine.orm.entity_manager');
                $em->remove($doc);
                $em->flush();
                return new JsonResponse(['message' => 'success','year'=>$year,'url'=>$lien], Response::HTTP_OK);
            } catch (Exception $e) {
                return new JsonResponse(['message' => 'error'], Response::HTTP_BAD_REQUEST);
            }
        }
        return new JsonResponse(['message' => 'error'], Response::HTTP_BAD_REQUEST);

    }


    /**
     * @Route("/folders", name="folder_post")
     * @Method({"POST"})
     */
    public function postFoldersAction(Request $request)
    {
        $token = $this->get('security.token_storage')->getToken();
        /* @var $token AuthToken */
        $user = $token -> getUser();
        /* @var $user User */
        $user_id = $user->getId();

        $request=json_decode($request->getContent(), true);
        $name = $this->Normalize($request['name']);

        $same_folder = $this->get('doctrine.orm.entity_manager')
        ->getRepository('AppBundle:Folder')
        ->findBy(array('normalized' =>$name,'year'=>$request['year']));

        if ($same_folder){
            $i=1;
            $name.=strval($i);
            while ($same_folder){
                $name= substr($name, 0, -1);
                $i+=1;
                $name.=strval($i);
                $same_folder = $this->get('doctrine.orm.entity_manager')
                    ->getRepository('AppBundle:Folder')
                    ->findBy(array('normalized' =>$name,'year'=>$request['year']));
            }
        }



        $folder = new Folder();
        $folder->setName($request['name'])
            ->setDescription($request['description'])
            ->setYear($request['year'])
            ->setUpdated()
            ->setNormalized($name)
            ->setUser($user_id);

        try {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($folder);
            $em->flush();
            return new JsonResponse(['message' => 'Folder successfully created'], Response::HTTP_OK);
        } catch (Exception $e) {
            return new JsonResponse(['message' => 'Folder already existing'], Response::HTTP_BAD_REQUEST);
        }


    }
    /**
     * @Route("/folders/{folder_id}", name="folder_put")
     * @Method({"PUT"})
     */
    public function putFoldersAction(Request $request)
    {

        $folder = $this->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:Folder')
            ->find($request->get('folder_id'));

        /* @var $folder Folder */
        $request=json_decode($request->getContent(), true);
        $folder->setName($request['name'])
            ->setDescription($request['description'])
            ->setYear($request['year']);

        try {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->flush();
            return new JsonResponse(['message' => 'Folder successfully updated'], Response::HTTP_OK);
        } catch (Exception $e) {
            return new JsonResponse(['message' => 'Folder updated'], Response::HTTP_BAD_REQUEST);
        }


    }

    /**
     * @Route("/folders/{folder_id}", name="folder_put")
     * @Method({"GET"})
     */
    public function getFolderByIdAction(Request $request)
    {
        $folder = $this->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:Folder')
            ->find($request->get('folder_id'));

        /* @var $folder Folder */

        if (empty($folder)) {
            return new JsonResponse(['message' => 'Folder not found'], Response::HTTP_NOT_FOUND);
        }

        $formatted = [
            'id' => $folder->getId(),
            'name' => $folder->getName(),
            'description' => $folder->getDescription(),
            'updated'=>$folder->getUpdated(),
            'normalized'=>$folder->getNormalized(),
            'year'=>$folder->getYear(),
        ];

        return new JsonResponse($formatted);
    }

    public function Normalize($name){
        $logger = $this->get('logger');

        $name2 =str_replace("."," ",$name);
        $logger->info($name2);
        $tab = explode(" ",$name2);
        $string ="";

        foreach ($tab as $elem){
            $elem = strtolower($elem);
            $elem = ucfirst($elem);
            $string .=$elem;
        }
        return $string;
    }

}