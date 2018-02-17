<?php
namespace AppBundle\Controller;

use AppBundle\Entity\NotifMail;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Document;
use AppBundle\Entity\Folder;
use AppBundle\Entity\User;

class DocumentController extends Controller
{
    /**
     * @Route("/documents", name="documents_list")
     * @Method({"GET"})
     */
    public function getDocumentsAction(Request $request)
    {
        $docs = $this->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:Document')
            ->findAll();

        /* @var $docs Document[] */

        $formatted = [];
        foreach ($docs as $doc) {
            $formatted[] = [
                'id' => $doc->getId(),
                'name' => $doc->getName(),
                'description' => $doc->getDescription(),
                'added'=>$doc->getAdded(),
                'folder'=>$doc->getFolder(),
                'url'=>$doc->getUrl(),
                'user'=>$doc->getUser()
            ];
        }

        return new JsonResponse($formatted);
    }

    /**
     * @Route("/documents/{doc_id}", name="doc_one")
     * @Method({"GET"})
     */
    public function getDocumentAction(Request $request)
    {
        $doc = $this->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:Document')
            ->find($request->get('doc_id'));

        /* @var $doc Document */

        if (empty($doc)) {
            return new JsonResponse(['message' => 'Document not found'], Response::HTTP_NOT_FOUND);
        }

        $formatted = [
            'id' => $doc->getId(),
            'name' => $doc->getName(),
            'description' => $doc->getDescription(),
            'added'=>$doc->getAdded(),
            'folder'=>$doc->getFolder(),
            'url'=>$doc->getUrl(),
            'user'=>$doc->getUser()
        ];

        return new JsonResponse($formatted);
    }
    /**
     * @Route("/documents/folder/{year}/{folder_name}", name="doc_by_folder")
     * @Method({"GET"})
     */
    public function getDocumentByFolderAction(Request $request)
    {
        $foldbyname = $this->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:Folder')
            ->findOneBy(array('normalized' => $request->get('folder_name'),'year' => $request->get('year')));
        /* @var $foldbyname Folder */

        $id_fold = $foldbyname->getId();

        $docs = $this->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:Document')
            ->findBy(array('folder' => $request->get('folder_name'),'year'=>$request->get('year')),array('added'=>'desc'));

        $token = $this->get('security.token_storage')->getToken();
        /* @var $token AuthToken */
        $user = $token -> getUser();
        /* @var $user User */
        $user_id = $user->getId();


        /* @var $docs Document[] */

        $formatted = [];
        foreach ($docs as $doc) {
            if ($doc->getUser()==$user_id){
                $isUser=true;
            }
            else{
                $isUser=false;
            }
            $formatted[] = [
                'id' => $doc->getId(),
                'name' => $doc->getName(),
                'description' => $doc->getDescription(),
                'added'=>$doc->getAdded(),
                'folder'=>$doc->getFolder(),
                'year'=>$doc->getYear(),
                'url'=>$doc->getUrl(),
                'user'=>$doc->getUser(),
                'mime'=>$doc->getMime(),
                'truetype'=>$doc->getTruetype(),
                'user'=>$isUser
            ];
        }

        return new JsonResponse($formatted);
    }

    /**
     * @Route("/documents/next/{year}/{folder_name}/{doc}", name="next_doc")
     * @Method({"GET"})
     */
    public function getNextDocumentAction(Request $request)
    {
        $foldbyname = $this->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:Folder')
            ->findOneBy(array('normalized' => $request->get('folder_name'),'year' => $request->get('year')));
        /* @var $foldbyname Folder */

        $id_fold = $foldbyname->getId();

        $docs = $this->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:Document')
            ->findBy(array('folder' => $request->get('folder_name'),'year'=>$request->get('year')),array('added'=>'desc'));

        $d = $request->get('doc');
        /* @var $docs Document[] */
        $key=0;
        foreach ($docs as $i=>$doc){
            if ($doc->getUrl()==$d){
                $type = $doc->getMime();
                $key = $i;
                break;
            }
        }
        $key_prev =0;
        $key_next = 0;
        if ($key==0){
            $key_prev = count($docs)-1;
        }
        else {
            $key_prev=$key-1;
        }
        if ($key!=count($docs)-1){
            $key_next=$key+1;
        }



        $formatted = [
            'next'=>$docs[$key_next]->getUrl(),
            'prev'=>$docs[$key_prev]->getUrl(),
            'type'=>$type
        ];


        return new JsonResponse($formatted);
    }

