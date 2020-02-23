<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class PressController extends AbstractController
{
    /**
     * @Route("/press", name="press")
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request, TranslatorInterface $translator)
    {
        $data = [
            urlencode('01Amour_2009'),
            urlencode('20minutes_23mai2012'),
            urlencode('24heures_2011'),
            urlencode('AZ_19.05.2012'),
            urlencode('Bilan_2011'),
            urlencode('EntrepriseRomande_2009'),
            urlencode('LesQuotidiennes_2009'),
            urlencode('LesQuotidiennes_2010'),
            urlencode('LeTemps_2010'),
            urlencode('SeMarier_2008'),
            urlencode('TdG_2009'),
            urlencode('TrendysLeMag_10mai2012')
        ];
        $yt = $request->getLocale() == 'fr' ? 'https://www.youtube.com/embed/dZpvv-uxgmk' :
            'https://www.youtube.com/embed/FKopJSUGx4k';
        $btn = $translator->trans('view.more', [], null, $request->getLocale());

        return $this->render('press/index.html.twig', [
            'datas' => $data,
            'yt' => $yt,
            'btn' => $btn
        ]);
    }

    /**
     * @param $name
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @Route("/press/pdf/{name}", name="press_pdf")
     */
    public function renderPdf($name){
        return $this->file($this->getParameter('storage.pdf') . '/' . $name);
    }
}
