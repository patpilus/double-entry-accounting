<?php

declare(strict_types=1);

namespace App\Accounting\Application;

interface CommandHandler
{
    public function execute(Command $command): void;
}
