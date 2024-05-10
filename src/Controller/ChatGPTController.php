<?php

namespace App\Controller;

use OpenAI;
use App\Entity\enduser;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ChatGPTController extends AbstractController
{
    #[Route('/chatgpt', name: 'chatgpt')]
    public function index( ? string $question, ? string $response,ManagerRegistry $doctrine, Request $request): Response
    {
        $userId = $request->getSession()->get('user_id');
        //get user
                $userRepository = $doctrine->getRepository(enduser::class);
                $users = $userRepository->findOneBy(['id_user' => $userId]);
        return $this->render('messagerie/chat.html.twig', [
            'user' => $users,
        ]);
    }

    #[Route('/messagerie/chatbot', name: 'chatbot')]
    public function index1( ? string $question, ? string $response, ManagerRegistry $doctrine, Request $request): Response
    {
        $userId = $request->getSession()->get('user_id');
        //get user
                $userRepository = $doctrine->getRepository(enduser::class);
                $users = $userRepository->findOneBy(['id_user' => $userId]);
        return $this->render('messagerie/chatgpt.html.twig', [
            'user' => $users,
        ]);
    }



    #[Route('/chat', name: 'send_chat', methods:"POST")]
    public function chat(Request $request): Response
    {
        $question=$request->request->get('text');

        //Implémentation du chat gpt

        $myApiKey = $_ENV['OPENAI_KEY'];


        $client = OpenAI::client($myApiKey);

        $result = $client->completions()->create([
            'model' => 'text-davinci-003',
            'prompt' => $question,
            'max_tokens'=>2048
        ]);

        $response=$result->choices[0]->text;
  
        
        return $this->forward('App\Controller\HomeController::index', [
           
            'question' => $question,
            'response' => $response
        ]);
    }

   


}