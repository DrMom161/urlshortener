<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Redirecting;
use AppBundle\Repository\RedirectingRepository;
use AppBundle\Service\ResponseBuilder;
use AppBundle\Service\UrlShortener;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * Index page or redirecting
     * @Route("/{shortUrl}", name="homepage", defaults={"shortUrl" = ""})
     * @param string $shortUrl
     * @Method({"GET"})
     * @return Response
     */
    public function indexAction($shortUrl)
    {
        //try to redirect
        if($shortUrl){
            /**
             * @var RedirectingRepository $redirectingRepository
             * @var Redirecting $redirecting
             */
            $redirectingRepository = $this->getDoctrine()->getRepository('AppBundle:Redirecting');
            $redirecting = $redirectingRepository->findOneByShortUrl($shortUrl);
            if($redirecting){
                $redirectingRepository->incUsageCount($redirecting);
                return $this->redirect($redirecting->getLongUrl());
            }
        }
        //default page
        return $this->render('default/index.html.twig');
    }
    /**
     * Create short url from requested long url
     * @param Request $request
     * @return JsonResponse
     * @Route("/create_short_url")
     * @Method({"POST"})
     */
    public function createShortUrlAction(Request $request)
    {
        /**
         * @var RedirectingRepository $redirectingRepository
         */
        $redirectingRepository = $this->getDoctrine()->getRepository('AppBundle:Redirecting');

        $longUrl = $request->get('longUrl');
        $shortUrl = $this->getShortUrl($request, $redirectingRepository);

        if(!ResponseBuilder::hasErrors()){
            $redirectingRepository->saveUrlPair($longUrl, $shortUrl);
            ResponseBuilder::addData('shortUrl', $shortUrl);
        }

        $response = new JsonResponse();
        $response->setData(ResponseBuilder::getResponse());
        return $response;
    }

    /**
     * @param Request $request
     * @param RedirectingRepository $redirectingRepository
     * @return string
     */
    private function getShortUrl(Request $request, RedirectingRepository $redirectingRepository){
        $shortUrl = $request->get('shortUrl');
        if ($shortUrl) {
            //desired short url need to check for uniqueness
            if(!$redirectingRepository->isUniqueShortUrl($shortUrl)){
                ResponseBuilder::addError('This URL is already in use');
            }
        }else{
            /**
             * @var UrlShortener $urlShortenerService
             */
            $urlShortenerService = $this->get('app.url_shortener');
            $shortUrl = $urlShortenerService->generateUniqueShortUrl();
        }
        return $shortUrl;
    }
}
