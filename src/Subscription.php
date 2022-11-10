<?php

declare(strict_types=1);

namespace Imdhemy\Purchases;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Imdhemy\AppStore\ClientFactory as AppStoreClientFactory;
use Imdhemy\AppStore\Exceptions\InvalidReceiptException;
use Imdhemy\AppStore\Receipts\ReceiptResponse;
use Imdhemy\AppStore\Receipts\Verifier;
use Imdhemy\GooglePlay\ClientFactory as GooglePlayClientFactory;
use Imdhemy\GooglePlay\Subscriptions\SubscriptionClient as GooglePlaySubscription;
use Imdhemy\GooglePlay\Subscriptions\SubscriptionPurchase;
use Imdhemy\GooglePlay\ValueObjects\EmptyResponse;
use Imdhemy\Purchases\Contracts\SubscriptionContract;
use Imdhemy\Purchases\Subscriptions\AppStoreSubscription;
use Imdhemy\Purchases\Subscriptions\GoogleSubscription;

class Subscription
{
    /**
     * @var string
     */
    protected $itemId;

    /**
     * @var string
     */
    protected $token;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $packageName;

    /**
     * @var string
     */
    protected $receiptData;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var bool
     */
    protected $renewalAble;

    /**
     * @var SubscriptionPurchase
     */
    protected $googleGetResponse;

    /**
     * @var bool
     */
    protected $isGoogle = false;

    /**
     * @var ReceiptResponse
     */
    private $appStoreResponse;

    public function googlePlay(?ClientInterface $client = null): self
    {
        $this->isGoogle = true;
        $this->client = $client ?? GooglePlayClientFactory::create([GooglePlayClientFactory::SCOPE_ANDROID_PUBLISHER]);
        $this->packageName = config('liap.google_play_package_name');

        return $this;
    }

    public function appStore(?ClientInterface $client = null): self
    {
        $this->isGoogle = false;
        $this->client = $client ?? AppStoreClientFactory::create();
        $this->password = config('liap.appstore_password');
        $this->renewalAble = false;

        return $this;
    }

    public function id(string $itemId): self
    {
        $this->itemId = $itemId;

        return $this;
    }

    public function token(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function packageName(string $packageName): self
    {
        $this->packageName = $packageName;

        return $this;
    }

    /**
     * @throws GuzzleException
     * @throws InvalidReceiptException
     */
    public function verifyRenewable(?ClientInterface $sandboxClient = null): ReceiptResponse
    {
        $this->renewalAble = true;

        return $this->verifyReceipt($sandboxClient);
    }

    /**
     * @throws GuzzleException
     * @throws InvalidReceiptException
     */
    public function verifyReceipt(?ClientInterface $sandboxClient = null): ReceiptResponse
    {
        if (is_null($this->appStoreResponse)) {
            $verifier = new Verifier($this->client, $this->receiptData, $this->password);
            $this->appStoreResponse = $verifier->verify($this->renewalAble, $sandboxClient);
        }

        return $this->appStoreResponse;
    }

    public function renewable(): self
    {
        $this->renewalAble = true;

        return $this;
    }

    public function nonRenewable(): self
    {
        $this->renewalAble = false;

        return $this;
    }

    /**
     * @throws GuzzleException
     */
    public function acknowledge(?string $developerPayload = null): EmptyResponse
    {
        return $this->createSubscription()->acknowledge($developerPayload);
    }

    public function createSubscription(): GooglePlaySubscription
    {
        return new GooglePlaySubscription(
            $this->client,
            $this->packageName,
            $this->itemId,
            $this->token
        );
    }

    /**
     * @return $this
     */
    public function receiptData(string $receiptData): self
    {
        $this->receiptData = $receiptData;

        return $this;
    }

    /**
     * @return $this
     */
    public function password(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @throws GuzzleException
     * @throws InvalidReceiptException
     */
    public function toStd(): SubscriptionContract
    {
        if ($this->isGoogle) {
            $response = $this->get();

            return new GoogleSubscription($response, $this->itemId, $this->token);
        }

        $response = $this->verifyReceipt();

        return new AppStoreSubscription($response->getLatestReceiptInfo()[0]);
    }

    /**
     * @throws GuzzleException
     */
    public function get(): SubscriptionPurchase
    {
        if (is_null($this->googleGetResponse)) {
            $this->googleGetResponse = $this->createSubscription()->get();
        }

        return $this->googleGetResponse;
    }
}
