<?php

namespace App\Controller;

const MIN_SIMILARITY_THRESHOLD = 70; 

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChatbotController extends AbstractController
{

    #[Route('/chatbot', name: 'chatbot_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('chatbot/index.html.twig');
    }

    #[Route('/chatbot/process', name: 'chatbot_process', methods: ['POST'])]
    public function process(Request $request): Response
    {
        $inputText = $request->request->get('user_input');
        $response = $this->guessResponse($inputText);

        return new Response($response);
    }

    #[Route('/chatbot/clear', name: 'chatbot_clear', methods: ['POST'])]
    public function clearConversation(): Response
    {
        return new Response();
    }

    private function guessResponse(string $input): string
    {
        $input = strtolower($input);

        $keywordResponses = [
            "" => "...",
            "hi" => "Hello!",
            "hello" => "Hi there!",
            "hey" => "Hey!",
            "howdy" => "Howdy!",
            "greetings" => "Greetings!",
            "good morning" => "Good morning!",
            "good afternoon" => "Good afternoon!",
            "good evening" => "Good evening!",
            "good day" => "Good day!",
            "morning" => "Good morning!",
            "afternoon" => "Good afternoon!",
            "evening" => "Good evening!",
            "night" => "Good night!",
            "yo" => "Yo!",
            "sahbi frere khoya" => "frero",
            "sup" => "What's up?",
            "wtf" => "What the fuck, Oh shit!",
            "what's up" => "Not much, you?",
            "how's it going" => "It's going well, thanks!",
            "how are you" => "I'm just a bot, but I'm doing well, thanks for asking!",
            "nawara amine yahyaoui Siliana" => "Siliana",
            "hadil sediri jandouba" => "Jandouba",
            "akram louati Sfax" => "Sfax",
            "yassine Zayane Monastir" => "Monastir",
            "kaboubi rafraf" => "rafraf",

            "time temp wa9t wakt heure hours minutes d9aye9" => "The current time is " . date("H:i"),
            "date" => "Today's date is " . date("Y-m-d"),
            "day nhar lyoum aujourdhui " => "Today is " . date("l"),
            "tomorrow ghodwa demain" => "Tomorrow's date is " . date("Y-m-d", strtotime("+1 day")),
            "yesterday berah emes" => "Yesterday's date was " . date("Y-m-d", strtotime("-1 day")),
            "weekday" => "Today is a weekday.",
            "weekend" => "Today is a weekend.",
            "next week jom3a jeya" => "Next week starts on " . date("l", strtotime("+1 week")),
            "last week jom3a fetet" => "Last week started on " . date("l", strtotime("-1 week")),
            "next month chhar jey" => "Next month is " . date("F", strtotime("+1 month")),
            "last month chhar fet" => "Last month was " . date("F", strtotime("-1 month")),
            "next year 3am jey" => "Next year is " . date("Y", strtotime("+1 year")),
            "last year 3amnewel" => "Last year was " . date("Y", strtotime("-1 year")),
            "holiday" => "Today is a holiday!",
            "workday" => "Today is a workday.",

            "weather ta9s meteo" => "The current weather is sunny.",

            "job 5idma travail" => "Sorry, I'm just a chatbot and don't have job listings.",
            
            "animal 7ayawen" => "I love animals too!",
            "dog kalb" => "Dogs are great companions!",
            "cat katous" => "Cats are independent creatures!",
            
            "badword klem mirzi mauvais" => "Please be polite!",
            
            "how old are you kdeh omrik" => "I'm just a computer program, so I don't have an age.",
            "where are you from mnin enty" => "I exist in the digital world, so I don't have a physical location.",
            "what is your purpose chnia feyidtik f denya" => "My purpose is to assist you with tasks and provide information.",
            "do you have siblings aandek sghar" => "No, I'm a standalone entity, i dont produce childrens.",
            "what do you like to do  t7ib taamel" => "I enjoy helping users and learning new things!",
            "who are you chknk tqui toi" => "I'm a Baladity chatbot designed by skilled devs, to assist you with tasks and provide information.",
            "can you help me kifeh nijim n3awnek" => "Of course, I'm here to help. What do you need assistance with?",
            "what can you do  tijim taamel" => "I can answer questions, provide information, and assist you with various tasks.",
            "tell me a joke tayech nokta" => "Why don't scientists trust atoms? Because they make up everything!",
            "what is the meaning of life ma3na hayet" => "The meaning of life is subjective and can vary from person to person.",
            "tell me a fact fact" => "Did you know that the shortest war in history lasted only 38 minutes?",
            "are you a human enty bachar" => "No, I'm a chatbot powered by artificial intelligence.",
            "do you like movies aflem filem filme" => "I don't have personal preferences, but I can provide information about movies.",
            "what is your favorite color couleur alwen" => "As a chatbot, I don't have the ability to perceive colors.",
            "do you dream ti7lim" => "No, I don't have the ability to dream.",
            "what is your favorite food meka mofathla" => "I don't have preferences for food since I'm not capable of eating.",
            "tell me something interesting ahki hja tisla7 interessante" => "The world's largest desert is not the Sahara, but Antarctica.",
            "are you sentient enty monji" => "No, I'm not sentient. I'm a computer program designed to respond to input.",
            "what is the capital of France capital mtaa fransa" => "The capital of France is Paris.",
            "what is the population of China" => "As of the latest data, the population of China is over 1.4 billion people.",
            "how do you kifeh ti5dim" => "I work by processing input from users and providing predefined responses based on keywords.",
            "what languages do you speak chnia lo8tik" => "I can communicate in multiple languages, including English, French, and Arabic.",
            "me ena" => "you are a great person.",
            "chbik" => "chbini",
            "salem" => "wa alaykom salem",
            "chtaaml" => "nvalid f PI",
            "aloo" => "winek sahbi",
            "yaakoubi" => "om mtaa ommek ella ma *****",
            "ti cha3el" => "thaw",
            "waa" => "waywa",
            "ai" => "artificial intelligence",
            "yesine zayane" => "limhaf",
            "amine yahyaoui" => "nawara",
            "fedi wartetanni" => "admin",
            "mezri abdelaziz" => "prof java",
            "iheb aloui" => "prof java",
            "java" => "Java est un langage de programmation de haut niveau orienté objet créé par James Gosling et Patrick Naughton, employés de Sun Microsystems, avec le soutien de Bill Joy, présenté officiellement le 23 mai 1995 au SunWorld. La société Sun est rachetée en 2009 par la société Oracle qui détient et maintient désormais Java.",
            "esprit" => "se former autrement",
            "haha hhhh" => "ith7ak saha lik",
            "mama" => "mia",
            "feyda" => "f niya",
            "louati akram" => "Ingenieur a paris",
            "winek" => "wa9t bard kleni ooo",
            "cv ?" => "Hamdoullah",
            "blablabla" => "yizitchi m bleda",
            "chkon enty tqui toi" => "Chihimek Sahbi",
            "salut" => "Salut!",
            "bonjour" => "Bonjour!",
            "coucou" => "Coucou!",
            "ça va" => "Ça va bien, et toi?",
            "comment ça va" => "Ça va bien, et toi?",
            "ça roule" => "Ça roule!",
            "quoi de neuf" => "Pas grand chose, et toi?",
            "qui es-tu" => "Je suis un chatbot conçu pour vous aider avec les tâches et fournir des informations.",
            "peux-tu m'aider" => "Bien sûr, je suis là pour vous aider. De quoi avez-vous besoin d'aide?",
            "que peux-tu faire" => "Je peux répondre aux questions, fournir des informations et vous aider dans diverses tâches.",
            "raconte-moi une blague" => "Pourquoi les plongeurs plongent-ils toujours en arrière et jamais en avant? Parce que sinon ils tombent dans le bateau!",
            "quelle est la capitale de la France" => "La capitale de la France est Paris.",
            "quelle est la population de la Chine" => "Selon les dernières données, la population de la Chine est de plus de 1,4 milliard de personnes.",
            "comment fonctionnes-tu" => "Je fonctionne en traitant les entrées des utilisateurs et en fournissant des réponses prédéfinies basées sur des mots clés.",
            "quelles langues parles-tu" => "Je peux communiquer dans plusieurs langues, y compris l'anglais, le français et l'arabe.",
            "moi" => "vous êtes une personne géniale.",
            "123" => "ta7ya tounes",
            "what is baladity c quoi ce projet chnowa projet baladity baladia" => "baladity est un projet qui simplifie la gestions des municipalitees en tunisie, ainsi que la partie service client, en fait notre application est destinee au employee et les citoyens, actuallement nous sommes encore en cours de developpement, ce consontront sur le secteur ariana.",
            "devs membres developpeurs de baladity" => "louati akram, zayane yassine, yahyaoui amine, sediri hadil et kaboubi amine.",
            "fine nikel mrigel" => "great, how can i help",
            "bb" => "hobi rak",
            "m3alem rak yre the bos t le patron" => "thank you ya rayes",
        ];
        $keywordsHashSet = array_keys($keywordResponses);

        if (array_key_exists($input, $keywordResponses)) {
            return $keywordResponses[$input];
        }

        // similar_text
        $bestMatch = '';
        $highestSimilarity = 0;
        foreach ($keywordResponses as $keyword => $response) {
            $similarity = similar_text($input, $keyword);
            if ($similarity > $highestSimilarity) {
                $highestSimilarity = $similarity;
                $bestMatch = $keyword;
            }
        }
        return $keywordResponses[$bestMatch];

    }
}