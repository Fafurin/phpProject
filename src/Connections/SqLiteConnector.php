<?php

namespace App\Connections;

use App\config\SqLiteConfig;

class SqLiteConnector extends Connector implements SqLiteConnectorInterface
{

    public function getDsn(): string
    {
        return SqLiteConfig::DSN;
    }

    public function getUserName(): string
    {
        return '';
    }

    public function getPassword(): string
    {
        return '';
    }

    public function getOptions(): array
    {
        return [];
    }
}