<?php

// Copyright (C) 2023 Ivan Stasiuk <ivan@stasi.uk>.
//
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this file,
// You can obtain one at https://mozilla.org/MPL/2.0/.

namespace BrokeYourBike\ParallexBank\Tests;

use Psr\SimpleCache\CacheInterface;
use Psr\Http\Message\ResponseInterface;
use Carbon\CarbonImmutable;
use BrokeYourBike\ParallexBank\Responses\TransferResponse;
use BrokeYourBike\ParallexBank\Interfaces\TransactionInterface;
use BrokeYourBike\ParallexBank\Interfaces\ConfigInterface;
use BrokeYourBike\ParallexBank\Client;

/**
 * @author Ivan Stasiuk <ivan@stasi.uk>
 */
class PostTransactionTest extends TestCase
{
    private string $token = 'super-secure-token';

    /** @test */
    public function it_can_prepare_request(): void
    {
        $transaction = $this->getMockBuilder(TransactionInterface::class)->getMock();
        $transaction->method('getReference')->willReturn('ref-123');
        $transaction->method('getAccountNumber')->willReturn('12345');
        $transaction->method('getRecipientName')->willReturn('John Doe');
        $transaction->method('getAmount')->willReturn(50.00);
        $transaction->method('getTransactionDate')->willReturn(CarbonImmutable::parse('23 Oct 2021 13:43:37'));

        /** @var TransactionInterface $transaction */
        $this->assertInstanceOf(TransactionInterface::class, $transaction);

        $mockedConfig = $this->getMockBuilder(ConfigInterface::class)->getMock();
        $mockedConfig->method('getUrl')->willReturn('https://api.example/');
        $mockedConfig->method('getDebitAccountNumber')->willReturn('debit-12345');
        $mockedConfig->method('getDebitAccountName')->willReturn('debit-Jane');

        $mockedResponse = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $mockedResponse->method('getStatusCode')->willReturn(200);
        $mockedResponse->method('getBody')
            ->willReturn('{
                "responseCode":"00",
                "responseDescription":"Transaction Successful"
            }');

        /** @var \Mockery\MockInterface $mockedClient */
        $mockedClient = \Mockery::mock(\GuzzleHttp\Client::class);
        $mockedClient->shouldReceive('request')->withArgs([
            'POST',
            'https://api.example/api/ThirdPartyTransfer/BulkTransfer',
            [
                \GuzzleHttp\RequestOptions::HEADERS => [
                    'Accept' => 'application/json',
                    'Authorization' => "Bearer {$this->token}",
                ],
                \GuzzleHttp\RequestOptions::JSON => [
                    'transactionDate' => '2021-10-23',
                    'transactionID' => 'ref-123',
                    'credits' => [[
                        'accountToDebit' => '12345',
                        'accountName' => 'John Doe',
                        'amount' => "50.00",
                        'naration' => 'ref-123',
                    ]],
                    'debits' => [[
                        'accountToDebit' => 'debit-12345',
                        'accountName' => 'debit-Jane',
                        'amount' => "50.00",
                        'naration' => 'ref-123',
                    ]],
                ],
            ],
        ])->once()->andReturn($mockedResponse);

        $mockedCache = $this->getMockBuilder(CacheInterface::class)->getMock();
        $mockedCache->method('has')->willReturn(true);
        $mockedCache->method('get')->willReturn($this->token);

        /**
         * @var ConfigInterface $mockedConfig
         * @var \GuzzleHttp\Client $mockedClient
         * @var CacheInterface $mockedCache
         * */
        $api = new Client($mockedConfig, $mockedClient, $mockedCache);

        $requestResult = $api->transfer($transaction);
        $this->assertInstanceOf(TransferResponse::class, $requestResult);
    }
}