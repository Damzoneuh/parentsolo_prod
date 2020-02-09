<?php

namespace App\Controller;


use App\Entity\Canton;
use App\Entity\Cities;
use App\Entity\Comment;
use App\Entity\Description;
use App\Entity\Diary;
use App\Entity\GeneratedVisit;
use App\Entity\Groups;
use App\Entity\Img;
use App\Entity\Messages;
use App\Entity\News;
use App\Entity\NewsLetter;
use App\Entity\Payment;
use App\Entity\Profil;
use App\Entity\Testimony;
use App\Entity\User;
use App\Entity\Visit;
use App\Service\MailingService;
use Ingenico\Connect\Sdk\Client;
use Ingenico\Connect\Sdk\CommunicatorConfiguration;
use Ingenico\Connect\Sdk\DefaultConnection;
use Ingenico\Connect\Sdk\Domain\Definitions\AmountOfMoney;
use Ingenico\Connect\Sdk\Domain\Definitions\Card;
use Ingenico\Connect\Sdk\Domain\Payment\CreatePaymentRequest;
use Ingenico\Connect\Sdk\Domain\Payment\Definitions\CardPaymentMethodSpecificInput;
use Ingenico\Connect\Sdk\Domain\Payment\Definitions\ThreeDSecure;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Yaml;
use Symfony\Contracts\Translation\TranslatorInterface;
use Ingenico\Connect\Sdk\Communicator;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index()
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        $imgs = $this->getDoctrine()->getRepository(Img::class)->findBy(['isValidated' => null]);
        $sub = 0;
        $man = 0;
        $woman = 0;
        if (count($users) > 0){
            foreach ($users as $user){
                if ($user->getSubscribe()){
                    $sub ++;
                }
                $user->getProfil()->getIsMan() ? $man ++ : $woman ++;
            }
        }
        $manPercent = round(($man/count($users)) * 100);
        $womanPercent = round(($woman/count($users)) * 100);

        return $this->render('admin/index.html.twig', [
            'users' => $users,
            'imgs' => $imgs,
            'sub' => $sub,
            'manPercent' => $manPercent,
            'womanPercent' => $womanPercent,
            'active' => $this->getDoctrine()->getRepository(User::class)->getActiveUsers(),
            'groups' => $this->getDoctrine()->getRepository(Groups::class)->findBy(['isValidated' => null]),
            'generatedVisit' => $this->getDoctrine()->getRepository(GeneratedVisit::class)->findAll(),
            'paymentRefused' => $this->getDoctrine()->getRepository(Payment::class)->getPaymentRefused(),
            'textToValidate' => $this->getDoctrine()->getRepository(User::class)->getTextToValidate()
        ]);
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/admin/regularisation/{id}", name="admin_regularisation")
     */
    public function regularisationForPayment($id){
        $em = $this->getDoctrine()->getManager();
        $payment = $em->getRepository(Payment::class)->find($id);

        $payment->setIsAccepted(true);
        $em->flush();

        return $this->redirectToRoute('admin');
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/admin/table/select", name="admin_table_select")
     */
    public function selectTable(Request $request){
        $table = array(
            'Activity',
            'Cook',
            'Eyes',
            'Hair',
            'HairStyle',
            'Hobbies',
            'Langages',
            'LifeStyle',
            'Movies',
            'Music',
            'Origin',
            'Outing',
            'Pets',
            'Profession',
            'Reading',
            'Religion',
            'Silhouette',
            'Smoke',
            'Sport',
            'Status',
            'Studies',
            'Size',
            'Temperament',
            'ChildGard',
            'Nationality'
        );

        $formChoices = [];

        foreach ($table as $row){
            $formChoices[$row] = $row;
        }

        $form = $this->createFormBuilder()
            ->add('table', ChoiceType::class, ['choices' => $formChoices])
            ->add('valider', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();
            //dump($data); die();
            return $this->redirect('/admin/put/' . $data['table']);
        }

        return $this->render('admin/selectTable.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @param Request $request
     * @param $table
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/admin/put/{table}", name="admin_put_table")
     */
    public function putInTable(Request $request, $table){
        $form = $this->createFormBuilder()
            ->add('nom_en', TextType::class)
            ->add('nom_de', TextType::class)
            ->add('nom_fr', TextType::class)
            ->add('valider', SubmitType::class);

        $tableForm = $form->getForm();
        $tableForm->handleRequest($request);

        if ($tableForm->isSubmitted() && $tableForm->isValid()){
            $data = $tableForm->getData();
            $table = '\App\Entity\\'.$table;
            //dump($table); die();
            $dbTable = new $table();
            $dbTable->setName($data['nom_en']);

            $en = Yaml::parseFile($this->getParameter('en.trans.file'));
            $fr = Yaml::parseFile($this->getParameter('fr.trans.file'));
            $de = Yaml::parseFile($this->getParameter('de.trans.file'));

            if (!array_key_exists($data['nom_en'], $en)){
                $enTrans = $en;
                $enTrans[$data['nom_en']] = $data['nom_en'];

                $frTrans = $fr;
                $frTrans[$data['nom_en']] = $data['nom_fr'];

                $deTrans = $de;
                $deTrans[$data['nom_en']] = $data['nom_de'];

                $yamlEn = Yaml::dump($enTrans);
                $yamlFr = Yaml::dump($frTrans);
                $yamlDe = Yaml::dump($deTrans);

                file_put_contents($this->getParameter('en.trans.file'), $yamlEn);
                file_put_contents($this->getParameter('de.trans.file'), $yamlDe);
                file_put_contents($this->getParameter('fr.trans.file'), $yamlFr);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($dbTable);
            $em->flush();

            return $this->redirectToRoute('admin_table_select');
        }

        return $this->render('admin/putInTable.html.twig', ['form' => $tableForm->createView()]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/admin/trans", name="admin_trans")
     */
    public function addTrans(Request $request){
        $form = $this->createFormBuilder()
            ->add('key', TextType::class)
            ->add('fr', TextType::class)
            ->add('de', TextType::class)
            ->add('en', TextType::class)
            ->add('submit', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();

            $en = Yaml::parseFile($this->getParameter('en.trans.file'));
            $fr = Yaml::parseFile($this->getParameter('fr.trans.file'));
            $de = Yaml::parseFile($this->getParameter('de.trans.file'));

            if (!array_key_exists($data['key'], $en)){
                $en[$data['key']] = $data['en'];
                $fr[$data['key']] = $data['fr'];
                $de[$data['key']] = $data['de'];

                $yamlEn = Yaml::dump($en);
                $yamlFr = Yaml::dump($fr);
                $yamlDe = Yaml::dump($de);

                file_put_contents($this->getParameter('en.trans.file'), $yamlEn);
                file_put_contents($this->getParameter('de.trans.file'), $yamlDe);
                file_put_contents($this->getParameter('fr.trans.file'), $yamlFr);

                return $this->redirect('/admin/trans');
            }
            return $this->render('admin/addtrans.html.twig', ['form' => $form->createView()]);
        }
        return $this->render('admin/addtrans.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @param MailingService $mailingService
     * @param TranslatorInterface $translator
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/admin/user/{id}", name="admin_user")
     * @throws \Exception
     */
    public function getUserProfile(MailingService $mailingService, TranslatorInterface $translator, Request $request,$id){
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);
        $parameterForm = $this->createFormBuilder()
            ->add('alias', TextType::class, [
                'data' => $user->getPseudo(),
                'label' => 'Pseudo',
                'required' => true
            ])
            ->add('gender', ChoiceType::class, [
                'label' => 'Genre',
                'choices' => [
                    'Homme' => true,
                    'Femme' => false
                ],
                'data' => $user->getProfil()->getIsMan() ? 'Homme' : 'Femme'
            ])
            ->add('name', TextType::class, [
                'data' => $user->getName(),
                'label' => 'Nom',
                'required' => false
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'data' => $user->getFirstName(),
                'required' => false
            ])
            ->add('address', TextType::class, [
                'data' => $user->getAddress(),
                'label' => 'Adresse',
                'required' => false
            ])
            ->add('npa', TextType::class, [
                'data' => $user->getNpa(),
                'label' => 'NPA',
                'required' => false
            ])
            ->add('phone', TextType::class, [
                'data' => $user->getPhone(),
                'label' => 'Téléphone'
            ])
            ->add('lang', ChoiceType::class, [
                'data' => $user->getLangForModeration(),
                'choices' => [
                    'Français' => 'FR',
                    'Anglais' => 'EN',
                    'Allemand' => 'DE'
                ],
                'label' => 'Langue',
                'required' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Valider',
            ])
            ->getForm();
        $parameterForm->handleRequest($request);

        if ($parameterForm->isSubmitted() && $parameterForm->isValid()){
            $data = $parameterForm->getData();
            $user->setPseudo($data['alias']);
            $user->getProfil()->setIsMan($data['gender']);
            $user->setName($data['name']);
            $user->setFirstName($data['firstName']);
            $user->setAddress($data['address']);
            $user->setNpa($data['npa']);
            $user->setPhone($data['phone']);
            $user->setLangForModeration($data['lang']);
            $em->flush();

            return $this->redirectToRoute('admin_user', ['id' => $user->getId()]);
        }
        $day = [];
        $month = [];
        $year = [];
        for ($i = 1; $i < 32; $i++){
            $day[$i] = $i;
        }
        for ($i = 1; $i < 13; $i++){
            $month[$i] = $i;
        }
        for ($i = 2020; $i < 2040; $i++){
            $year[$i] = $i;
        }
        $deadline = null;
        if ($user->getSubscribe()){
            $deadline = $user->getSubscribe()->getDeadline();
        }
        $subForm = $this->createFormBuilder()
            ->add('day', ChoiceType::class, [
                'data' => $deadline ? intval($deadline->format('d')) : '',
                'choices' => $day,
                'label' => 'Jour'
            ])
            ->add('month', ChoiceType::class, [
                'data' => $deadline ? intval($deadline->format('m')) : '',
                'choices' => $month,
                'label' => 'Mois',
            ])
            ->add('year', ChoiceType::class, [
                'data' => $deadline ? intval($deadline->format('Y')) : '',
                'choices' => $year,
                'label' => 'Année'
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Créditer'
            ])
            ->getForm();
        $subForm->handleRequest($request);

        if ($subForm->isSubmitted() && $subForm->isValid()){
            $data = $subForm->getData();
            $user->getSubscribe()->setDeadline(new \DateTime($data['year'] . '-' . $data['month'] . '-' . $data['day']));
            $em->flush();

            return $this->redirectToRoute('admin_user', ['id' => $user->getId()]);
        }

        $commentForm = $this->createFormBuilder()
            ->add('comment', TextareaType::class, [
                'label' => 'Commentaire'
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Valider'
            ])
            ->getForm();
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()){
            $data = $commentForm->getData();
            $comment = new Comment();
            $comment->setText($data['comment']);
            $comment->addUser($user);
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('admin_user', ['id' => $id]);
        }

        $descriptionForm = $this->createFormBuilder()
            ->add('text', TextareaType::class, [
                'label' => 'description',
                'data' => $user->getProfil()->getDescription() ?  $user->getProfil()->getDescription()->getText() : ''
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Valider'
            ])
            ->getForm();
        $descriptionForm->handleRequest($request);

        if ($descriptionForm->isSubmitted() && $descriptionForm->isValid()){
            $data = $descriptionForm->getData();
            if ($user->getProfil()->getDescription()){
                $user->getProfil()->getDescription()->setText($data['text']);
                $user->getProfil()->getDescription()->setIsValidated(true);
                $em->flush();
                return $this->redirectToRoute('admin_user', ['id' => $id]);
            }

            $description = new Description();
            $description->setText($data['text']);
            $description->setIsValidated(true);
            $em->persist($description);
            $user->getProfil()->setDescription($description);
            $em->flush();

            return $this->redirectToRoute('admin_user', ['id' => $id]);
        }
        
        return $this->render('admin/user.html.twig', [
            'user' => $user,
            'parameterForm' => $parameterForm->createView(),
            'subForm' => $subForm->createView(),
            'commentForm' => $commentForm->createView(),
            'comments' => $user->getComments(),
            'descriptionForm' => $descriptionForm->createView()
        ]);
    }

    /**
     * @param MailingService $mailingService
     * @param TranslatorInterface $translator
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/admin/img/{id}/validate", name="admin_img_validate")
     */
    public function validateImg(MailingService $mailingService, TranslatorInterface $translator, $id){
        $em = $this->getDoctrine()->getManager();
        $img = $em->getRepository(Img::class)->find($id);
        if ($img){
            $img->setIsValidated(true);
            $em->flush();
            if ($img->getUser() && $img->getUser()->getIsNotified()){
                $mailingService->sendAdminMail($img->getUser(),
                    $translator->trans('img.accepted', [], null, $img->getUser()->getLangForModeration() ?
                        $img->getUser()->getLangForModeration() : 'fr'));
            }
        }

        return $this->redirectToRoute('admin');
    }

    /**
     * @param MailingService $mailingService
     * @param TranslatorInterface $translator
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/admin/img/{id}/refuse", name="admin_img_refuse")
     */
    public function refuseImg(MailingService $mailingService, TranslatorInterface $translator, $id){
        $em = $this->getDoctrine()->getManager();
        $img = $em->getRepository(Img::class)->find($id);
        if ($img){
            $img->setIsValidated(false);
            $em->flush();
            if ($img->getUser() && $img->getUser()->getIsNotified()){
                $mailingService->sendAdminMail($img->getUser(),
                    $translator->trans('img.refused', [], null, $img->getUser()->getLangForModeration() ?
                        $img->getUser()->getLangForModeration() : 'FR'));
            }
        }
        return $this->redirectToRoute('admin');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/admin/stats", name="admin_stats")
     */
    public function adminStats(){
        //TODO fonctionnel
        return $this->redirectToRoute('admin');
    }

    /**
     * @param $id
     * @param $validate
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/admin/call/{validate}/{id}", name="admin_call")
     */
    public function callValidation($id, $validate){
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);
        $user->setIsCalled($validate);
        $em->flush();

        return $this->redirectToRoute('admin_user', ['id' => $id]);
    }

    /**
     * @param $id
     * @param $validate
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/admin/account/{id}/{validate}", name="admin_account_validate")
     */
    public function validateAccount($id, $validate){
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);
        $user->setIsValidated($validate);
        $em->flush();

        return $this->redirectToRoute('admin_user', ['id' => $id]);
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/admin/subscribe/{id}/delete", name="admin_delete_subscribe")
     */
    public function deleteSub($id){
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);
        if ($user->getSubscribe()){
            $em->remove($user->getSubscribe());
            $em->flush();
            $user->setRoles(["ROLE_USER"]);
            $em->flush();
        }
        return $this->redirectToRoute('admin');
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/admin/description/refuse/{id}", name="admin_description_refuse")
     */
    public function refuseDescription($id){
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);
        $user->getProfil()->getDescription()->setIsValidated(false);
        $em->flush();

        return $this->redirectToRoute('admin_user', ['id' => $id]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/admin/users/all", name="admin_users_all")
     */
    public function getUsers(){
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        return $this->render('admin/admin-users.html.twig', ['users' => $users]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/admin/testimonies", name="admin_testimonies")
     */
    public function getTestimonies(){
        return $this->render('admin/testimonies.html.twig', [
            'testimonies' => $this->getDoctrine()->getRepository(Testimony::class)->findAll()
        ]);
    }

    /**
     * @param $id
     * @param $validate
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/admin/testimony/{id}/{validate}", name="admin_testimony_validate")
     */
    public function validateTestimony($id, $validate){
        $em = $this->getDoctrine()->getManager();
        $testimony = $em->getRepository(Testimony::class)->find($id);
        if ($testimony){
            $testimony->setIsValidated($validate);
            $em->flush();
        }

        return $this->redirectToRoute('admin_testimonies');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/admin/groups", name="admin_groups")
     */
    public function getGroups(){
        return $this->render('admin/groups.html.twig', [
            'groups' => $this->getDoctrine()->getRepository(Groups::class)->findAll()
        ]);
    }

    /**
     * @param $id
     * @param $validate
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/admin/group/validate/{id}/{validate}", name="admin_group_validate")
     */
    public function validateGroup($id, $validate){
        $em = $this->getDoctrine()->getManager();
        $group = $em->getRepository(Groups::class)->find($id);
        if ($group){
            $group->setIsValidated($validate);
            $em->flush();
        }
        return $this->redirectToRoute('admin_groups');
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/admin/news", name="admin_news")
     */
    public function adminNews(Request $request){
        $newsForm = $this->createFormBuilder()
            ->add('title', TextType::class, [
                'label' => 'titre',
            ])
            ->add('text', TextareaType::class, [
                'label' => 'texte'
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Valider'
            ])
            ->getForm();
        $newsForm->handleRequest($request);

        if ($newsForm->isSubmitted() && $newsForm->isValid()){
            $data = $newsForm->getData();
            $em = $this->getDoctrine()->getManager();
            $new = new News();
            $new->setText($data['text']);
            $new->setTitle($data['title']);
            $new->setIsActive(false);
            $em->flush();

            return $this->redirectToRoute('admin_news');
        }

        return $this->render('admin/news.html.twig', [
            'news' => $this->getDoctrine()->getRepository(News::class)->findAll(),
            'newsForm' => $newsForm->createView()
        ]);
    }

    /**
     * @param $id
     * @param $validate
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/admin/new/validate/{id}/{validate}", name="admin_new_validate")
     */
    public function newsValidate($id, $validate){
        $em = $this->getDoctrine()->getManager();
        $new = $em->getRepository(News::class)->find($id);
        if ($new){
            $new->setIsActive($validate);
            $em->flush();
        }

        return $this->redirectToRoute('admin_news');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/admin/diary", name="admin_diary")
     */
    public function diary(){
        return $this->render('admin/diary.html.twig', [
            'diaries' => $this->getDoctrine()->getRepository(Diary::class)->findAll()
        ]);
    }

    /**
     * @param $id
     * @param $validate
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/admin/diary/{id}/{validate}", name="admin_diary_validate")
     */
    public function validateDiary($id, $validate){
        $em = $this->getDoctrine()->getManager();
        $diary = $em->getRepository(Diary::class)->find($id);
        if ($diary){
            $diary->setIsValidate($validate);
            $em->flush();
        }

        return $this->redirectToRoute('admin_diary');
    }

    /**
     * @param Request $request
     * @Route("/admin/newsletter", name="admin_newsletter")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function sendNewsletter(Request $request){
        $newsLetterForm = $this->createFormBuilder()
            ->add('titleFr', TextType::class, [
                'label' => 'titre en Francais'
            ])
            ->add('textFr', TextareaType::class, [
                'label' => 'Texte en Francais'
            ])
            ->add('titleEn', TextType::class, [
                'label' => 'titre en Anglais'
            ])
            ->add('textEn', TextareaType::class, [
                'label' => 'Texte en Anglais'
            ])
            ->add('titleDe', TextType::class, [
                'label' => 'titre en Allemand'
            ])
            ->add('textDe', TextareaType::class, [
                'label' => 'Texte en Allemand'
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer'
            ])
            ->getForm();

        $newsLetterForm->handleRequest($request);

        if ($newsLetterForm->isSubmitted() && $newsLetterForm->isValid()){
            $data = $newsLetterForm->getData();
            $em = $this->getDoctrine()->getManager();
            $newsLetter = new NewsLetter();
            $newsLetter->setFrTitle($data['titleFr']);
            $newsLetter->setFrText($data['textFr']);
            $newsLetter->setDeTitle($data['titleDe']);
            $newsLetter->setDeText($data['textDe']);
            $newsLetter->setEnTitle($data['titleEn']);
            $newsLetter->setEnText($data['textEn']);
            $em->persist($newsLetter);
            $em->flush();
            \App\Async\NewsLetter::sendNewsLetter($newsLetter->getId());

            return $this->redirectToRoute('admin_newsletter');
        }

        return $this->render('admin/newsletter.html.twig', [
            'form' => $newsLetterForm->createView()
        ]);
    }

    /**
     * @param Request $request
     * @param MailingService $mailingService
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/admin/message", name="admin_message")
     */
    public function generateMessage(Request $request, MailingService $mailingService){
        $em = $this->getDoctrine()->getManager();
        $cantons = $em->getRepository(Canton::class)->findAll();
        $users = $em->getRepository(User::class)->findAll();
        $cantonChoices = [];
        $helvetica = [];
        foreach ($users as $user){
            if(in_array("ROLE_HELVETICA", $user->getRoles())){
                $gender =  $user->getProfil()->getIsMan() ? '(h)' : '(f)';
                    $helvetica[$user->getPseudo() . ' ' . $gender] = $user->getId();
            }
        }
        foreach ($cantons as $canton){
            $cantonChoices[$canton->getName()] =  $canton->getId();
        }

        $form = $this->createFormBuilder()
            ->add('profile', ChoiceType::class, [
                'choices' => $helvetica,
                'label' => 'Profil',
            ])
            ->add('canton', ChoiceType::class, [
                'choices' => $cantonChoices,
                'label' => 'canton',
                'required' => false,
                'empty_data' => null
            ])
            ->add('child', ChoiceType::class, [
                'choices' => [
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4' => 4
                ],
                'label' => 'Nombre d\'enfants',
                'required' => false,
                'empty_data' => null,
            ])
            ->add('gender', ChoiceType::class, [
                'choices' => [
                    'Homme' => true,
                    'Femme' => false
                ],
                'label' => 'Genre',
                'required' => false,
                'empty_data' => null
            ])
            ->add('isSub', ChoiceType::class, [
                'choices' => [
                    'Abonné' => true,
                    'Non abonné' => false
                ],
                'label' => 'abonné',
                'required' => false,
                'empty_data' => null
            ])
            ->add('haveImg', ChoiceType::class, [
                'choices' => [
                    'Oui' => true,
                    'false' => false
                ],
                'label' => 'A une photo de profil',
                'required' => false,
                'empty_data' => null
            ])
            ->add('text', TextareaType::class, [
                'label' => 'Texte'
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer'
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();
            $targets = [];
            foreach ($users as $user){
                if ($data['canton'] && $data['canton'] == $user->getProfil()->getCity()->getCanton()->getId()){
                    if (!array_key_exists($user->getId(), $targets)) {
                        $targets[$user->getId()] =$user;
                    }
                }
                if (!$data['canton']){
                    if (!array_key_exists($user->getId(), $targets)) {
                        $targets[$user->getId()] =$user;
                    }
                }
                if ($data['child'] && $data['child'] == $user->getProfil()->getChilds()->count()){
                    if (!array_key_exists($user->getId(), $targets)) {
                        $targets[$user->getId()] =$user;
                    }
                }
                if (!$data['child']){
                    if (!array_key_exists($user->getId(), $targets)) {
                        $targets[$user->getId()] =$user;
                    }
                }
                if ($data['isSub'] === null){
                    if (!array_key_exists($user->getId(), $targets)) {
                        $targets[$user->getId()] =$user;
                    }
                }
                if ($data['isSub'] !== null && $data['isSub'] === true && $user->getSubscribe()){
                    if (!array_key_exists($user->getId(), $targets)) {
                        $targets[$user->getId()] =$user;
                    }
                }
                if ($data['isSub'] !== null && $data['isSub'] === false && !$user->getSubscribe()){
                    if (!array_key_exists($user->getId(), $targets)) {
                        $targets[$user->getId()] =$user;
                    }
                }

                if ($data['haveImg'] && $user->getImg()->count() > 0){
                    if (!array_key_exists($user->getId(), $targets)) {
                        $targets[$user->getId()] =$user;
                    }
                }
                if ($data['haveImg'] !== null && $data['haveImg'] === false && $user->getImg()->count() == 0){
                    if (!array_key_exists($user->getId(), $targets)) {
                        $targets[$user->getId()] =$user;
                    }
                }

                if ($data['haveImg'] === null){
                    if (!array_key_exists($user->getId(), $targets)) {
                        $targets[$user->getId()] =$user;
                    }
                }

                if ($data['gender'] === null){
                    if (!array_key_exists($user->getId(), $targets)) {
                        $targets[$user->getId()] =$user;
                    }
                }

                if ($data['gender'] !== null && $data['gender'] === true && $user->getProfil()->getIsMan()){
                    if ($data['gender'] === null){
                        if (!array_key_exists($user->getId(), $targets)){
                        $targets[$user->getId()] =$user;
                        }
                    }
                }
                if ($data['gender'] !== null && $data['gender'] === false && !$user->getProfil()->getIsMan()){
                    if ($data['gender'] === null){
                        if (!array_key_exists($user->getId(), $targets)) {
                        $targets[$user->getId()] =$user;
                        }
                    }
                }
            }
            
            $profile  = $em->getRepository(User::class)->find($data['profile']);
            if (count($targets) > 0){
                foreach ($targets as $t){
                    if ($t->getIsNotified()){
                        $mailingService->sendMessageReceived($profile, $t);
                    }
                    $messages = $em->getRepository(Messages::class)->findBy(['messageFrom' => $profile->getId(), 'messageTo' => $t->getId()]);
                    if (count($messages) > 0){
                        foreach ($messages as $message){
                            $message->setIsClose(false);
                            $em->flush();
                        }
                    }

                    $messages = $em->getRepository(Messages::class)->findBy(['messageFrom' => $t->getId(), 'messageTo' => $profile->getId()]);
                    if (count($messages) > 0){
                        foreach ($messages as $message){
                            $message->setIsClose(false);
                            $em->flush();
                        }
                    }
                    $message = new Messages();
                    $text = str_replace("@user", $t->getPseudo(), $data['text']);
                    $message->setContent($text);
                    $message->setMessageFrom($profile->getId());
                    $message->setMessageTo($t->getId());
                    $message->setIsRead(false);
                    $em->persist($message);
                    $em->flush();
                }
            }

            return $this->redirectToRoute('admin_message');
        }

        return $this->render('admin/messages.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @param MailingService $mailingService
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     * @Route("/admin/generated/visit", name="admin_generated_visit")
     */
    public function generateVisit(Request $request, MailingService $mailingService){
        $em = $this->getDoctrine()->getManager();
        $cantons = $em->getRepository(Canton::class)->findAll();
        $users = $em->getRepository(User::class)->findAll();
        $cantonChoices = [];
        $helvetica = [];
        foreach ($users as $user){
            if(in_array("ROLE_HELVETICA", $user->getRoles())){
                $gender =  $user->getProfil()->getIsMan() ? '(h)' : '(f)';
                $helvetica[$user->getPseudo() . ' ' . $gender] = $user->getId();
            }
        }
        foreach ($cantons as $canton){
            $cantonChoices[$canton->getName()] =  $canton->getId();
        }

        $form = $this->createFormBuilder()
            ->add('profile', ChoiceType::class, [
                'choices' => $helvetica,
                'label' => 'Profil',
            ])
            ->add('canton', ChoiceType::class, [
                'choices' => $cantonChoices,
                'label' => 'canton',
                'required' => false,
                'empty_data' => null
            ])
            ->add('child', ChoiceType::class, [
                'choices' => [
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4' => 4
                ],
                'label' => 'Nombre d\'enfants',
                'required' => false,
                'empty_data' => null,
            ])
            ->add('gender', ChoiceType::class, [
                'choices' => [
                    'Homme' => true,
                    'Femme' => false
                ],
                'label' => 'Genre',
                'required' => false,
                'empty_data' => null
            ])
            ->add('isSub', ChoiceType::class, [
                'choices' => [
                    'Abonné' => true,
                    'Non abonné' => false
                ],
                'label' => 'abonné',
                'required' => false,
                'empty_data' => null
            ])
            ->add('haveImg', ChoiceType::class, [
                'choices' => [
                    'Oui' => true,
                    'false' => false
                ],
                'label' => 'A une photo de profil',
                'required' => false,
                'empty_data' => null
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Générer'
            ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $targets = [];
            foreach ($users as $user) {
                if ($data['canton'] && $data['canton'] == $user->getProfil()->getCity()->getCanton()->getId()) {
                    if (!array_key_exists($user->getId(), $targets)){
                        $targets[$user->getId()] = $user;
                    }
                }
                if (!$data['canton']) {
                    if (!array_key_exists($user->getId(), $targets)) {
                       $targets[$user->getId()] = $user;
                    }
                }
                if ($data['child'] && $data['child'] == $user->getProfil()->getChilds()->count()) {
                    if (!array_key_exists($user->getId(), $targets)) {
                        $targets[$user->getId()] = $user;
                    }
                }
                if (!$data['child']) {
                    if (!array_key_exists($user->getId(), $targets)) {
                       $targets[$user->getId()] = $user;
                    }
                }
                if ($data['isSub'] === null) {
                    if (!array_key_exists($user->getId(), $targets)) {
                       $targets[$user->getId()] = $user;
                    }
                }
                if ($data['isSub'] !== null && $data['isSub'] === true && $user->getSubscribe()) {
                    if (!array_key_exists($user->getId(), $targets)) {
                        $targets[$user->getId()] = $user;
                    }
                }
                if ($data['isSub'] !== null && $data['isSub'] === false && !$user->getSubscribe()) {
                    if (!array_key_exists($user->getId(), $targets)) {
                        $targets[$user->getId()] = $user;
                    }
                }

                if ($data['haveImg'] && $user->getImg()->count() > 0) {
                    if (!array_key_exists($user->getId(), $targets)) {
                       $targets[$user->getId()] = $user;
                    }
                }
                if ($data['haveImg'] !== null && $data['haveImg'] === false && $user->getImg()->count() == 0) {
                    if (!array_key_exists($user->getId(), $targets)) {
                       $targets[$user->getId()] = $user;
                    }
                }

                if ($data['haveImg'] === null) {
                    if (!array_key_exists($user->getId(), $targets)) {
                       $targets[$user->getId()] = $user;
                    }
                }

                if ($data['gender'] === null) {
                    if (!array_key_exists($user->getId(), $targets)) {
                       $targets[$user->getId()] = $user;
                    }
                }

                if ($data['gender'] !== null && $data['gender'] === true && $user->getProfil()->getIsMan()) {
                    if ($data['gender'] === null) {
                        if (!array_key_exists($user->getId(), $targets)) {
                           $targets[$user->getId()] = $user;
                        }
                    }
                }
                if ($data['gender'] !== null && $data['gender'] === false && !$user->getProfil()->getIsMan()) {
                    if ($data['gender'] === null) {
                        if (!array_key_exists($user->getId(), $targets)) {
                            $targets[$user->getId()] = $user;
                        }
                    }
                }
            }

            $profile = $em->getRepository(User::class)->find($data['profile']);
            if (count($targets) > 0) {
                /** @var User $t */
                foreach ($targets as $t) {
                    if ($t->getVisits()->count() > 0){
                        /** @var Visit $v */
                        $isVisited = false;
                        $oldVisit = null;
                        foreach ($t->getVisits()->getvalues() as $v){
                            if ($v->getVisitor()->getId() === $profile->getId()){
                                $isVisited = true;
                                $oldVisit = $v;
                            }
                        }
                        if (!$isVisited){
                            $visit = new Visit();
                            $visit->setVisitor($profile);
                            $visit->setDate(new \DateTime('now'));
                            $em->persist($visit);
                            $t->addVisit($visit);
                            $em->flush();
                        }
                        else{
                            $oldVisit->setDate(new \DateTime('now'));
                            $em->flush();
                        }
                    }
                    if ($t->getIsNotified()){
                        $mailingService->sendNotification($t, $profile, 'Visite', 'A visité votre profil', 'visit');
                    }
                }
            }
            $generatedVisit = new GeneratedVisit();
            $generatedVisit->setDate(new \DateTime('now'));
            $generatedVisit->addProfil($profile);
            $em->persist($generatedVisit);
            $em->flush();
            return $this->redirectToRoute('admin_generated_visit');
        }
        $visits = $this->getDoctrine()->getRepository(GeneratedVisit::class)->getOrderByLast();

        if (count($visits) > 0){
            /**
             * @var int $key
             * @var GeneratedVisit $visit
             */
            $previous = null;
            foreach ($visits as $key => $visit){
                if ($previous && $visit->getDate() == $previous){
                    unset($visits[$key]);
                    $previous = null;
                }
                $previous = $visit->getDate();
            }
        }
        return $this->render('admin/generated-visit.html.twig', [
            'form' => $form->createView(),
            'visits' => $visits
        ]);
    }

//    /**
//     * @param MailingService $mailingService
//     * @param TranslatorInterface $translator
//     * @return \Symfony\Component\HttpFoundation\Response
//     * @Route("/email", name="admin_email")
//     */
//    public function seeMail(MailingService $mailingService, TranslatorInterface $translator){
//        $em = $this->getDoctrine()->getConnection();
//        $sql = 'insert into parentsolo.canton (id, name, code) values (2, \'Vaud\', 1);
//insert into parentsolo.canton (id, name, code) values (3, \'Genève\', 2);
//insert into parentsolo.canton (id, name, code) values (4, \'Neuchâtel\', 3);
//insert into parentsolo.canton (id, name, code) values (5, \'Fribourg\', 4);
//insert into parentsolo.canton (id, name, code) values (6, \'Jura\', 5);
//insert into parentsolo.canton (id, name, code) values (7, \'Valais\', 6);
//insert into parentsolo.canton (id, name, code) values (8, \'Tessin\', 7);
//insert into parentsolo.canton (id, name, code) values (9, \'Grisons\', 8);
//insert into parentsolo.canton (id, name, code) values (10, \'Berne\', 9);
//insert into parentsolo.canton (id, name, code) values (11, \'Appenzell Rhodes-Extérieures\', 10);
//insert into parentsolo.canton (id, name, code) values (12, \'Appenzell Rhodes-Intérieures\', 11);
//insert into parentsolo.canton (id, name, code) values (13, \'Argovie\', 12);
//insert into parentsolo.canton (id, name, code) values (14, \'Bâle-Campagne\', 13);
//insert into parentsolo.canton (id, name, code) values (15, \'Bâle-Ville\', 14);
//insert into parentsolo.canton (id, name, code) values (16, \'Glaris\', 15);
//insert into parentsolo.canton (id, name, code) values (17, \'Lucerne\', 16);
//insert into parentsolo.canton (id, name, code) values (18, \'Nidwald\', 17);
//insert into parentsolo.canton (id, name, code) values (19, \'Obwald\', 18);
//insert into parentsolo.canton (id, name, code) values (20, \'Saint-Gall\', 19);
//insert into parentsolo.canton (id, name, code) values (21, \'Schaffhouse\', 20);
//insert into parentsolo.canton (id, name, code) values (22, \'Schwytz\', 21);
//insert into parentsolo.canton (id, name, code) values (23, \'Soleure\', 22);
//insert into parentsolo.canton (id, name, code) values (24, \'Thurgovie\', 23);
//insert into parentsolo.canton (id, name, code) values (25, \'Uri\', 24);
//insert into parentsolo.canton (id, name, code) values (26, \'Zoug\', 25);
//insert into parentsolo.canton (id, name, code) values (27, \'Zurich\', 26);';
//        $stmt = $em->prepare($sql);
//        $stmt->execute([]);
  //      $token = 'kjfedhsikfjbhkswjdfbksjbf';
//        /** @var User $user */
//        $user = $this->getUser();
//        return $this->render('email/register.html.twig',[
//            'user' => $user,
//            'validate' => $translator->trans('validate', [], null, strtolower($user->getLangForModeration())),
//            'content' => $translator->trans('register.text', [], null, strtolower($user->getLangForModeration())),
//            'token' => $token,
//            'title' => $translator->trans('register.title', [], null, strtolower($user->getLangForModeration())),
//            'links' => [
//                'testimony' => $translator->trans('testimony.link', [], null, strtolower($user->getLangForModeration())),
//                'diary' => $translator->trans('diary', [], null, strtolower($user->getLangForModeration())),
//                'first' => $translator->trans('text.email.first', [], null, strtolower($user->getLangForModeration())),
//                'second' => $translator->trans('text.email.second', [], null, strtolower($user->getLangForModeration()))
//            ]
//        ]);
//        return $this->json($stmt->fetchAll());
//    }

}
