<?php

namespace App\Domain\SMS\Repository;

use App\Factory\LoggerFactory;

use MysqliDb;
use Psr\Log\LoggerInterface;

class SMSRepository
{
    private LoggerInterface $logger;

    public function __construct(MysqliDb $db, LoggerFactory $logger)
    {
        $this->logger = $logger->addFileHandler('sms.log')->createLogger();
//        $this->logger->debug("AqualinkD curl url was set to $this->aqualinkDUrl");
        $this->db = $db;
    }

    public function getSentSmsMessages($limit = 20, $order = 'asc'): MysqliDb|array|string
    {

        $this->db->orderBy('ID', $this->db->escape($order));
        $this->db->orderBy('SequencePosition', $this->db->escape('asc'));
        $messages = $this->db->get('sentitems', (int)$limit);
        $multiPartMessageIDs = [];
        $newMessagesArr = [];

        // Getting message ID for all the multipart messages and store them in array and creating new array of messages
        // without sequential messages.
        foreach ($messages as $v1) {
            if ($v1['SequencePosition'] > 1) {
                if (!in_array($v1['ID'], $multiPartMessageIDs)) {
                    $multiPartMessageIDs[] = $v1['ID'];
                }

            }
            if ($v1['SequencePosition'] == 1) {
                $newMessagesArr[] = $v1;
            }
        }

        // Combining multipart messages in to one and storing it in the $messageCombined array
        $messageCombined = [];
        foreach ($messages as $v2) {
            foreach ($multiPartMessageIDs as $id) {
                if ($v2['ID'] == $id) {
                    if (!isset($messageCombined[$v2['ID']])) {
                        $messageCombined[$v2['ID']] = [];
                        $messageCombined[$v2['ID']]['TextDecoded'] = '';
                        $messageCombined[$v2['ID']]['Text'] = '';
                    }
                    $nMess = [
                        'TextDecoded' => $messageCombined[$v2['ID']]['TextDecoded'] . $v2['TextDecoded'],
                        'Text' => $messageCombined[$v2['ID']]['Text'] . $v2['Text'],

                    ];

                    $messageCombined[$v2['ID']] = $nMess;
                }
            }

        }
        // Replacing partial message with the full one for all the multipart messages in $newMessagesArr array,
        // ready to be returned.
        foreach ($newMessagesArr as $newMessageKey => $newMessageVol) {
            foreach ($multiPartMessageIDs as $multiPartMessageID) {
                if ($multiPartMessageID == $newMessageVol['ID']) {
                    $newMessagesArr[$newMessageKey]['Text'] = $messageCombined[$multiPartMessageID]['Text'];
                    $newMessagesArr[$newMessageKey]['TextDecoded'] = $messageCombined[$multiPartMessageID]['TextDecoded'];
                }
            }
        }
        return $newMessagesArr;
    }

    public function getSentSmsMessageById(int $id)
    {
        $message = $this->db->where('ID', $this->db->escape($id))->get('sentitems');

        if (count($message) > 1) {
            $fullTextDecodedMessage = "";
            $fullTextMessage = "";
            foreach ($message as $k => $v) {
                $fullTextDecodedMessage = $fullTextDecodedMessage . $message[$k]['TextDecoded'];
                $fullTextMessage = $fullTextMessage . $message[$k]['Text'];
            }
            $message = $message[0];
            $message['TextDecoded'] = $fullTextDecodedMessage;
            $message['Text'] = $fullTextMessage;
            return $message;
        }
        return $message[0];
    }

    public function getInboxSmsMessages($limit = 20, $order = 'asc'): MysqliDb|array|string
    {
        $this->db->orderBy('ID', $this->db->escape($order));
        return $this->db->get('inbox', (int)$limit);
    }
}