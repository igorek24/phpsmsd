<?php

namespace App\Domain\SMS\Service;

use App\Domain\SMS\Repository\SMSRepository;

class SentSMSMessagesGetter
{
    /**
     * @var SMSRepository
     */
    private SMSRepository $repo;

    public function __construct(SMSRepository $repo)
    {
        $this->repo = $repo;

    }

    public function getSentSmsMessages($order = 'asc', $limit = 100)
    {
        return $this->repo->getSentSmsMessages($limit, $order);
    }
}