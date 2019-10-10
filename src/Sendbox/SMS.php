<?php
/**
 * Mail.ru for business Sendbox SMS service API
 *
 * @link https://help.mail.ru/biz/sendbox/api/sms
 * @license MIT
 * @author Igor Sazonov <sovletig@gmail.com>
 */

namespace Tigusigalpa\MailRu\Sendbox;

class SMS extends \Tigusigalpa\MailRu\Sendbox\Sendbox
{
    public function __construct($id, $secret)
    {
        parent::__construct($id, $secret);
    }

    public function addPhones($addressBookId, array $phones)
    {
        return $this->handleRequest(
            'sms/numbers',
            'post',
            [
                'addressBookId' => $addressBookId,
                'phones' => \GuzzleHttp\json_encode($phones)
            ]
        );
    }

    public function updatePhonesVariables($addressBookId, array $phones, array $variables)
    {
        return $this->handleRequest(
            'sms/numbers',
            'put',
            [
                'addressBookId' => $addressBookId,
                'phones' => \GuzzleHttp\json_encode($phones),
                'variables' => \GuzzleHttp\json_encode($variables)
            ]
        );
    }

    public function deletePhones($addressBookId, array $phones)
    {
        return $this->handleRequest(
            'sms/numbers',
            'delete',
            [
                'addressBookId' => $addressBookId,
                'phones' => \GuzzleHttp\json_encode($phones)
            ]
        );
    }

    public function getPhone($addressBookId, $phone)
    {
        return $this->handleRequest(
            'sms/numbers/info/' . $addressBookId . '/' . $phone,
            'get',
            [
                'addressBookId' => $addressBookId,
                'phones' => $phone
            ]
        );
    }

    public function addPhonesToBlackList(array $phones, $description)
    {
        return $this->handleRequest(
            'sms/black_list',
            'post',
            [
                'phones' => \GuzzleHttp\json_encode($phones),
                'description' => $description
            ]
        );
    }

    public function deletePhonesFromBlackList(array $phones)
    {
        return $this->handleRequest(
            'sms/black_list',
            'delete',
            [
                'phones' => \GuzzleHttp\json_encode($phones)
            ]
        );
    }

    public function getBlackList()
    {
        return $this->handleRequest('sms/black_list', 'get');
    }

    public function getBlackListPhonesInfo(array $phones)
    {
        return $this->handleRequest(
            'sms/black_list/by_numbers',
            'get',
            ['phones' => \GuzzleHttp\json_encode($phones)]
        );
    }

    public function createCampaign($sender, $addressBookId, $body, $date, $transliterate)
    {
        return $this->handleRequest(
            'sms/campaigns',
            'post',
            [
                    'sender' => $sender,
                    'addressBookId' => $addressBookId,
                    'body' => $body,
                    'date' => $date,
                    'transliterate' => intval($transliterate)
                ]
        );
    }

    public function createCampaignByPhonesList($sender, array $phones, $body, $transliterate, $date = '')
    {
        $data = [
            'sender' => $sender,
            'phones' => \GuzzleHttp\json_encode($phones),
            'body' => $body,
            'date' => $date,
            'transliterate' => intval($transliterate)
        ];
        if ($date) {
            $data['date'] = $date;
        }
        return $this->handleRequest('sms/send', 'post', $data);
    }

    public function getCampaigns($dateFrom, $dateTo)
    {
        return $this->handleRequest(
            'sms/campaigns/list',
            'get',
            [
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo
            ]
        );
    }

    public function getCampaign($id)
    {
        return $this->handleRequest('sms/campaigns/info/' . $id, 'get', ['id' => $id]);
    }

    public function cancelCampaign($id)
    {
        return $this->handleRequest('sms/campaigns/cancel/' . $id, 'put', ['id' => $id]);
    }

    public function getCampaignCost($addressBookId, $sender, array $phones, $body)
    {
        return $this->handleRequest(
            'sms/campaigns/cost',
            'get',
            [
                'addressBookId' => $addressBookId,
                'sender' => $sender,
                'phones' => \GuzzleHttp\json_encode($phones),
                'body' => $body
            ]
        );
    }

    public function deleteCampaign($id)
    {
        return $this->handleRequest('sms/campaigns', 'delete', ['id' => $id]);
    }

    public function addPhonesWithVariables($addressBookId, array $phones)
    {
        return $this->handleRequest(
            'sms/numbers/variables',
            'post',
            [
                'addressBookId' => $addressBookId,
                'phones' => \GuzzleHttp\json_encode($phones)
            ]
        );
    }
}
