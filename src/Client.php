<?php

// Copyright (C) 2023 Ivan Stasiuk <ivan@stasi.uk>.
//
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this file,
// You can obtain one at https://mozilla.org/MPL/2.0/.

namespace BrokeYourBike\ParallexBank;

use GuzzleHttp\ClientInterface;
use BrokeYourBike\ResolveUri\ResolveUriTrait;
use BrokeYourBike\ParallexBank\Models\TransactionResponse;
use BrokeYourBike\ParallexBank\Interfaces\TransactionInterface;
use BrokeYourBike\ParallexBank\Interfaces\ConfigInterface;
use BrokeYourBike\HttpEnums\HttpMethodEnum;
use BrokeYourBike\HttpClient\HttpClientTrait;
use BrokeYourBike\HttpClient\HttpClientInterface;
use BrokeYourBike\HasSourceModel\SourceModelInterface;
use BrokeYourBike\HasSourceModel\HasSourceModelTrait;

/**
 * @author Ivan Stasiuk <ivan@stasi.uk>
 */
class Client implements HttpClientInterface
{
    use HttpClientTrait;
    use ResolveUriTrait;
    use HasSourceModelTrait;

    private ConfigInterface $config;

    public function __construct(ConfigInterface $config, ClientInterface $httpClient)
    {
        $this->config = $config;
        $this->httpClient = $httpClient;
    }

    public function getConfig(): ConfigInterface
    {
        return $this->config;
    }

    public function postTransaction(string $requestId, TransactionInterface $transaction): TransactionResponse
    {
        $options = [
            \GuzzleHttp\RequestOptions::HEADERS => [
                'Accept' => 'application/json',
                'Client-Id' => $this->config->getClientId(),
                'Client-Key' => $this->config->getClientSecret(),
            ],
            \GuzzleHttp\RequestOptions::JSON => [
                'BankId' => $transaction->getBankCode(),
                'TrnType' => 'T',
                'TrnSubType' => 'CI',
                'RequestID' => $requestId,
                'PartTrnRec' => [
                    [
                        'AcctId' => $transaction->getBankAccount(),
                        'CreditDebitFlg' => 'D',
                        'TrnAmt' => $transaction->getAmount(),
                        'CurrencyCode' => $transaction->getCurrencyCode(),
                        'TrnParticulars' => $transaction->getReference(),
                        'ValueDt' => $transaction->getValueDate()->format('Y-m-d\TH:i:s.uP'),
                    ]
                ],
            ],
        ];

        if ($transaction instanceof SourceModelInterface){
            $options[\BrokeYourBike\HasSourceModel\Enums\RequestOptions::SOURCE_MODEL] = $transaction;
        }

        $uri = (string) $this->resolveUriFor($this->config->getUrl(), 'coreapi/api/finacle/PostingTransaction');
        $response = $this->httpClient->request(HttpMethodEnum::POST->value, $uri, $options);

        return new TransactionResponse($response);
    }
}
