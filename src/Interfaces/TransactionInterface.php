<?php

// Copyright (C) 2023 Ivan Stasiuk <ivan@stasi.uk>.
//
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this file,
// You can obtain one at https://mozilla.org/MPL/2.0/.

namespace BrokeYourBike\ParallexBank\Interfaces;

use Carbon\CarbonImmutable;

/**
 * @author Ivan Stasiuk <ivan@stasi.uk>
 */
interface TransactionInterface
{
    public function getReference(): string;
    public function getAmount(): float;
    public function getAccountNumber(): string;
    public function getRecipientName(): string;
    public function getTransactionDate(): CarbonImmutable;
}
