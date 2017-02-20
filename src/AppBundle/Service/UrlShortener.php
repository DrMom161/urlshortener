<?php

namespace AppBundle\Service;


use AppBundle\Entity\Redirecting;
use AppBundle\Repository\RedirectingRepository;
use Symfony\Component\HttpFoundation\Request;

class UrlShortener
{
    /**
     * @var RedirectingRepository
     */
    private $redirectingRepository;
    /**
     * @var ResponseBuilder
     */
    private $responseBuilder;

    /**
     * UrlShortener constructor.
     * @param RedirectingRepository $redirectingRepository
     * @param ResponseBuilder $responseBuilder
     */
    public function __construct(RedirectingRepository $redirectingRepository, ResponseBuilder $responseBuilder)
    {
        $this->redirectingRepository = $redirectingRepository;
        $this->responseBuilder = $responseBuilder;
    }

    /**
     * Getter for $responseBuilder
     * @return  ResponseBuilder
     */
    public function getResponseBuilder()
    {
        return $this->responseBuilder;
    }

    /**
     * Create Redirecting entity from request
     * @param Request $request
     * @return Redirecting
     */
    public function getRedirectingFromRequest(Request $request)
    {
        $redirecting = new Redirecting();
        $redirecting->setShortUrl($this->getAndValidateShortUrl($request));
        $redirecting->setLongUrl($this->getAndValidateLongurl($request));
        return $redirecting;
    }

    /**
     * Get from request or create new short url and validate it
     * @param Request $request
     * @return string
     */
    private function getAndValidateShortUrl(Request $request)
    {
        $shortUrl = $request->get('shortUrl');
        if ($shortUrl) {
            //desired short url need to check for uniqueness
            if (!$this->redirectingRepository->isUniqueShortUrl($shortUrl)) {
                $this->responseBuilder->addError('This URL is already in use');
            }
        } else {
            /**
             * @var UrlShortener $urlShortenerService
             */
            $shortUrl = $this->generateUniqueShortUrl();
        }
        return $shortUrl;
    }

    /**
     * Generate unique short url
     * @return string
     */
    private function generateUniqueShortUrl()
    {
        $isUnique = false;

        while (!$isUnique) {
            $shortUrl = $this->generateShortUrl();
            $isUnique = $this->redirectingRepository->isUniqueShortUrl($shortUrl);
        }

        return $shortUrl;
    }

    /**
     * Generate unique short url (alphabetic)
     * @return string
     */
    private function generateShortUrl()
    {
        $characters = 'abcdefghijklmnopqrstuvwxyz';
        $charactersMaxIndex = strlen($characters) - 1;
        $urlLength = rand(8, 10);

        $url = '';
        for ($i = 0; $i < $urlLength; $i++) {
            $url .= $characters[rand(0, $charactersMaxIndex)];
        }

        return $url;
    }

    /**
     * Get from request long url and validate it
     * @param Request $request
     * @return string
     */
    private function getAndValidateLongurl(Request $request)
    {
        $longUrl = $request->get('longUrl');
        if (!$this->isValidUrl($longUrl)) {
            $this->responseBuilder->addError('Your url is not valid or unavailable');
        }
        return $longUrl;
    }

    /**
     * Validate url by http status code
     * @param string $url
     * @return bool
     */
    private function isValidUrl($url)
    {
        $file_headers = @get_headers($url);
        if (!$file_headers || preg_match("/HTTP.* 404/i", $file_headers[0])) {
            $result = false;
        } else {
            $result = true;
        }
        return $result;
    }


}