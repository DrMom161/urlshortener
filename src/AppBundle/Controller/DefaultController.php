<?php

namespace AppBundle\Controller;

use AppBundle\Repository\RedirectingRepository;
use AppBundle\Service\ResponseBuilder;
use AppBundle\Service\UrlShortener;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        return $this->render('default/index.html.twig');
    }

    /**
     * Create short url from requested long url
     * @param Request $request
     * @return JsonResponse
     * @Route("/create_short_url")
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
