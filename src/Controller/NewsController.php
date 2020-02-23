<?php

namespace App\Controller;

use App\Entity\News;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class NewsController extends AbstractController
{
    /**
     * @Route("/api/news/{limit}", name="api_news", methods={"GET"})
     * @param TranslatorInterface $translator
     * @param Request $request
     * @param null $limit
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getNews(TranslatorInterface $translator, Request $request, $limit = null)
    {
        $em = $this->getDoctrine()->getRepository(News::class);
        $data = [];
        if ($limit){
            $news = $em->findBy(['isActive' => true], ['id' => 'DESC'], $limit);
        }
        else{
            $news = $em->findBy(['isActive' => true], ['id' => 'DESC']);
        }
        if (count($news) > 0){
            foreach ($news as $new){
                $id = $new->getId();
                $title = $new->getTitle();
                $text = $new->getText();
                $transNews = $translator->trans('news', [], null, $request->getLocale());
                $viewMore = $translator->trans('read.more', [], null, $request->getLocale());
                array_push($data, [
                    'id' => $id,
                    'text' => $text,
                    'title' => $title,
                    'news' => $transNews,
                    'viewMore' => $viewMore
                    ]);
            }
        }
        return $this->json($data, 200);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/news", name="news")
     */
    public function news(){
        $news = $this->getDoctrine()->getRepository(News::class)->findBy(['isActive' => true], ['id' => 'DESC']);
        return $this->render('news/news.html.twig', ['news' => $news]);
    }
}
