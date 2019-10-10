<?php
/**
 * @link https://help.mail.ru/biz/sendbox/api/email
 */

namespace Tigusigalpa\MailRu\Sendbox;

class Email extends \Tigusigalpa\MailRu\Sendbox\Sendbox
{
    public function __construct($id, $secret)
    {
        parent::__construct($id, $secret);
    }

    /**
     * Create an address book
     *
     * @param string $bookName Address book name
     *
     * @return array|int
     */
    public function createAddressBook($bookName)
    {
        if ($response = $this->request('addressbooks', ['bookName' => $bookName], true)) {
            if (isset($response['id']) && !empty($response['id'])) {
                return $response['id'];
            } else {
                $this->setError('Create AddressBook: empty id');
            }
        }
        return $this->getErrors();
    }

    /**
     * @param int $id
     * @param string $bookName Address book name
     *
     * @return array|bool
     */
    public function editAddressBook($id, $bookName)
    {
        if ($bookName) {
            return $response = $this->handleRequest(
                'addressbooks/' . $id,
                'put',
                ['id' => $id, 'name' => $bookName],
                true
            );
        }
        //TODO
        return $this->getErrors();
    }

    /**
     * Get address books list
     *
     * @param int $limit
     * @param int $offset
     *
     * @return array
     */
    public function getAddressBooks($limit = 0, $offset = 0)
    {
        $data = [];
        if ($limit > 0) {
            $data['limit'] = $limit;
        }
        if ($offset > 0) {
            $data['offset'] = $offset;
        }
        return $this->handleRequest('addressbooks', 'get', $data);
    }

    /**
     * Get address book info
     *
     * @param int $id Address book id
     *
     * @return array
     */
    public function getAddressBookInfo($id)
    {
        return $this->handleRequest('addressbooks/' . $id, 'get');
    }

    /**
     * Get address book variables
     *
     * @param int $id Address book id
     *
     * @return array
     */
    public function getAddressBookVariables($id)
    {
        return $this->handleRequest('addressbooks/' . $id . '/variables', 'get');
    }

    /**
     * Get address book emails
     *
     * @param int $id Address book id
     * @param int $limit
     * @param int $offset
     *
     * @return array
     */
    public function getAddressBookEmails($id, $limit = 0, $offset = 0)
    {
        $data = ['id' => $id];
        if ($limit > 0) {
            $data['limit'] = $limit;
        }
        if ($offset > 0) {
            $data['offset'] = $offset;
        }
        return $this->handleRequest('addressbooks/' . $id . '/emails', 'get', $data);
    }

    /**
     * Add emails to the address book
     *
     * @param int $id Address book id
     * @param array $emails Array of emails
     * @param bool $confirmation
     * @param string $senderEmail
     *
     * @return array|bool
     */
    public function addAddressBookEmails($id, array $emails, $confirmation = false, $senderEmail = '')
    {
        $data = [
            'id' => $id,
            'emails' => serialize($emails)
        ];
        if ($confirmation) {
            if ($senderEmail) {
                $data['email'] = $data['emails'];
                unset($data['emails']);
                $data['confirmation'] = 'force';
                $data['sender_email'] = $senderEmail;
            } else {
                $this->setError('Add emails to address book: sender_email is required for confirmation');
            }
        }
        return $this->handleRequest('addressbooks/' . $id . '/emails', 'post', $data, true);
    }

    /**
     * Delete emails from address book
     *
     * @param int $id Address book id
     * @param array $emails
     *
     * @return array|bool
     */
    public function deleteAddressBookEmails($id, array $emails)
    {
        return $this->handleRequest(
            'addressbooks/' . $id . '/emails',
            'delete',
            [
                'id' => $id,
                'emails' => serialize($emails)
            ],
            true
        );
    }

