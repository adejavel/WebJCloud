<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\Type\CredentialsType;
use AppBundle\Entity\AuthToken;
use AppBundle\Entity\Credentials;
use AppBundle\Entity\User;


class AuthTokenController extends Controller
{
    /**
     * @Route("/auth-tokens", name="auth_token")
     * @Method({"POST"})
     */
    public function postAuthTokensAction(Request $request)
    {
        $request=json_decode($request->getContent(), true);

        $credentials = new Credentials();


        if ($request['login']==""&&$request['password']=="") {
            return new JsonResponse(['message' => 'Empty field'], Response::HTTP_BAD_REQUEST,['Access-Control-Allow-Origin'=> '*']);
        }
        $credentials->setLogin($request['login']);
        $credentials->setPassword($request['password']);
        $em = $this->get('doctrine.orm.entity_manager');

        $user = $em->getRepository('AppBundle:User')
            ->findOneByEmail($credentials->getLogin());


        if (!$user) { // L'utilisateur n'existe pas
            return new JsonResponse(['message' => 'bad login'], Response::HTTP_BAD_REQUEST,['Access-Control-Allow-Origin'=> '*']);
        }

        $encoder = $this->get('security.password_encoder');
        $isPasswordValid = $encoder->isPasswordValid($user, $credentials->getPassword());


        if (!$isPasswordValid) { // Le mot de passe n'est pas correct
            return new JsonResponse(['message' => 'bad password'], Response::HTTP_BAD_REQUEST,['Access-Control-Allow-Origin'=> '*']);
        }


        /* @var $user User */
        if (!($user->getisValid())){
            return new JsonResponse(['message' => 'account not valid'], Response::HTTP_BAD_REQUEST,['Access-Control-Allow-Origin'=> '*']);
        }

        $tokens = $this->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:AuthToken')
            ->findBy(array('user' =>$user->getId() ));
        /* @var $tokens AuthToken[] */
        $em = $this->get('doctrine.orm.entity_manager');
        foreach ($tokens as $token){
                $em->remove($token);
        }
        $em->flush();

        $authToken = new AuthToken();
        $authToken->setValue(base64_encode(random_bytes(50)));
        $authToken->setCreatedAt(new \DateTime('now'));
        $authToken->setUser($user);

        $em->persist($authToken);
        $em->flush();

        return new JsonResponse(['token' => $authToken->getValue(),'valid'=>(time()+30*60)], Response::HTTP_OK,['Access-Control-Allow-Origin'=> '*']);
    }
    /**
     * @Route("/auth-tokens", name="auth_token_del")
     * @Method({"DELETE"})
     */

    public function delAuthTokensAction(Request $request)
    {
        $token = $this->get('security.token_storage')->getToken();
        $tokens = $this->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:AuthToken')
            ->findBy(array('value' =>$token));

        $em = $this->get('doctrine.orm.entity_manager');
        $em -> remove($tokens);
        return new JsonResponse(['deleted' => 'OK'], Response::HTTP_OK);
    }

}
