<?php

namespace App\Handler;

use App\Entity\Order;
use App\Entity\OrderLine;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Request;

class CheckOrderHandler
{
    public const GEO_API_URL = "https://api-adresse.data.gouv.fr/search/";
    public const GEO_FR_MIN_SCORE = 0.6;
    public const GEO_FR_LIMIT = 1;
    public const GEO_PARAM_ADDRESS = "q";
    public const GEO_PARAM_ZIPCODE = "postcode";
    public const GEO_PARAM_LIMIT = "limit";
    public const HEAVY_WEIGHT = 40000;
    public const EXCESSIVE_WEIGHT = 60000;

    protected $client;
    protected bool $logIssues;
    protected $em;

    public function __construct(HttpClientInterface $client, EntityManagerInterface $em)
    {
        $this->client = $client;
        $this->em = $em;
    }

    public function handle(Order $order, bool $logIssues = false): void
    {
        $this->logIssues = $logIssues;
        $this->clearOrderTags($order);
        $this->clearOrderIssues($order);
        $this->checkWeight($order);
        $this->checkShippingAddress($order);
        $this->checkEmail($order);
    }

    protected function checkWeight(Order $order): void
    {
        $orderWeight = $order->getWeight();

        if ($orderWeight > self::HEAVY_WEIGHT) {
            $order->addHeavyTag();

            $this->persistOrder($order);
        }

        if ($orderWeight > self::EXCESSIVE_WEIGHT) {
            $order->addHasIssuesTag();

            if ($this->logIssues) {
                $order->addExceeds60kgIssue();
            }

            $this->persistOrder($order);
        }
    }

    protected function checkShippingAddress(Order $order): void
    {
        if (Order::SHIPPING_COUNTRY_FR != $order->getShippingCountry()) {
            $order->addForeignWarehouseTag();

            $this->persistOrder($order);

            return;
        }

        if (!$this->hasFrenchAdress($order)) {
            $order->addHasIssuesTag();

            if ($this->logIssues) {
                $order->addInvalidFrenchAddressIssue();
            }

            $this->persistOrder($order);
        }
    }

    protected function hasFrenchAdress(Order $order): bool
    {
        $address = $order->getShippingAddress();
        $zipcode = $order->getShippingZipcode();

        $geojson = $this->searchAddress($address, $zipcode);
        $score = $this->getGeoScore($geojson);

        return $score > self::GEO_FR_MIN_SCORE;
    }

    protected function searchAddress(string $address, string $zipcode): string
    {
        $response = $this->client->request(Request::METHOD_GET, self::GEO_API_URL, [
          'query' => [
            self::GEO_PARAM_ADDRESS => $address,
            self::GEO_PARAM_ZIPCODE => $zipcode,
            self::GEO_PARAM_LIMIT => self::GEO_FR_LIMIT,
          ],
      ]);

        $statusCode = $response->getStatusCode();
        $contentType = $response->getHeaders()['content-type'][0];
        $content = $response->getContent();

        return $content;
    }

    protected function getGeoScore(string $geojson): float
    {
        $geo = json_decode($geojson);
        $features = $geo->features;
        $properties = $features[0]->properties;
        $score = $properties->score;

        return $score;
    }

    protected function checkEmail(Order $order): void
    {
        $email = $order->getContactEmail();

        if (empty($email)) {
            $order->addHasIssuesTag();

            if ($this->logIssues) {
                $order->addEmptyEmailIssue();
            }

            $this->persistOrder($order);
        }
    }

    protected function persistOrder(Order $order)
    {
        $this->em->persist($order);
        $this->em->flush();
    }

    protected function clearOrderTags(Order $order): void
    {
        foreach ($order->getTags() as $tag) {
            $this->em->remove($tag);
        }
        $this->em->flush();
    }

    protected function clearOrderIssues(Order $order): void
    {
        foreach ($order->getIssues() as $issue) {
            $this->em->remove($issue);
        }
        $this->em->flush();
    }
}
