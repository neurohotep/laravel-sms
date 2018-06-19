<?php

namespace Neurohotep\LaravelSms\Drivers;

use SoapClient;
use SoapFault;

class MtsSms extends SmsDriver
{
    private $wsdl, $param, $default_user_group;

    public function __construct($login, $password, $user_group)
    {
        $this->default_user_group = $user_group;

        $this->wsdl = new SoapClient('https://www.mcommunicator.ru/m2m/m2m_api.asmx?WSDL', [
            'soap_version' => SOAP_1_1
        ]);

        $this->param = [
            'login' => $login,
            'password' => $password,
            'trace' => 1,
            'exceptions' => 1
        ];
    }

    /**
     * Send a single or multiple message
     *
     * @param null $phone
     * @param string $message
     * @param array $config
     * @return bool
     */
    public function send($phone = null, string $message = '', array $config = [])
    {
        if (empty($phone) || empty($message)) {
            return false;
        }

        try {
            if (\is_array($phone)) {
                $phone = $this->phoneParseArray($phone);
                if (!empty($phone)) {
                    return $this->wsdl->SendMessages(array_merge ([
                        'msids' => $phone,
                        'message' => $message
                    ], $this->param));
                }
                return false;
            }
            return $this->wsdl->SendMessage(array_merge([
                'msid' => $this->phoneParse($phone),
                'message' => $message
            ], $this->param));
        } catch (SoapFault $e) {
            // add handler
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get a message status
     *
     * @param null $message_id
     * @return bool
     */
    public function getMessageStatus($message_id = null)
    {
        if (!$message_id) {
            return false;
        }

        try {
            if (\is_array($message_id)) {
                return $this->wsdl->GetMessagesStatus(array_merge([
                    'messageIDs' => $message_id
                ], $this->param));
            }
            return $this->wsdl->GetMessageStatus(array_merge([
                'messageID' => $message_id
            ], $this->param));
        } catch (SoapFault $e) {
            // add handler
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Sending the same messages to several subscribers
     * 
     * @param array $phones
     * @param string $message
     * @return bool|mixed
     */
    public function sendMultiple(array $phones, string $message = '')
    {
        if (empty($phones) || empty($message)) {
            return false;
        }

        try {
            $phones = $this->phoneParseArray($phones);
            return $this->wsdl->SendMessages([
                'msids' => $phones,
                'message' => $message,
                'login' => $this->login,
                'password' => $this->password,
                'trace' => 1,
                'exceptions' => 1
            ]);
        } catch (SoapFault $e) {
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * User creation function
     * 
     * @param string $username
     * @param string $phone
     * @param string $email
     * @param null $user_group
     * @return bool|mixed
     */
    public function addUser(string $username = '', string $phone = '', string $email = '', $user_group = null, $base_user = 'BaseUser')
    {
        if (empty($phone) || empty($username)) {
            return false;
        }

        if (!in_array($base_user, ['Administrator', 'Operator', 'BaseUser'])) {
            return false;
        }

        try {
            if (!$user_group) {
                $user_group = $this->default_user_group;
            }
            return $this->wsdl->AddUser(array_merge([
                'userName' => $username,
                'userMSID' => $this->phoneParse($phone),
                'userEmail' => $email,
                'accessLevel' => $base_user,
                'userGroupId' => $user_group,
                'webAccessEnabled' => false,
            ], $this->param));
        } catch (SoapFault $e) {
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Parsing phone number
     * 
     * @param string $phone
     * @return string
    */
    private function phoneParse($phone = '')
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        $replace = preg_replace('/^([8]{1})([0-9]{10}$)/', '7$2', $phone);
        if (!empty($replace)) {
            $phone = $replace;
        }
        return $phone;
    }

    /**
     * Parsing an array of phone numbers
     *
     * @param array $phones
     * @return array
     */
    private function phoneParseArray(array $phones)
    {
        $phones = array_filter($phones, function($phone) {
            return !empty($phone);
        });
        return array_map(function ($phone) {
            return $this->phoneParse($phone);
        }, $phones);
    }
}
