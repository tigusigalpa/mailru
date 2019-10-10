<?php

namespace Tigusigalpa\MailRu\Tests\Sendbox;

use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    private $stub = null;

    public function setUp()
    {
        $this->stub = $this->getMockBuilder(\Tigusigalpa\MailRu\Sendbox\Email::class);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testClientID()
    {
        return getenv('CLIENT_ID');
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testClientSecret()
    {
        return getenv('CLIENT_SECRET');
    }

    /**
     * @depends testClientID
     * @depends testClientSecret
     * @doesNotPerformAssertions
     */
    public function testGetMock($clientId, $clientSecret)
    {
        return $this->stub->setConstructorArgs([$clientId, $clientSecret])
            ->enableProxyingToOriginalMethods()
            ->getMock();
    }

    /**
     * @depends testGetMock
     */
    public function testCreateAddressBook($mock)
    {
        $mock->method('createAddressBook');
        $result = $mock->createAddressBook('Test');
        $this->assertIsInt($result);
        if (is_array($result)) {
            if (isset($result['errors']) && !empty($result['errors'])) {
                $this->fail('Request errors: ' . print_r($result['errors'], 1));
            } else {
                $this->fail('Create address book request fails');
            }
        }
        return (filter_var($result, FILTER_VALIDATE_INT) === false) ? 0 : $result;
    }

    /**
     * @depends testGetMock
     * @depends testCreateAddressBook
     */
    public function testEditAddressBook($mock, $id)
    {
        $mock->method('editAddressBook');
        $result = $mock->editAddressBook($id, 'TestEdited');
        $this->assertEquals(1, $result);
        if (is_array($result)) {
            if (isset($result['errors']) && !empty($result['errors'])) {
                $this->fail('Request errors: ' . print_r($result['errors'], 1));
            } else {
                $this->fail('Edit address book request fails');
            }
        }
    }

    /**
     * @depends testGetMock
     */
    public function testGetAddressBooks($mock)
    {
        $mock->method('getAddressBooks');
        $result = $mock->getAddressBooks();
        $this->assertIsArray($result);
        if (is_array($result)) {
            if (!empty($result)) {
                if (!isset($result[0]) || !isset($result[0]['id']) || !isset($result[0]['name'])) {
                    $this->fail('Get address books wrong array: ' . print_r($result, 1));
                } else {
                    if ($result[0]['name'] == 'TestEdited') {
                        return $result;
                    } else {
                        $this->fail('Edit address book request fails on rename');
                    }
                }
            }
        }
        return [];
    }

    /**
     * @depends testGetMock
     * @depends testGetAddressBooks
     */
    public function testGetAddressBookInfo($mock, array $books)
    {
        $mock->method('getAddressBookInfo');
        $id = $books[0]['id'];
        $result = $mock->getAddressBookInfo($id);
        $this->assertIsArray($result);
    }

    /**
     * @depends testGetMock
     * @depends testGetAddressBooks
     */
    public function testGetAddressBookVariables($mock, array $books)
    {
        $mock->method('getAddressBookVariables');
        $id = $books[0]['id'];
        $result = $mock->getAddressBookVariables($id);
        $this->assertIsArray($result);
    }

    /**
     * @depends testGetMock
     * @depends testGetAddressBooks
     */
    public function testAddAddressBookEmails($mock, array $books)
    {
        $mock->method('addAddressBookEmails');
        $id = $books[0]['id'];
        $result = $mock->addAddressBookEmails(
            $id,
            [
                'test1@test.com',
                'test2@test.com',
                'test3@test.com',
            ]
        );
        $this->assertEquals(1, $result);
    }

    /**
     * @depends testGetMock
     * @depends testGetAddressBooks
     */
    public function testGetAddressBookEmails($mock, array $books)
    {
        //Wait for API takes effect
        sleep(5);
        $mock->method('getAddressBookEmails');
        $id = $books[0]['id'];
        $result = $mock->getAddressBookEmails($id);
        $this->assertIsArray($result);
        if (is_array($result)) {
            if (!empty($result)) {
                if (!isset($result[0]) || !isset($result[0]['email'])) {
                    $this->fail('Get address book emails wrong array: ' . print_r($result, 1));
                }
            }
        }
    }

    /**
     * @depends testGetMock
     * @depends testGetAddressBooks
     */
    public function testDeleteAddressBookEmails($mock, array $books)
    {
        $mock->method('deleteAddressBookEmails');
        $id = $books[0]['id'];
        $result = $mock->deleteAddressBookEmails(
            $id,
            [
                'test1@test.com',
                'test3@test.com',
            ]
        );
        $this->assertEquals(1, $result);
    }

    /**
     * @depends testGetMock
     * @depends testGetAddressBooks
     */
    public function testGetAddressBookEmail($mock, array $books)
    {
        $mock->method('getAddressBookEmail');
        $id = $books[0]['id'];
        $result = $mock->getAddressBookEmail($id, 'test2@test.com');
        $this->assertIsArray($result);
        if (is_array($result)) {
            if (!empty($result)) {
                if (!isset($result['email']) || !isset($result['abook_id'])) {
                    $this->fail('Get address book email wrong array: ' . print_r($result, 1));
                }
            }
        }
    }

    /**
     * @depends testGetMock
     * @depends testGetAddressBooks
     */
    public function testDeleteAddressBook($mock, array $books)
    {
        $mock->method('deleteAddressBook');
        $id = $books[0]['id'];
        $result = $mock->deleteAddressBook($id);
        $this->assertEquals(1, $result);
    }
}
