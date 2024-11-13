# phpSMSd
## What's phpSMSd
phpSMSd is php based api for [gammu-sms](https://wammu.eu/smsd/), build with [Slim v4 Framework ](https://www.slimframework.com/) . Send and receive SMS messages using 4G, LTE modem with [gammu](https://wammu.eu/gammu/) as a backend.

I'm using this script on my Raspberry Pi, with a USB LTE 4G modem to send SMS messages programmatically to notify myself and my family members, of the events that are produced ether by my home automation, or various monitoring software.

> [!CAUTION]
> This script has **NO SECURITY** or authentication of any kind, at the moment. **DO NOT EXPOSE IT TO THE PUBLIC!**


## API usage examples

### Send message

```
curl --header "Content-Type: application/json" \
    --request POST \
    --data '{"phone_number":"5551234567","message":"Igor6"}' \
    http://192.168.1.101/api/v1/sms/send/ | jq
```

### Response

```json
{
  "status": "OK",
  "0": "message' => 'Message was sent successfully to +5551234567.",
  "log": "gammu-smsd-inject[3238]: Warning: No PIN code in /etc/gammu-smsdrc file\ngammu-smsd-inject[3238]: Connected to Database: smsd on localhost\ngammu-smsd-inject[3238]: Connected to Database native_mysql: smsd on localhost\ngammu-smsd-inject[3238]: Written message with ID 86\n6 / 0\nWritten message with ID 86\n",
  "uri": "/api/v1/sms/send/"
}
```
### Request all messages (limited to 1000):
```
$ curl http://192.168.1.101/api/v1/sms/sent/messages/ | jq
```

### Response:
```json
[
  {
    "UpdatedInDB": "2023-06-02 15:16:27",
    "InsertIntoDB": "2023-06-02 15:16:25",
    "SendingDateTime": "2023-06-02 15:16:27",
    "DeliveryDateTime": null,
    "Text": "0054006500730074006D006500730073006100670065",
    "DestinationNumber": "+5551234567",
    "Coding": "Unicode_No_Compression",
    "UDH": "",
    "SMSCNumber": "+5557654321",
    "Class": -1,
    "TextDecoded": "Testmessage",
    "ID": 1,
    "SenderID": "",
    "SequencePosition": 1,
    "Status": "SendingOKNoReport",
    "StatusError": -1,
    "TPMR": 20,
    "RelativeValidity": 255,
    "CreatorID": "Gammu 1.42.0",
    "StatusCode": -1
  },
  {
    "UpdatedInDB": "2023-06-02 15:16:33",
    "InsertIntoDB": "2023-06-02 15:16:32",
    "SendingDateTime": "2023-06-02 15:16:33",
    "DeliveryDateTime": null,
    "Text": "0054006500730074006D006500730073006100670065",
    "DestinationNumber": "+5551234567",
    "Coding": "Unicode_No_Compression",
    "UDH": "",
    "SMSCNumber": "+5557654321",
    "Class": -1,
    "TextDecoded": "Testmessage",
    "ID": 2,
    "SenderID": "",
    "SequencePosition": 1,
    "Status": "SendingOKNoReport",
    "StatusError": -1,
    "TPMR": 21,
    "RelativeValidity": 255,
    "CreatorID": "Gammu 1.42.0",
    "StatusCode": -1
  }
 ]
```
### Request all messages (limited to 1000) sorted (asc|desc, desc in this case):
```
$ curl http://192.168.1.101/api/v1/sms/sent/messages/desc/
```

### Response:
```json
[
  {
    "UpdatedInDB": "2024-11-13 10:48:10",
    "InsertIntoDB": "2024-11-13 10:48:05",
    "SendingDateTime": "2024-11-13 10:48:10",
    "DeliveryDateTime": null,
    "Text": "0043004F00440045003A002000390038003000380020003100380031003000300033002000330032003500380033003600200032003200320037003500360036002000500049004E003A00200035003700300032002E",
    "DestinationNumber": "+5551234567",
    "Coding": "Unicode_No_Compression",
    "UDH": "",
    "SMSCNumber": "+5557654321",
    "Class": -1,
    "TextDecoded": "CODE: 9808 181003 325836 2227566 PIN: 5702.",
    "ID": 84,
    "SenderID": "",
    "SequencePosition": 1,
    "Status": "SendingOKNoReport",
    "StatusError": -1,
    "TPMR": 178,
    "RelativeValidity": 255,
    "CreatorID": "Gammu 1.42.0",
    "StatusCode": -1
  },
  {
    "UpdatedInDB": "2024-11-13 10:36:17",
    "InsertIntoDB": "2024-11-13 10:36:15",
    "SendingDateTime": "2024-11-13 10:36:17",
    "DeliveryDateTime": null,
    "Text": "00480065006C006C006F002000490067006F00720036002E",
    "DestinationNumber": "+5551234567",
    "Coding": "Unicode_No_Compression",
    "UDH": "",
    "SMSCNumber": "+5557654321",
    "Class": -1,
    "TextDecoded": "Hello Igor6.",
    "ID": 83,
    "SenderID": "",
    "SequencePosition": 1,
    "Status": "SendingOKNoReport",
    "StatusError": -1,
    "TPMR": 177,
    "RelativeValidity": 255,
    "CreatorID": "Gammu 1.42.0",
    "StatusCode": -1
  }
]
```

### List of all POST URIs:

```
/api/v1/sms/send/                       # Send a message. Body mis include {"phone_number":"<phone number>","message":"My message goes here!"}
```


### List of all GET URIs:

```
# Send message

/api/v1/sms/send/                       # Send a message. Body mis include {"phone_number":"<phone number>","message":"My message goes here!"}

# Messages were sent

/api/v1/sms/sent/messages/              # Get all sent messages.
/api/v1/sms/sent/messages/asc/          # Get all sent messages, ordered asc.
/api/v1/sms/sent/messages/desc/         # Get all sent messages, ordered desc.
/api/v1/sms/sent/messages/10  /         # Get 10 messages.
/api/v1/sms/sent/messages/10/desc/      # Get 10 messages, ordered desc.
/api/v1/sms/sent/messages/10/asc/       # Get 10 messages, orderrd asc.
/api/v1/sms/sent/message/5/             # Get a single message by ID (with ID of 5).
/api/v1/sms/sent/messages/

# Messages were recieved

/api/v1/sms/inbox/messages/             # get all recieved messages.
/api/v1/sms/inbox/messages/10/          # get all recieved messages, limited to to records.
/api/v1/sms/inbox/messages/asc/         # get all recieved messages, ordered asc.
/api/v1/sms/inbox/messages/desc/        # get all recieved messages, ordered desc.
/api/v1/sms/inbox/messages/10/desc/     # Get 10 messages, , ordered desc.
/api/v1/sms/inbox/messages/10/desc/     # Get 10 messages, , ordered asc.
```