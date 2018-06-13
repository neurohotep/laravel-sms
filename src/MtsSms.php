<?php

namespace Neirototam\MtsCommunicator;

use SoapClient;
use SoapFault;

class MtsSms
{
    private $login;
    private $password;

    public function __construct($login, $password)
    {
        $this->login = $login;
        $this->password = $password;
    }

    public function send(string $phone = "", string $message = "")
    {
        if (!empty($phone) && !empty($message)) {
            try {
                $phone = $this->phoneParse($phone);

                $wsdl = new SoapClient('https://www.mcommunicator.ru/m2m/m2m_api.asmx?WSDL', [
                    'soap_version' => SOAP_1_1
                ]);

                return $wsdl->SendMessage([
                    'msid' => $phone,
                    'message' => $message,
                    'login' => $this->login,
                    'password' => $this->password,
                    'trace' => 1,
                    'exceptions' => 1
                ]);
            } catch (SoapFault $e) {
                //trigger_error('Ошибка авторизации или внутренняя ошибка сервера.', E_ERROR);
                return false;
            }
        }
        return false;
    }

    public function sendMultiple(array $phones, string $message = "")
    {
        if (!empty($phones) && !empty($message)) {
            try {
                $phones = array_map(function ($phone) {
                    return $this->phoneParse($phone);
                }, $phones);

                $wsdl = new SoapClient("https://www.mcommunicator.ru/m2m/m2m_api.asmx?WSDL", [
                    'soap_version' => SOAP_1_1
                ]);

                return $wsdl->SendMessages([
                    'msids' => $phones,
                    'message' => $message,
                    'login' => $this->login,
                    'password' => $this->password,
                    'trace' => 1,
                    'exceptions' => 1
                ]);
            } catch (SoapFault $e) {
                //trigger_error('Ошибка авторизации или внутренняя ошибка сервера.', E_ERROR);
                return false;
            } catch (\Exception $e) {
                return false;
            }
        }
        return false;
    }

    public function addUser(string $username = '', string $phone = '', string $email = '')
    {
        if (!empty($phone) && !empty($username) && !empty($email)) {
            try {
                $wsdl = new SoapClient('https://www.mcommunicator.ru/m2m/m2m_api.asmx?WSDL', [
                    'soap_version' => SOAP_1_1
                ]);

                $phone = $this->phoneParse($phone);
                
                return $wsdl->AddUser([
                    'userName' => $username,
                    'userMSID' => $phone,
                    'userEmail' => $email,
                    'accessLevel' => 'BaseUser',
                    'userGroupId' => 29527,
                    'webAccessEnabled' => false,
                    'login' => $this->login,
                    'password' => $this->password,
                    'trace' => 1,
                    'exceptions' => 1
                ]);
            } catch (SoapFault $e) {
                //trigger_error('Ошибка авторизации или внутренняя ошибка сервера.', E_ERROR);
                return false;
            }
        }
        return false;
    }

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
