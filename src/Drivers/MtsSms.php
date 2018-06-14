<?php

namespace Neurohotep\LaravelSms\Drivers;

use SoapClient;
use SoapFault;

class MtsSms extends SmsDriver
{
    private $login, $password, $default_user_group, $wsdl;

    public function __construct($login, $password, $user_group)
    {
        $this->login = $login;
        $this->password = $password;
        $this->default_user_group = $user_group;
        $this->wsdl = new SoapClient('https://www.mcommunicator.ru/m2m/m2m_api.asmx?WSDL', [
            'soap_version' => SOAP_1_1
        ]);
    }

    /**
     * Sending a single message
     *
     * @param string $phone
     * @param string $message
     * @return bool|mixed
     */
    public function send(string $phone = '', string $message = '')
    {
        if (!empty($phone) && !empty($message)) {
            try {
                return $this->wsdl->SendMessage([
                    'msid' => $this->phoneParse($phone),
                    'message' => $message,
                    'login' => $this->login,
                    'password' => $this->password,
                    'trace' => 1,
                    'exceptions' => 1
                ]);
            } catch (SoapFault $e) {
                // add handler
                return false;
            } catch (\Exception $e) {
                return false;
            }
        }
        return false;
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
        if (!empty($phones) && !empty($message)) {
            try {
                $phones = array_map(function ($phone) {
                    return $this->phoneParse($phone);
                }, $phones);

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
        return false;
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

            return $this->wsdl->AddUser([
                'userName' => $username,
                'userMSID' => $this->phoneParse($phone),
                'userEmail' => $email,
                'accessLevel' => $base_user,
                'userGroupId' => $user_group,
                'webAccessEnabled' => false,
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
}