    /**
     * Get email info from address book
     *
     * @param int $id Address book id
     * @param string $email
     *
     * @return array
     */
    public function getAddressBookEmail($id, $email)
    {
        return $this->handleRequest(
            'addressbooks/' . $id . '/emails/' . $email,
            'get',
            [
                'id' => $id,
                'email' => $email
            ]
        );
    }

    /**
     * Delete address book
     *
     * @param int $id Address book id
     *
     * @return array|bool
     */
    public function deleteAddressBook($id)
    {
        return $this->handleRequest('addressbooks/' . $id, 'delete', ['id' => $id], true);
    }

    /**
     * Get address book cost
     *
     * @param int $id Address book id
     *
     * @return array
     */
    public function getAddressBookCost($id)
    {
        return $this->handleRequest('addressbooks/' . $id . '/cost', 'get', ['id' => $id]);
    }

    /**
     * Create mail campaign
     *
     * @param string $senderName
     * @param string $senderEmail
     * @param string $subject
     * @param string $body
     * @param string $listId
     * @param string $sendDate
     * @param string $name
     * @param array $attachments
     * @param string $type
     *
     * @return array
     */
    public function createCampaign(
        $senderName,
        $senderEmail,
        $subject,
        $body,
        $listId,
        $sendDate,
        $name,
        array $attachments = [],
        $type = ''
    ) {
        $data = [
            'sender_name' => $senderName,
            'sender_email' => $senderEmail,
            'subject' => $subject,
            'body' => $body,
            'list_id' => $listId,
            'send_date' => $sendDate,
            'name' => $name
        ];
        if ($attachments) {
            $data['attachments'] = serialize($attachments);
        }
        if ($type) {
            switch ($type) {
                case 'draft':
                    $data['type'] = $type;
                    break;
            }
        }
        return $this->handleRequest('campaigns', 'post', $data);
    }

    /**
     * Get campaign info
     *
     * @param string|int $id Mail campaign id
     *
     * @return array
     */
    public function getCampaign($id)
    {
        return $this->handleRequest('campaigns/' . $id, 'get', ['id' => $id]);
    }

    /**
     * Get email info from mail campaign
     *
     * @param string|int $id Mail campaign id
     * @param string $email
     *
     * @return array
     */
    public function getCampaignEmailInfo($id, $email)
    {
        return $this->handleRequest('campaigns/' . $id . '/email/' . $email, 'get');
    }

    /**
     * Get campaigns
     *
     * @param int $limit
     * @param int $offset
     *
     * @return array
     */
    public function getCampaigns($limit = 0, $offset = 0)
    {
        $data = [];
        if ($limit > 0) {
            $data['limit'] = $limit;
        }
        if ($offset > 0) {
            $data['offset'] = $offset;
        }
        return $this->handleRequest('campaigns', 'get', $data);
    }

    /**
     * Get mail campaign countries stats
     *
     * @param string|int $id Mail campaign id
     *
     * @return array
     */
    public function getCampaignStatByCountry($id)
    {
        return $this->handleRequest('campaigns/' . $id . '/countries', 'get', ['id' => $id]);
    }

    /**
     * Get mail campaign referrals stats
     *
     * @param string|int $id Mail campaign id
     *
     * @return array
     */
    public function getCampaignReferrals($id)
    {
        return $this->handleRequest('campaigns/' . $id . '/referrals', 'get', ['id' => $id]);
    }

    /**
     * Delete mail campaign
     *
     * @param string|int $id Mail campaign id
     *
     * @return array|bool
     */
    public function deleteCampaign($id)
    {
        return $this->handleRequest('campaigns/' . $id, 'delete', ['id' => $id], true);
    }

    /**
     * Create mail template
     *
     * @param string $name
     * @param string $body
     *
     * @return array|bool
     */
    public function createTemplate($name, $body)
    {
        return $this->handleRequest('template', 'post', ['name' => $name, 'body' => $body], true);
    }

    /**
     * @param string|int $id Template id
     *
     * @return array
     */
    public function getTemplate($id)
    {
        return $this->handleRequest('template/' . $id, 'get');
    }

