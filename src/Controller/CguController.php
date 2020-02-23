<?php

namespace App\Controller;

use App\Entity\Cgu;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Yaml;
use Symfony\Contracts\Translation\TranslatorInterface;

class CguController extends AbstractController
{
    /**
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/cgu", name="cgu")
     */
    public function index(Request $request, TranslatorInterface $translator)
    {
        $cgus = $this->getDoctrine()->getRepository(Cgu::class)->findAll();
        $rows = [];
        if ($cgus){
            foreach ($cgus as $cgu){
                array_push($rows, [
                    'title' => $translator->trans($cgu->getTitle(), [], null, $request->getLocale()),
                    'text' => $translator->trans($cgu->getText(), [], null, $request->getLocale())
                ]);
            }
        }
        return $this->render('cgu/index.html.twig', [
            'trans' => $translator->trans('cgu', [], null, $request->getLocale()),
            'cgus' => $rows
        ]);
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/admin/cgu", name="admin_cgu")
     */
    public function createField(Request $request){
        $em = $this->getDoctrine()->getManager();
        $cgus = $em->getRepository(Cgu::class)->findAll();

        $form = $this->createFormBuilder()
            ->add('title_fr', TextType::class)
            ->add('title_en', TextType::class)
            ->add('title_de', TextType::class)
            ->add('text_fr', TextareaType::class)
            ->add('text_en', TextareaType::class)
            ->add('text_de', TextareaType::class)
            ->add('submit', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $fr = Yaml::parseFile($this->getParameter('fr.trans.file'));
            $de = Yaml::parseFile($this->getParameter('de.trans.file'));
            $en = Yaml::parseFile($this->getParameter('en.trans.file'));

            $data = $form->getData();

            if (!array_key_exists($data['text_en'], $en) && !array_key_exists($data['title_en'], $en)){
                $en[$data['title_en']] = $data['title_en'];
                $en[$data['text_en']] = $data['text_en'];
                $fr[$data['title_en']] = $data['title_fr'];
                $fr[$data['text_en']] = $data['text_fr'];
                $de[$data['title_en']] = $data['title_de'];
                $de[$data['text_en']] = $data['text_de'];

                $dumpFr = Yaml::dump($fr);
                $dumpDe = Yaml::dump($de);
                $dumpEn = Yaml::dump($en);

                file_put_contents($this->getParameter('en.trans.file'), $dumpEn);
                file_put_contents($this->getParameter('fr.trans.file'), $dumpFr);
                file_put_contents($this->getParameter('de.trans.file'), $dumpDe);

                $cgu = new Cgu();
                $cgu->setTitle($data['title_en']);
                $cgu->setText($data['text_en']);
                $em->persist($cgu);
                $em->flush();

                $this->addFlash('success', 'CGU ajoutée');

                return $this->redirectToRoute('admin_cgu');
            }
            $this->addFlash('error', 'La clé de traduction n\'est pas disponible');
            return $this->redirectToRoute('admin_cgu');
        }

        return $this->render('cgu/admin.html.twig', ['form' => $form->createView(), 'cgus' => $cgus]);
    }

    /**
     * @param $id
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/admin/cgu/{id}", name="admin_edit_cgu")
     */
    public function editCgu($id, Request $request, TranslatorInterface $translator){
        $cgu = $this->getDoctrine()->getRepository(Cgu::class)->find($id);
        $form = $this->createFormBuilder()
            ->add('title_fr', TextType::class, [
                'data' => $translator->trans($cgu->getTitle(), [], null, 'fr')
            ])
            ->add('title_en', TextType::class,[
                'data' => $translator->trans($cgu->getTitle(), [], null, 'en')
            ])
            ->add('title_de', TextType::class,[
                'data' => $translator->trans($cgu->getTitle(), [], null, 'de')
            ])
            ->add('text_fr', TextareaType::class,[
                'data' => $translator->trans($cgu->getText(), [], null, 'fr')
            ])
            ->add('text_en', TextareaType::class,[
                'data' => $translator->trans($cgu->getText(), [], null, 'en')
            ])
            ->add('text_de', TextareaType::class,[
                'data' => $translator->trans($cgu->getText(), [], null, 'de')
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer'
            ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fr = Yaml::parseFile($this->getParameter('fr.trans.file'));
            $de = Yaml::parseFile($this->getParameter('de.trans.file'));
            $en = Yaml::parseFile($this->getParameter('en.trans.file'));

            $data = $form->getData();
            $em = $this->getDoctrine()->getManager();

            unset($en[$data['title_en']]);
            unset($en[$data['text_en']]);
            unset($fr[$data['title_en']]);
            unset($fr[$data['title_en']]);
            unset($fr[$data['text_en']]);
            unset($de[$data['title_en']]);
            unset($de[$data['text_en']]);

            $en[$data['title_en']] = $data['title_en'];
            $en[$data['text_en']] = $data['text_en'];
            $fr[$data['title_en']] = $data['title_fr'];
            $fr[$data['text_en']] = $data['text_fr'];
            $de[$data['title_en']] = $data['title_de'];
            $de[$data['text_en']] = $data['text_de'];

            $dumpFr = Yaml::dump($fr);
            $dumpDe = Yaml::dump($de);
            $dumpEn = Yaml::dump($en);

            file_put_contents($this->getParameter('en.trans.file'), $dumpEn);
            file_put_contents($this->getParameter('fr.trans.file'), $dumpFr);
            file_put_contents($this->getParameter('de.trans.file'), $dumpDe);

            $cgu->setTitle($data['title_en']);
            $cgu->setText($data['text_en']);
            $em->persist($cgu);
            $em->flush();
            $this->addFlash('success', 'CGU modifiée');
            return $this->redirectToRoute('admin_cgu');
        }

        return $this->render('cgu/edit-cgu.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @param $id
     * @Route("/admin/cgu/delete/{id}", name="admin_delete_cgu")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete($id){
        $em = $this->getDoctrine()->getManager();
        $cgu = $em->getRepository(Cgu::class)->find($id);

        $fr = Yaml::parseFile($this->getParameter('fr.trans.file'));
        $de = Yaml::parseFile($this->getParameter('de.trans.file'));
        $en = Yaml::parseFile($this->getParameter('en.trans.file'));

        unset($fr[$cgu->getTitle()]);
        unset($fr[$cgu->getText()]);
        unset($en[$cgu->getTitle()]);
        unset($en[$cgu->getText()]);
        unset($de[$cgu->getTitle()]);
        unset($de[$cgu->getText()]);

        $dumpFr = Yaml::dump($fr);
        $dumpEn = Yaml::dump($en);
        $dumpDe = Yaml::dump($de);

        file_put_contents($this->getParameter('en.trans.file'), $dumpEn);
        file_put_contents($this->getParameter('fr.trans.file'), $dumpFr);
        file_put_contents($this->getParameter('de.trans.file'), $dumpDe);

        $em->remove($cgu);
        $em->flush();
        $this->addFlash('success', 'CGU supprimée ');
        return $this->redirectToRoute('admin_cgu');
    }
}
