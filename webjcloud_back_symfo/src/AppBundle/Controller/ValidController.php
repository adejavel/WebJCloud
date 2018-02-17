<?php
namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\User;
use AppBundle\Entity\ValidationUser;
use AppBundle\Form\Type\UserType;

class ValidController extends Controller
{
    /**
         * @Route("/valid/user/{key}", name="users_valid")
         * @Method({"GET"})
         */
        public function ValidUserAction(Request $request,\Swift_Mailer $mailer)
        {

            $validation = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:ValidationUser')
                ->findOneBy(array('keyuser'=>$request->get('key')));

            /* @var $validation ValidationUser */
            if (!$validation){
                return new JsonResponse(['message' => 'uncorrect validation'], Response::HTTP_BAD_REQUEST);
            }
            if ($validation->getValidUser()){
                return new JsonResponse(['message' => 'already done'], Response::HTTP_BAD_REQUEST);
            }

            $validation->setValidUser(true);
            $em = $this->get('doctrine.orm.entity_manager');
            $em->flush();



            $user = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:User')
                ->find($validation->getIdUser());
            /* @var $user User */

            $this->SendValidAdmin($mailer,$user->getLastname(),$user->getFirstname(),$user->getEmail(),'https://YOUR_URL/valid/admin/'.$validation->getKeyAdmin(),'to_change');

            return new JsonResponse(['message' => 'validation done','name'=>$user->getFirstname()], Response::HTTP_OK);
        }

    /**
     * @Route("/valid/admin/info/{key}", name="admin_info")
     * @Method({"GET"})
     */
    public function GetAdminAction(Request $request,\Swift_Mailer $mailer)
    {

        $validation = $this->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:ValidationUser')
            ->findOneBy(array('keyAdmin'=>$request->get('key')));

        /* @var $validation ValidationUser */



        $user = $this->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:User')
            ->find($validation->getIdUser());
        /* @var $user User */


        return new JsonResponse(['firstname' => $user->getFirstname(),'lastname'=>$user->getLastname(),'email'=>$user->getEmail()], Response::HTTP_OK);
    }
    /**
     * @Route("/valid/admin/valid/{key}", name="admin_valid")
     * @Method({"GET"})
     */
    public function ValidAdminAction(Request $request,\Swift_Mailer $mailer)
    {

        $validation = $this->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:ValidationUser')
            ->findOneBy(array('keyAdmin'=>$request->get('key')));

        /* @var $validation ValidationUser */



        $user = $this->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:User')
            ->find($validation->getIdUser());
        /* @var $user User */
        $user->setisValid(true);
        $validation->setValidAdmin(true);

        $this->get('doctrine.orm.entity_manager')->flush();
        $this->SendValidUser($mailer,$user->getFirstname(),$user->getEmail());

        return new JsonResponse(['firstname' => $user->getFirstname(),'lastname'=>$user->getLastname(),'email'=>$user->getEmail()], Response::HTTP_OK);
    }

    public function SendValidAdmin($mailer,$lastname,$firstname,$email,$url,$to){
        $message = (new \Swift_Message('Validation JCloud'))
            ->setFrom('to_change')
            ->setTo($to)
            ->setBody(
                $this->renderView(
                // app/Resources/views/emails/validuser.html.twig
                    'emails/validadmin.html.twig',
                    array('firstname' => $firstname,'lastname'=>$lastname,'email'=>$email,'url'=>$url)
                ),
                'text/html'
            )
        ;

        $mailer->send($message);
    }
    public function SendValiduser($mailer,$firstname,$to){
        $message = (new \Swift_Message('Votre compte JCloud a été validé !'))
            ->setFrom('to_change')
            ->setTo($to)
            ->setBody(
                $this->renderView(
                // app/Resources/views/emails/validuser.html.twig
                    'emails/accountvalid.html.twig',
                    array('firstname' => $firstname)
                ),
                'text/html'
            )
        ;

        $mailer->send($message);
    }




}
