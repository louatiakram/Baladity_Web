<?php

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class OTPGenerator
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function generateOTP($length = 6)
    {
        $otp = '';

        // Generate a random OTP
        for ($i = 0; $i < $length; $i++) {
            $otp .= mt_rand(0, 9);
        }

        // Store the OTP in session
        $this->session->set('otp', $otp);

        return $otp;
    }

    public function getOTP()
    {
        // Retrieve the OTP from session
        return $this->session->get('otp');
    }
}
