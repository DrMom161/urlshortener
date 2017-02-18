<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Redirecting;
use AppBundle\Repository\RedirectingRepository;
use AppBundle\Service\ResponseBuilder;
use AppBundle\Service\UrlShortener;
use AppBundle\Service\UrlValidator;
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
        if ($shortUrl) {
            /**
             * @var RedirectingRepository $redirectingRepository
             * @var Redirecting $redirecting
             */
            $redirectingRepository = $this->getDoctrine()->getRepository('AppBundle:Redirecting');
            $redirecting = $redirectingRepository->findOneByShortUrl($shortUrl);
            if ($redirecting) {

                $logger = $this->get('logger');
                $logger->info('Redirected programmatically from ' . $redirecting->getShortUrl() . ' to ' . $redirecting->getLongUrl());

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

        $longUrl = $this->getAndValidateLongurl($request);
        $shortUrl = $this->getAndValidateShortUrl($request, $redirectingRepository);

        $logger = $this->get('logger');

        if (!ResponseBuilder::hasErrors()) {
            $redirectingRepository->saveUrlPair($longUrl, $shortUrl);
            ResponseBuilder::addData('shortUrl', $shortUrl);
            $logger->info('Can not create short url because: ' . implode(', ', ResponseBuilder::getErrors()));
        } else {
            $logger->error('Can not create short url because: ' . implode(', ', ResponseBuilder::getErrors()) . '. Data: short url - ' . $shortUrl . ', long url - ' . $longUrl);
        }

        $response = new JsonResponse();
        $response->setData(ResponseBuilder::getResponse());
        return $response;
    }

    /**
     * Get from request long url and validate it
     * @param $request
     */
    private function getAndValidateLongurl($request)
    {
        $longUrl = $request->get('longUrl');
        if (!UrlValidator::isValidUrl($longUrl)) {
            ResponseBuilder::addError('Your url is not valid or unavailable');
        }
        return $longUrl;
    }

    /**
     * Get from request or create new short url and validate it
     * @param Request $request
     * @param RedirectingRepository $redirectingRepository
     * @return string
     */
    private function getAndValidateShortUrl(Request $request, RedirectingRepository $redirectingRepository)
    {
        $shortUrl = $request->get('shortUrl');
        if ($shortUrl) {
            //desired short url need to check for uniqueness
            if (!$redirectingRepository->isUniqueShortUrl($shortUrl)) {
                ResponseBuilder::addError('This URL is already in use');
            }
        } else {
            /**
             * @var UrlShortener $urlShortenerService
             */
            $urlShortenerService = $this->get('app.url_shortener');
            $shortUrl = $urlShortenerService->generateUniqueShortUrl();
        }
        return $shortUrl;
    }
}
