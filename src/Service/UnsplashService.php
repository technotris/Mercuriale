<?php

namespace App\Service;

use Unsplash\HttpClient;
use Unsplash\PageResult;
use Unsplash\Search;

// call to Unsplash.com API
class UnsplashService
{
    // the param UNSPLASH_APP_KEY has to be set in the .env or config of the webserver
    public function __construct(private string $applicationId)
    {
    }

    public function connect(): void
    {
        HttpClient::init([
            'applicationId' => $this->applicationId,
            'utmSource' => 'Technical demo',
        ]);
    }

    // return the URL of the photo best match with the keyword search
    public function searchImage(string $keyword = 'forest'): ?string
    {
        $this->connect();
        $page = 1;
        $per_page = 1;
        $orientation = 'landscape';
        $result = Search::photos($keyword, $page, $per_page, $orientation);
        /* @var Unsplash\PageResult $result */
        $this->checkRemaining($result);
        if ($result->getTotal() > 0) {
            // retrieve the URL
            // To go further and optimize display of image query have to be adaptive
            return $result[0]['urls']['thumb'];
        }

        return null;
    }

    private function checkRemaining(PageResult $result): void
    {
        $headers = $result->getHeaders();
        if (0 === $headers['X-Ratelimit-Remaining'][0]) {
            // send warning no more called remaining
        } elseif ($headers['X-Ratelimit-Remaining'][0] < 10) {
            // send warning last fews calls
        }
    }
}
