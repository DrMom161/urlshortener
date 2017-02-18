<?php

namespace AppBundle\Service;


use AppBundle\Repository\RedirectingRepository;

class UrlShortener
{
    /**
     * @var RedirectingRepository
     */
    private $redirectingRepository;

    /**
     * UrlShortener constructor.
     * @param RedirectingRepository $redirectingRepository
     */
    public function __construct($redirectingRepository)
    {
        $this->redirectingRepository = $redirectingRepository;
    }

    /**
     * Generate unique short url
     * @return string
     */
    public function generateUniqueShortUrl()
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
}