    /**
     * Get templates info
     *
     * @return array
     */
    public function getTemplates()
    {
        return $this->handleRequest('templates', 'get');
    }

    /**
     * Get system templates with given language
     *
     * @param string $lang
     *
     * @return array
     */
    public function getSystemTemplates($lang = '')
    {
        $lang = ($lang && in_array($lang, parent::LANGS)) ? $lang : $this->lang;
        return $this->handleRequest('templates/' . $lang, 'get', ['owner' => 'sendbox']);
    }

    /**
     * Get user templates
     *
     * @return array
     */
    public function getUserTemplates()
    {
        return $this->handleRequest('templates', 'get', ['owner' => 'me']);
    }

    /**
     * Get user templates with given language
     *
     * @param string $lang
     *
     * @return array
     */
    public function getUserTemplatesLang($lang = '')
    {
        $lang = ($lang && in_array($lang, parent::LANGS)) ? $lang : $this->lang;
        return $this->handleRequest('templates/' . $lang, 'get', ['owner' => 'me']);
    }

    /**
     * Get senders list
     *
     * @return array
     */
    public function getSenders()
    {
        return $this->handleRequest('senders', 'get');
    }

    /**
     * Create sender
     *
     * @param string $name
     * @param string $email
     *
     * @return array|bool
     */
    public function createSender($name, $email)
    {
        return $this->handleRequest('senders', 'post', ['name' => $name, 'email' => $email], true);
    }

    /**
     * Delete sender
     *
     * @param string $email
     *
     * @return array|bool
     */
    public function deleteSender($email)
    {
        return $this->handleRequest('senders', 'delete', ['email' => $email], true);
    }

    /**
     * Activate sender
     *
     * @param string $email
     * @param string $code
     *
     * @return array|bool
     */
    public function activateSender($email, $code)
    {
        return $this->handleRequest('senders/' . $email . '/code', 'post', ['code' => $code]);
    }

    /**
     * Get activation code
     *
     * @param string $email
     *
     * @return array|bool
     */
    public function getActivationCode($email)
    {
        return $this->handleRequest('senders/' . $email . '/code', 'get');
    }

    /**
     * Get email info
     *
     * @param string $email
     *
     * @return array
     */
    public function getEmail($email)
    {
        return $this->handleRequest('emails/' . $email, 'get');
    }

    /**
     * Delete email info
     *
     * @param string $email
     *
     * @return array|bool
     */
    public function deleteEmail($email)
    {
        return $this->handleRequest('emails/' . $email, 'delete', [], true);
    }

    /**
     * Get email campaigns
     *
     * @param string $email
     *
     * @return array
     */
    public function getEmailCampaigns($email)
    {
        return $this->handleRequest('emails/' . $email . '/campaigns', 'get');
    }

    /**
     * Get black list
     *
     * @return array
     */
    public function getBlackList()
    {
        return $this->handleRequest('blacklist', 'get');
    }

    /**
     * Add email to the black list
     *
     * @param array $emails
     * @param string $comment
     *
     * @return array|bool
     */
    public function addEmailsToBlackList(array $emails, $comment = '')
    {
        $data = ['emails' => join(',', $emails)];
        if ($comment) {
            $data['comment'] = $comment;
        }
        return $this->handleRequest('blacklist', 'post', $data, true);
    }

    /**
     * Delete emails from the black list
     *
     * @param array $emails
     *
     * @return array|bool
     */
    public function deleteEmailsFromBlackList(array $emails)
    {
        return $this->handleRequest('blacklist', 'delete', ['emails' => join(',', $emails)], true);
    }

    /**
     * Get user balance
     *
     * @param string $currency
     *
     * @return array|bool
     */
    public function getUserBalance($currency = '')
    {
        return $this->handleRequest('balance' .
            (($currency && in_array($currency, self::CURRENCIES)) ? '/' . $currency : ''), 'get');
    }
}
