<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\User;
use AppBundle\Form\Type\UserType;
use AppBundle\Entity\ValidationUser;

class UserController extends Controller
{
    /**
     * @Route("/users", name="users_list")
     * @Method({"GET"})
     */
    public function getUsersAction(Request $request)
    {
        $users = $this->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:User')
            ->findAll();
        /* @var $users User[] */

        $formatted = [];
        foreach ($users as $user) {
            $formatted[] = [
                'id' => $user->getId(),
                'firstname' => $user->getFirstname(),
                'lastname' => $user->getLastname(),
                'email' => $user->getEmail(),
                'isValid'=>$user->getisValid(),
            ];
        }

        return new JsonResponse($formatted);
    }
    /**
     * @Route("/users/{id}", name="users_one")
     * @Method({"GET"})
     */
    public function getUserAction(Request $request)
    {
        $user = $this->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:User')
            ->find($request->get('id'));
        /* @var $user User */

        if (empty($user)) {
            return new JsonResponse(['message' => 'Place not found'], Response::HTTP_NOT_FOUND);
        }

        $formatted = [
            'id' => $user->getId(),
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'email' => $user->getEmail(),
            'isValid'=>$user->getisValid()
        ];

        return new JsonResponse($formatted);
    }
    /**
     * @Route("/users/valid", name="valid_token")
     * @Method({"POST"})
     */
    public function validUserAction(Request $request)
    {
        $logger = $this->get('logger');

        $logger->info("Authentication success");

        return new JsonResponse(['message' => 'success'], Response::HTTP_OK);
    }
    /**
     * @Route("/users", name="user_post")
     * @Method({"POST"})
     */
    public function postUserAction(Request $request,\Swift_Mailer $mailer)
    {

        $request=json_decode($request->getContent(), true);

        $user = new User();
        $user->setFirstname($request['firstname'])
            ->setLastname($request['lastname'])
            ->setEmail($request['email'])
            ->setisValid(false)
            ->setPlainPassword($request['plainPassword']);

        $same_user = $this->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:User')
            ->findOneBy(array('email'=>$user->getEmail()));
        if ($same_user){
            return new JsonResponse(['message' => 'User already existing'], Response::HTTP_BAD_REQUEST);
        }



        if(filter_var($user->getEmail(), FILTER_VALIDATE_EMAIL)) {
            $encoder = $this->get('security.password_encoder');
            $encoded = $encoder->encodePassword($user, $user->getPlainPassword());
            $user->setisValid(false);
            $user->setPassword($encoded);
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($user);
            $em->flush();
            try {
                $em = $this->get('doctrine.orm.entity_manager');
                $em->persist($user);
                $em->flush();
                $id_user = $user->getId();
                $keyuser = $this->Key("keyuser");
                $keyAdmin = $this->Key("keyAdmin");
                $validation = new ValidationUser();
                $validation->setIdUser($id_user)
                    ->setKeyAdmin($keyAdmin)
                    ->setKeyuser($keyuser)
                    ->setValidAdmin(false)
                    ->setValidUser(false);
                $em->persist($validation);
                $em->flush();
                $this->sendValidation($mailer,$user->getFirstname(),'https://YOUR_URL/valid/user/'.$keyuser,$user->getEmail());
                return new JsonResponse(['message' => 'User successfully created'], Response::HTTP_OK);
            } catch (Exception $e) {
                return new JsonResponse(['message' => 'User already existing'], Response::HTTP_BAD_REQUEST);
            }
        }
        else {
            return new JsonResponse(['message' => 'mail invalide!'], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(['message' => 'User successfully created'], Response::HTTP_OK);

    }


    private function generateKey(){
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
    private function Key($who){
        $code_aleatoire = $this->generateKey();
        $doc_code = $this->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:ValidationUser')
            ->findOneBy(array($who => $code_aleatoire));


        while($doc_code){
            $code_aleatoire=$this->generateCode();
            $doc_code = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:ValidationUser')
                ->findOneBy(array($who => $code_aleatoire));
        }
        return $code_aleatoire;
    }

    public function sendValidation($mailer,$name,$url,$to){
        $message = (new \Swift_Message('Bienvenue sur JCloud !'))
            ->setFrom('to_change')
            ->setTo($to)
            ->setBody(
                $this->renderView(
                // app/Resources/views/emails/validuser.html.twig
                    'emails/validuser.html.twig',
                    array('name' => $name,'url'=>$url)
                ),
                'text/html'
            )
        ;

        $mailer->send($message);
    }
    
}