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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DefaultController extends Controller
{
    /**
     * @var ResponseBuilder
     */
    private $responseBuilder;

    /**
     * Index page
     * @Route("/", name="homepage")
     * @Method({"GET"})
     * @return Response
     */
    public function indexAction()
    {
        return $this->render('default/index.html.twig');
    }

    /**
     * Redirect url
     * @Route("/{shortUrl}", requirements={"shortUrl": ".+"})
     * @param string $shortUrl
     * @Method({"GET"})
     * @return Response
     * @throws NotFoundHttpException
     */
    public function redirectAction($shortUrl)
    {
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
        } else {
            throw new NotFoundHttpException('This short url does not exists!');
        }
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
         * @var UrlShortener $urlShortener ;
         */
        $urlShortener = $this->get('app.url_shortener');
        $newRedirecting = $urlShortener->getRedirectingFromRequest($request);
        $this->responseBuilder = $urlShortener->getResponseBuilder();

        if (!$this->responseBuilder->hasErrors()) {
            $this->get('app.entity_validator')->validate($newRedirecting, $this->responseBuilder);

            $logger = $this->get('logger');

            if (!$this->responseBuilder->hasErrors()) {
                $this->getDoctrine()->getRepository('AppBundle:Redirecting')->save($newRedirecting);
                $this->responseBuilder->addData('shortUrl', htmlspecialchars($newRedirecting->getShortUrl()));
                $logger->info('Short url is created. ' . 'Data: short url - ' . $newRedirecting->getShortUrl() . ', long url - ' . $newRedirecting->getLongUrl());
            } else {
                $logger->error('Can not create short url because: ' . implode(', ', $this->responseBuilder->getErrors()) . '. Data: short url - ' . $newRedirecting->getShortUrl() . ', long url - ' . $newRedirecting->getLongUrl());
            }
        }
        return $this->responseBuilder->getJsonResponse();
    }

}
