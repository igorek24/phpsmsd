<?php

namespace App\Domain\SMS\Service;

use App\Domain\SMS\Repository\SMSRepository;

class SentSMSMessageGetter
{
    /**
     * @var SMSRepository
     */
    private SMSRepository $repo;

    public function __construct(SMSRepository $repo)
    {
        $this->repo = $repo;

    }

    public function getSentSmsMessage($id)
    {
        return $this->repo->getSentSmsMessageById($id);
    }
}