    /**
     * @Route("/documents", name="document_post")
     * @Method({"POST"})
     */
    public function postDocumentsAction(Request $request)
    {
        $code_aleatoire=$this->Code();


        $request=json_decode($request->getContent(), true);
        $doc = new Document();
        $doc->setName($request['name'])
            ->setDescription($request['description'])
            ->setAdded()
            ->setFolder($request['folder'])
            ->setUrl('https://YOUR_URL/document-'.$code_aleatoire);

        try {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($doc);
            $em->flush();
            return new JsonResponse(['message' => 'Document successfully created'], Response::HTTP_OK);
        } catch (Exception $e) {
            return new JsonResponse(['message' => 'Document already existing'], Response::HTTP_BAD_REQUEST);
        }


    }
    /**
     * @Route("/documents/{doc_id}", name="doc_put")
     * @Method({"PUT"})
     */
    public function putDocumentsAction(Request $request)
    {

        $doc = $this->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:Document')
            ->find($request->get('doc_id'));

        /* @var $doc Document */
        $request=json_decode($request->getContent(), true);
        $doc->setName($request['name'])
            ->setDescription($request['description']);

        try {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->flush();
            return new JsonResponse(['message' => 'Document successfully updated'], Response::HTTP_OK);
        } catch (Exception $e) {
            return new JsonResponse(['message' => 'Document updated'], Response::HTTP_BAD_REQUEST);
        }


    }
    /**
     * @Route("/documents/delete/{doc_id}", name="doc_delete")
     * @Method({"GET"})
     */
    public function getDocumentsDeleteAction(Request $request)
    {
        $token = $this->get('security.token_storage')->getToken();
        /* @var $token AuthToken */
        $user = $token -> getUser();
        /* @var $user User */
        $user_id = $user->getId();

        $doc = $this->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:Document')
            ->findOneBy(array('id' => $request->get('doc_id'),'user'=>$user_id));

        if ($doc){
            /* @var $doc Document */
            try {
                $logger = $this->get('logger');
                $logger->info(strval($user_id));
                $logger->info(strval($request->get('doc_id')));
                $logger->info($doc->getFolder());
                $year = $doc->getYear();
                $dossier = $doc->getFolder();
                $lien = $doc->getUrl();
                $em = $this->get('doctrine.orm.entity_manager');
                $em->remove($doc);
                $em->flush();
                return new JsonResponse(['message' => 'success','year'=>$year,'folder'=>$dossier,'url'=>$lien], Response::HTTP_OK);
            } catch (Exception $e) {
                return new JsonResponse(['message' => 'error'], Response::HTTP_BAD_REQUEST);
            }
        }
        return new JsonResponse(['message' => 'error'], Response::HTTP_BAD_REQUEST);

    }

