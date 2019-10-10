<?php
/**
 * Mail.ru for business Sendbox SMTP service API
 *
 * @link https://help.mail.ru/biz/sendbox/api/smtp
 * @license MIT
 * @author Igor Sazonov <sovletig@gmail.com>
 */

namespace Tigusigalpa\MailRu\Sendbox;

class SMTP extends \Tigusigalpa\MailRu\Sendbox\Sendbox
{
    public function __construct($id, $secret)
    {
        parent::__construct($id, $secret);
    }

    /**
     * Send email
     *
     * @param array $email
     *
     * @return array|bool
     */
    public function sendEmail(array $email)
    {
        return $this->handleRequest('smtp/emails', 'post', ['email' => serialize($email)], true);
    }

    /**
     * Get email
     *
     * @return array
     */
    public function getEmails()
    {
        return $this->handleRequest('smtp/emails', 'get');
    }

    /**
     * Get emails total count
     *
     * @return array
     */
    public function getEmailsTotal()
    {
        return $this->handleRequest('smtp/emails/total', 'get');
    }

    /**
     * Get email info
     *
     * @param int $id
     *
     * @return array
     */
    //TODO проверить
    public function getEmail($id)
    {
        return $this->handleRequest('smtp/emails/' . $id, 'get', ['id' => $id]);
    }

    /**
     * Get bounces daily
     *
     * @param string $date
     * @param int $limit
     * @param int $offset
     *
     * @return array
     */
    public function getBouncesDaily($date, $limit, $offset = 0)
    {
        return $this->handleRequest('smtp/bounces/day', 'get',
            ['date' => $date, 'limit' => $limit, 'offset' => $offset]);
    }

    /**
     * Get bounces daily total
     *
     * @return array
     */
    public function getBouncesDailyTotal()
    {
        return $this->handleRequest('smtp/bounces/day/total', 'get');
    }

    /**
     * Unuscribe emails
     *
     * @param array $emails
     * @example $emails = [ ['email' => 'email@email.com', 'comment' => 'My comment'], ['email' => 'email@email.com'] ]
     *
     * @return array
     */
    public function unuscribe(array $emails)
    {
        return $this->handleRequest('smtp/unsubscribe', 'post', ['emails' => serialize($emails)], true);
    }

    /**
     * Delete from unuscribe list
     *
     * @param array $emails
     *
     * @return array
     */
    public function deleteFromUnuscribe(array $emails)
    {
        return $this->handleRequest('smtp/unsubscribe', 'delete', ['emails' => serialize($emails)], true);
    }

    /**
     * Get all IP senders list
     *
     * @return array
     */
    public function getIPList()
    {
        return $this->handleRequest('smtp/ips', 'get');
    }

    /**
     * Get allowed domains list
     *
     * @return array
     */
    public function getAllowedDomains()
    {
        return $this->handleRequest('smtp/domains', 'get');
    }

    /**
     * Add domain
     *
     * @param string $email
     *
     * @return array
     */
    public function addDomain($email)
    {
        return $this->handleRequest('smtp/domains', 'post', ['email' => $email], true);
    }

    /**
     * Add domain
     *
     * @param string $email
     *
     * @return array
     */
    public function verifyDomain($email)
    {
        return $this->handleRequest('smtp/domains/' . $email, 'get', ['email' => $email], true);
    }
}
