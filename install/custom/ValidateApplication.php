<?php
/*
 * base on Damijan Cavar code
 * developed by open-ecommerce.org
 */

class ValidateApplication implements IWebApplicationInstaller_Script {

    /**
     * @see IWebApplicationInstaller_Script::run()
     */
    public function run() {
        $wai = WAI::getInstance();

        if ($wai->do_validate === false) {
            return true;
        }

        $application = $wai->getRequest('application');
        if ((empty($application)) or ($application == 'Please select an application' )) {
            WAI::warningMsg('Please select an application');
            $wai->progresion = 0;
            return true;
        } else {
            $wai->setAppliction();
        }
    }

    /**
     * @see IWebApplicationInstaller_CustomScript::getErrorMsg()
     */
    public function getErrorMsg() {
        return '';
    }

}

?>