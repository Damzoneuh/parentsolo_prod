<?php

namespace App\Controller;

use App\Entity\Faq;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Yaml;
use Symfony\Contracts\Translation\TranslatorInterface;

class FaqController extends AbstractController
{
    /**
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/faq", name="faq")
     */
    public function index(Request $request, TranslatorInterface $translator)
    {
        $faqs = $this->getDoctrine()->getRepository(Faq::class)->findAll();
        $rows = [];
        if ($faqs){
            foreach ($faqs as $faq){
                array_push($rows, [
                    'title' => $translator->trans($faq->getTitle(), [], null, $request->getLocale()),
                    'text' => $translator->trans($faq->getText(), [], null, $request->getLocale())
                ]);
            }
        }
        return $this->render('faq/index.html.twig', [
            'trans' => $translator->trans('faq', [], null, $request->getLocale()),
            'faqs' => $rows
        ]);
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/admin/faq", name="admin_faq")
     */
    public function createField(Request $request){
        $em = $this->getDoctrine()->getManager();
        $faqs = $em->getRepository(Faq::class)->findAll();

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

                $faq = new Faq();
                $faq->setTitle($data['title_en']);
                $faq->setText($data['text_en']);
                $em->persist($faq);
                $em->flush();

                $this->addFlash('success', 'FAQ ajoutée');

                return $this->redirectToRoute('admin_faq');
            }
            $this->addFlash('error', 'La clé de traduction n\'est pas disponible');
            return $this->redirectToRoute('admin_faq');
        }

        return $this->render('faq/admin.html.twig', ['form' => $form->createView(), 'faqs' => $faqs]);
    }

    /**
     * @param $id
     * @Route("/admin/faq/delete/{id}", name="admin_delete_faq")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete($id){
        $em = $this->getDoctrine()->getManager();
        $faq = $em->getRepository(Faq::class)->find($id);

        $fr = Yaml::parseFile($this->getParameter('fr.trans.file'));
        $de = Yaml::parseFile($this->getParameter('de.trans.file'));
        $en = Yaml::parseFile($this->getParameter('en.trans.file'));

        unset($fr[$faq->getTitle()]);
        unset($fr[$faq->getText()]);
        unset($en[$faq->getTitle()]);
        unset($en[$faq->getText()]);
        unset($de[$faq->getTitle()]);
        unset($de[$faq->getText()]);

        $dumpFr = Yaml::dump($fr);
        $dumpEn = Yaml::dump($en);
        $dumpDe = Yaml::dump($de);

        file_put_contents($this->getParameter('en.trans.file'), $dumpEn);
        file_put_contents($this->getParameter('fr.trans.file'), $dumpFr);
        file_put_contents($this->getParameter('de.trans.file'), $dumpDe);

        $em->remove($faq);
        $em->flush();
        $this->addFlash('success', 'FAQ supprimée ');
        return $this->redirectToRoute('admin_faq');
    }
}
