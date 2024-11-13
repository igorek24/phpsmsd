<?php

namespace App\Domain\SMS\Service;

use App\Domain\SMS\Repository\SMSRepository;
use App\Factory\LoggerFactory;
use Psr\Log\LoggerInterface;

class SMSMessageSender
{
    private SMSRepository $repo;
    private LoggerInterface $logger;

    public function __construct(LoggerFactory $logger, SMSRepository $repo)
    {
        $this->repo = $repo;
        $this->logger = $logger->addFileHandler('sms.log')->createLogger();
    }

    public function sendSmsMessage($postBody)
    {
        $message = $postBody->message;
        $phoneNumber = $postBody->phone_number;
        $phoneNumber = filter_var($phoneNumber, FILTER_SANITIZE_NUMBER_INT);
        $phoneNumber = str_replace('.', '', $phoneNumber);
        $phoneNumber = str_replace('-', '', $phoneNumber);
        if (strlen($phoneNumber) == 10) {
            $phoneNumber = "1" . $phoneNumber;
        }
        if (strlen($phoneNumber) == 11 && !stristr($phoneNumber, "+")) {
            $phoneNumber = '+' . $phoneNumber;
        }

        $errors = [];
        if (empty($phoneNumber)) {
            array_push($errors, 'Phone number can not be empty (it might be empty if non numeric string is provided).');
        }
        if (empty($message)) {
            array_push($errors, 'Message can not be empty.');
        }
        if (strlen($phoneNumber) != 12 || $phoneNumber[0] != '+') {
            array_push($errors, "Not a vaflid phone number ($phoneNumber).");
        }
        if (empty($errors)) {


            $message = $this->repo->db->escape($message);
            $cmd = sprintf('gammu-smsd-inject TEXT %s -unicode -len ' . (strlen($message) + 1) . ' -text %s', $phoneNumber, escapeshellarg($message));
            $shell = shell_exec($cmd);
            if ($shell) {
                $this->logger->debug($shell);
                return [
                    'status' => 'OK',
                    "message' => 'Message was sent successfully to {$phoneNumber}.",
                    'log' => $shell,
                    'uri' => $_SERVER['REQUEST_URI']
                ];
            }
        }
        $this->logger->warning("Failed to send message to {$phoneNumber}, {$errors[0]}");
        return [
            'status' => 'error',
            'message' => 'Failed to send message.',
            'error' => $errors[0],
            'uri' => $_SERVER['REQUEST_URI']
        ];
    }
}