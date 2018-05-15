<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 5/14/2018
 * Time: 5:13 PM
 */

namespace ALS;

require_once 'firebase/firebaseLib.php';
require_once 'firebase/firebaseStub.php';
error_reporting(-1);

class fBase
{

    const DEFAULT_URL = 'https://als-login-api.firebaseio.com/';
    const DEFAULT_TOKEN = '7v1F6tvKBwQwsxdRbJIjH1kEeXHsUYmdXciX1q2b';
    const DEFAULT_PATH = '';
    private $fBase;

    public function __construct()
    {

        // This assumes that you have placed the Firebase credentials in the same directory
        // as this PHP file.
        $this->fBase = new \Firebase\FirebaseLib(fBase::DEFAULT_URL, fBase::DEFAULT_TOKEN);
        //return $fBase;
    }

    public function get()
    {
        return $this->fBase;
    }
}

$firebase = new fBase();