    /**
     * @Route("/upload", name="upload_file")
     * @Method({"POST"})
     */
    public function uploadFile(Request $request,\Swift_Mailer $mailer){

        $token = $this->get('security.token_storage')->getToken();
        /* @var $token AuthToken */
        $user = $token -> getUser();
        /* @var $user User */
        $user_id = $user->getId();
        $logger = $this->get('logger');

        $request = json_decode($request->getContent(), true);
        $dossier = $request['dossier'];
        $ext = $request['ext'];
        $name= $request['name'];
        $mime = $request['type'];
        $truetype = $request['TrueExt'];
        $year = $request['year'];

        $norm = $this->Normalize($name);

        $same_file = $this->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:Document')
            ->findBy(array('url' =>$norm,'folder'=>$dossier,'year'=>$year));

        if ($same_file){
            $i=1;
            $norm.=strval($i);
            while ($same_file){
                $norm= substr($norm, 0, -1);
                $i+=1;
                $norm.=strval($i);
                $same_file = $this->get('doctrine.orm.entity_manager')
                    ->getRepository('AppBundle:Document')
                    ->findBy(array('url' =>$norm,'folder'=>$dossier,'year'=>$year));
            }
        }

        $doc = new Document();
        $doc->setName($name)
            ->setAdded()
            ->setFolder($dossier)
            ->setExt($ext)
            ->setUser($user_id)
            ->setMime($mime)
            ->setTruetype($truetype)
            ->setYear($year)
            ->setUrl($norm);

        try {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($doc);
            $em->flush();
            $doss = $em->getRepository('AppBundle:Folder')
                ->findOneBy(array('normalized' =>$dossier));
            /* @var $doss Folder */
            $doss->setUpdated(time());
            $em->flush();

            //Send emails
            $folder = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Folder')
                ->findOneBy(array('year' =>$year,'normalized'=>$dossier));
            /* @var $folder Folder */

            $folder_id = $folder->getId();
            $notif = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:NotifMail')
                ->findOneBy(array('folder_id' =>$folder_id));
            $url = "https://YOUR_URL/".$year.'/'.$dossier;
            if ($notif){
                /* @var $notif NotifMail */
                if ($notif->isTime()){
                    $this->SendNotif($mailer,$dossier,$url);
                    $notif->setLastNotif();
                }
            }
            else {
                $not = new NotifMail();
                $not->setFolderId($folder_id)
                    ->setLastNotif();
                $this->SendNotif($mailer,$dossier,$url);
                $em->persist($not);
            }
            $em->flush();

            return new JsonResponse(['message' => 'Success','name'=>$norm], Response::HTTP_OK);
        } catch (Exception $e) {
            return new JsonResponse(['message' => 'Error'], Response::HTTP_BAD_REQUEST);
        }
        return new JsonResponse(['message' => 'Error'], Response::HTTP_OK);
    }


    private function generateCode(){
        $characts    = 'abcdefghijklmnopqrstuvwxyz';
        $characts   .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $characts   .= '1234567890';
        $code_aleatoire      = null;

        for($i=0;$i < 100;$i++)
        {
            $code_aleatoire .= substr($characts,rand()%(strlen($characts)),1);
        }
        return $code_aleatoire;
    }
    private function Code(){
        $code_aleatoire = $this->generateCode();
        $doc_code = $this->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:Document')
            ->findOneBy(array('url' => 'https://YOUR_URL/document-'.$code_aleatoire));


        while($doc_code){
            $code_aleatoire=$this->generateCode();
            $doc_code = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Document')
                ->findOneBy(array('url' => 'https://YOUR_URL/document-'.$code_aleatoire));
        }
        return $code_aleatoire;
    }
    public function Normalize($name){
        $logger = $this->get('logger');
        $name = utf8_decode($name);
        //$name3 = $this->wd_remove_accents($name);
        //$logger->info($name3);
        $name2 =str_replace("?","",$name);
        $name2 =str_replace("."," ",$name2);
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

    public function sendNewFile($mailer,$firstname,$url,$dossier,$to){
        $message = (new \Swift_Message('Nouveaux fichiers sur JCloud !'))
            ->setFrom('to_change')
            ->setTo($to)
            ->setBody(
                $this->renderView(
                // app/Resources/views/emails/validuser.html.twig
                    'emails/newfile.html.twig',
                    array('firstname' => $firstname,'url'=>$url,'dossier'=>$dossier)
                ),
                'text/html'
            )
        ;

        $mailer->send($message);
    }

    public function SendNotif($mailer,$dossier,$url){
        $users = $this->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:User')
            ->findAll();

        /* @var $users User[] */

        foreach ($users as $user){

            $this->sendNewFile($mailer,$user->getFirstname(),$url,$dossier,$user->getEmail());
        }

    }

    function wd_remove_accents($str)
    {
        $str = utf8_decode($str);

        $texte = strtr(
            $str,
            '@ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ',
            'aAAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy'
        );

        return $texte;
    }
}