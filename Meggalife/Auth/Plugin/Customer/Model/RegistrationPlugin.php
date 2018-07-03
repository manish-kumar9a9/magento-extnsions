<?php
    namespace Meggalife\Auth\Plugin\Customer\Model;
    use Magento\Customer\Model\Registration;
    class RegistrationPlugin
    {
        /**
         * @param Registration $subject
         * @param boolean $result
         */
        public function afterIsAllowed(Registration $subject, $result)
        {
            return false;
        }
    }
?>