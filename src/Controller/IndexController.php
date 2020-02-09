<?php

namespace App\Controller;

use App\Entity\Diary;
use App\Entity\Testimony;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Contracts\Translation\TranslatorInterface;

class IndexController extends AbstractController
{
    private $_serializer;

    public function __construct()
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->_serializer = new Serializer($normalizers, $encoders);
    }

    /**
     * @Route("/", name="index")
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function index(Request $request, TranslatorInterface $translator)
    {
        $diaryDefault = $translator->trans('diary.home', [], null, $request->getLocale());
        /** @var Diary $diary */
        $diary = $this->getDoctrine()->getRepository(Diary::class)->findByValidateAndActual();
        $testimony = $this->getDoctrine()->getRepository(Testimony::class)
            ->findBy(['isValidated' => true], ['id' => 'DESC'], 1);
        if (empty($diary)){
            $diary = null;
        }
        return $this->render('index/index.html.twig',[
            'intro' => $translator->trans('intro', [], null, $request->getLocale()),
            'introRed' => $translator->trans('intro.red', [], null, $request->getLocale()),
            'meetingTitle' => $translator->trans('meeting.quality.title', [], null, $request->getLocale()),
            'meetingText' => $translator->trans('meeting.quality', [], null, $request->getLocale()),
            'meetingRed' => $translator->trans('meeting.quality.red', [], null, $request->getLocale()),
            'ratio' => $translator->trans('ratio', [], null, $request->getLocale()),
            'securityTitle' => $translator->trans('security.title', [], null, $request->getLocale()),
            'securityText' => $translator->trans('security.text', [], null, $request->getLocale()),
            'securityGreen' => $translator->trans('security.green', [], null, $request->getLocale()),
            'securityRed' => $translator->trans('security.red', [], null, $request->getLocale()),
            'interactiveTitle' => $translator->trans('interactive.title', [], null, $request->getLocale()),
            'interactiveText' => $translator->trans('interactive.text', [], null, $request->getLocale()),
            'interactiveGreen' => $translator->trans('interactive.green', [], null, $request->getLocale()),
            'interactiveRed' => $translator->trans('interactive.red', [], null, $request->getLocale()),
            'diaryDefault' => $diaryDefault,
            'diary' => $translator->trans('diary', [], null, $request->getLocale()),
            'diaryValue' => $diary,
            'shareEvent' => $translator->trans('share.event', [], null, $request->getLocale()),
            'location' => $translator->trans('location', [], null, $request->getLocale()),
            'date' => $translator->trans('date', [], null, $request->getLocale()),
            'readMore' => $translator->trans('read.more', [], null, $request->getLocale()),
            'testimony' => $testimony,
            'testimonyLink' => $translator->trans('testimony.link', [], null, $request->getLocale())
        ]);
    }

    /**
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return JsonResponse
     * @Route("/api/footer", name="api_footer", methods={"GET"})
     */
    public function getFooter(Request $request, TranslatorInterface $translator){
        $links = [
            'home' => $translator->trans('home.link', [], null, $request->getLocale()),
            'diary' => $translator->trans('diary', [], null, $request->getLocale()),
            'faq' => 'FAQ',
            'testimony' => $translator->trans('testimony.link', [], null, $request->getLocale()),
            'contact' => $translator->trans('contact', [], null, $request->getLocale()),
            'cgu' => $translator->trans('cgu', [], null, $request->getLocale()),
            'press' => $translator->trans('press.link', [], null, $request->getLocale()),
            'add' => $translator->trans('add', [], null, $request->getLocale()),
            'payment' => $translator->trans('payment', [], null, $request->getLocale()),
            'follow' => $translator->trans('follow', [], null, $request->getLocale()),
            'sub' => $translator->trans('baseline', [], null, $request->getLocale()) . ' ' . $translator->trans('baseline.red', [], null, $request->getLocale()),
            'letTestimony' => $translator->trans('let.testimony', [], null, $request->getLocale()),
            'goShop' => $translator->trans('go.shop', [], null, $request->getLocale()),
            'rate' => $translator->trans('links.rate', [], null, $request->getLocale()),
            'subAndOption' => $translator->trans('sub.and.option', [], null, $request->getLocale())
        ];
        return $this->json($links);
    }

    /**
     * @param TranslatorInterface $translator
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/nav", name="api_get_nav", methods={"GET"})
     */
    public function getNavBar(TranslatorInterface $translator, Request $request){
        $links = [
            'home' => [
                'name' => $translator->trans('home.link', [], null, $request->getLocale()),
                'path' => '/'
            ],
            'testimony' => [
                'name' => $translator->trans('dashboard.link', [], null, $request->getLocale()),
                'path' => '/dashboard'
            ],
            'faq' => [
                'name' => 'FAQ',
                'path' => '/faq'
            ]
        ];
        $connection = [];
        if ($this->getUser()){
            $connection['path'] = '/logout';
            $connection['name'] = $translator->trans('logout.link', [], null, $request->getLocale());
        }
        else{
            $connection['path'] = '/login';
            $connection['name'] = $translator->trans('connection.link', [], null, $request->getLocale());
        }
        $lang = [
            'fr' => [
                'name' => 'fr',
            ],
            'de' => [
                'name' => 'de'
            ],
            'en' => [
                'name' => 'en'
            ],
            'selected' => $request->getLocale()
        ];

        $res = ['lang' => $lang, 'links' => $links, 'connection' => $connection];
        return $this->json($res);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/lang", name="api_set_local", methods={"POST"})
     */
    public function setLocal(Request $request){
        $data = $this->_serializer->decode($request->getContent(), 'json');
        $response = new JsonResponse();
        $response->headers->setCookie(Cookie::create('_locale', $data['lang']));
        $response->setJson((string)'ok');
        $response->send();

        return $response;
    }

    /**
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return JsonResponse
     * @Route("/api/talking/subscribe", name="api_talking_subscribe", methods={"GET"})
     */
    public function getTalkingThreat(Request $request, TranslatorInterface $translator){
        $data = [
            'first' => [
                $translator->trans('talking.threat.first', [], null, $request->getLocale()),
                $translator->trans('talking.threat.first.red', [], null, $request->getLocale())
                ],
            'firstButton' => [
                'lovely' => [
                    'value' => 'lovely',
                    'text' => $translator->trans('lovely', [], null, $request->getLocale())
                ],
                'friendly' => [
                    'value' => 'friendly',
                    'text' => $translator->trans('friendly', [], null, $request->getLocale())
                ],
                'both' => [
                    'value' => 'both',
                    'text' => $translator->trans('both', [], null, $request->getLocale())
                ]
            ],
            'second' => [
                $translator->trans('talking.threat.second', [], null, $request->getLocale()),
                $translator->trans('talking.threat.second.red', [], null, $request->getLocale())
            ],
            'secondButton' => [
                'daddy' => [
                    'value' => true,
                    'text' => $translator->trans('daddy', [], null, $request->getLocale())
                ],
                'mom' => [
                    'value' => false,
                    'text' => $translator->trans('mom', [], null, $request->getLocale())
                ]
            ],
            'third' => [
                $translator->trans('talking.threat.third', [], null, $request->getLocale()),
                $translator->trans('talking.threat.third.red', [], null, $request->getLocale())
            ],
            'thirdButton' => [
                'text' => $translator->trans('validate', [], null, $request->getLocale())
            ],
            'thirdError' => [
                'text' => $translator->trans('talking.threat.third.error', [], null, $request->getLocale())
            ],
            'fourth' => [
                'text' => [
                    $translator->trans('talking.threat.fourth', [], null, $request->getLocale()),
                    $translator->trans('talking.threat.fourth.red', [], null, $request->getLocale())
                ],
                'response' => [
                    $translator->trans('talking.threat.fourth.response', [], null, $request->getLocale())
                ],
                'labels' => [
                    'canton' => $translator->trans('talking.threat.fourth.label.canton', [], null, $request->getLocale()),
                    'city' => $translator->trans('talking.threat.fourth.label.city')
                ]
            ],
            'fifth' => [
                $translator->trans('talking.threat.fifth', [], null, $request->getLocale()),
                $translator->trans('talking.threat.fifth.red', [], null, $request->getLocale())
            ],
            'sixth' => [
                $translator->trans('talking.threat.sixth', [], null, $request->getLocale()),
                $translator->trans('talking.threat.sixth.red', [], null, $request->getLocale())
            ],
            'sixthButton' => [
                'text' => $translator->trans('validate', [], null, $request->getLocale())
            ],
            'seventh' => [
                $translator->trans('talking.threat.seventh', [], null, $request->getLocale()),
                $translator->trans('talking.threat.seventh.red', [], null, $request->getLocale()),
                $translator->trans('talking.threat.seventh.confirm', [], null, $request->getLocale())
            ],
            'seventhButton' => [
                'text' => $translator->trans('validate', [], null, $request->getLocale())
            ],
            'seventhError' => [
                'text' => $translator->trans('talking.threat.seventh.error', [], null, $request->getLocale())
            ],
            'final' => [
                $translator->trans('talking.threat.final', [], null, $request->getLocale())
            ],
            'back' => [
                $translator->trans('back', [], null, $request->getLocale())
            ],
            'alias' => [
                'bubble' => $translator->trans('talking.threat.alias', [], null, $request->getLocale()),
                'error' => $translator->trans('talking.threat.alias.error', [],null, $request->getLocale()),
                'placeholder' => $translator->trans('alias', [], null, $request->getLocale())
            ],
            'pattern' => $translator->trans('pattern', [], null, $request->getLocale()),
            'errorEmail' => $translator->trans('email.exists', [], null, $request->getLocale())
        ];
        return $this->json($data);
    }

    /**
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return JsonResponse
     * @throws \Exception
     * @Route("/api/diary", name="api_get_diary", methods={"GET"})
     */
    public function getDiary(Request $request, TranslatorInterface $translator){
        $em = $this->getDoctrine()->getRepository(Diary::class);
        $data = [];
        $date = new \DateTime('now');

        if ($lastDiary = $em->findByValidateAndActual()){
            foreach ($lastDiary as $diary){
                /** @var Diary $diary */
                if ($diary->getDate()->getTimestamp() > $date->getTimestamp()){
                    $data['readMore'] = $translator->trans('read.more', [], null, $request->getLocale());
                    $data['shareEvent'] = $translator->trans('share.event', [], null, $request->getLocale());
                    $data['text'] = $diary->getText();
                    $data['title'] = $diary->getTitle();
                    $data['img'] = $diary->getImg() ? $diary->getImg()->getId() : null;
                    $data['location'] = $diary->getLocation();
                    $data['date'] = $diary->getDate()->format('d/m/y');
                    $data['diary'] = $translator->trans('diary', [], null, $request->getLocale());
                }
                else{
                    $data['readMore'] = null;
                    $data['diary'] = $translator->trans('diary', [], null, $request->getLocale());
                    $data['title'] = null;
                    $data['text'] = $translator->trans('diary.home', [], null, $request->getLocale());
                    $data['shareEvent'] = $translator->trans('share.event', [], null, $request->getLocale());
                }
            }

            return $this->json($data, 200);
        }
        $data['readMore'] = null;
        $data['title'] = null;
        $data['diary'] = $translator->trans('diary', [], null, $request->getLocale());
        $data['text'] = $translator->trans('diary.home', [], null, $request->getLocale());
        $data['shareEvent'] = $translator->trans('share.event', [], null, $request->getLocale());

        return $this->json($data, 200);
    }

    /**
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return JsonResponse
     * @Route("/api/press", name="api_press", methods={"GET"})
     */
    public function getPress(Request $request, TranslatorInterface $translator){
        return $this->json(['press' => $translator->trans('press', [], null, $request->getLocale())]);
    }

    /**
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return JsonResponse
     * @Route("/api/baseline", name="api_baseline", methods={"GET"})
     */
    public function baseline(Request $request, TranslatorInterface $translator){
        $data = [
            'baseline' => [
                $translator->trans('baseline', [], null, $request->getLocale()),
                $translator->trans('baseline.red', [], null, $request->getLocale())
            ]
        ];
        return $this->json($data);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/api/lang", name="api_lang", methods={"GET"})
     */
    public function getCurrentLang(Request $request){
        return $this->json($request->getLocale());
    }
}
