<?php

namespace App\Domain\SMS\Service;

use App\Domain\SMS\Repository\SMSRepository;

class InboxSMSMessagesGetter
{
    /**
     * @var SMSRepository
     */
    private SMSRepository $repo;

    public function __construct(SMSRepository $repo)
    {
        $this->repo = $repo;

    }

    public function getInboxSmsMessages($order = 'asc', $limit = 100)
    {
        return $this->repo->getInboxSmsMessages($limit, $order);
    }
}