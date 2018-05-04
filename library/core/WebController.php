<?php

abstract class FR_WebController extends FR_WebModule
{

    /**
     *
     * @var FR_User
     */
    protected $user;

    public function __construct()
    {
        parent::__construct();
    }

    private function setUser(FR_User $user)
    {
        $this->user = $user;
    }

    public function authenticate()
    {
        $access = false;


        // **************************************************************
        // Session
        // **************************************************************
        if (Session::GetOrFalse("userdata")) {

            $this->setUser(new FR_User());
            $access = true;

        } else {


            $auth = filter_input(INPUT_SERVER, "HTTP_AUTHORIZATION");
            if (!$auth) {
                // Fix til dev-linux-maskiner og deres postman ..
                if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
                    $auth = "Basic " . base64_encode($_SERVER['PHP_AUTH_USER'] . ":" . $_SERVER['PHP_AUTH_PW']);
                }
            }

            $httpUser = null;
            $httpPass = null;
            $userTicket = null;
            if (mb_strtolower(mb_substr($auth, 0, 6)) == "basic ") {
                $userTicket = mb_substr($auth, 6);
                $ex = explode(":", base64_decode($userTicket), 2);
                if (count($ex) == 2) {
                    $httpUser = $ex[0];
                    $httpPass = $ex[1];
                }
            }


            // **************************************************************
            // HTTP-BASIC
            // **************************************************************
            if (!$access && mb_strlen($httpUser) > 0 && mb_strlen($httpPass) > 0) {

                $user = new FR_User($userTicket);
                if ($user->isValidUser()) {
                    $access = true;
                    $this->setUser($user);
                }
            }


            // **************************************************************
            // SDC Silent logon
            // **************************************************************
            if (!$access) {
                $userTicket = HttpRequest::GetOrFalse("__userTicket");
                if (!$userTicket) {




                    $redirectUrl = $_SERVER['REQUEST_URI'];

                    ?>
                    <script type="text/javascript">
                        try {
                                window.location.href = "/system/login/fallback?r=<?php echo urlencode($redirectUrl); ?>";
                        } catch (err) {
                            alert('Kaj');
                        }
                    </script>
                    <?php
                    die();
                } else {
                    $user = new FR_User($userTicket);
                    if ($user->isValidUser()) {
                        $access = true;
                        $this->setUser($user);
                    }
                }
            }


        }


        // **************************************************************

        if ($access && $this->user->isValidUser()) {
            $this->user->requireAcl("User");
            return true;
        } else {
            return false;
        }
    }

    protected function render($tplFile, $masterTemplate = "default")
    {
        try {
            $this->presenter = "php";
            $this->masterTemplate = $masterTemplate;
            $presenter = FR_Presenter::factory($this->presenter, $this);
            $presenter->display($tplFile);
        } catch (Exception $error) {
            throw new Exception($error->getMessage());
        }
    }

    protected function renderRaw($text = "")
    {
        try {
            $this->presenter = "raw";
            $presenter = FR_Presenter::factory($this->presenter, $this);
            $presenter->display($text);
        } catch (Exception $error) {
            throw new Exception($error->getMessage());
        }
    }

    protected function renderExcelFile($path)
    {
        try {
            $this->presenter = "excel";
            $presenter = FR_Presenter::factory($this->presenter, $this);
            $presenter->display($path);
        } catch (Exception $error) {
            throw new Exception($error->getMessage());
        }
    }

    protected function renderRedirect($url)
    {
        $html = "";
        $html .= "<script type='text/javascript'>";
        $html .= "window.location.href = '" . $url . "';";
        $html .= "</script>";
        $this->renderRaw($html);
    }

    public function __destruct()
    {
        parent::__destruct();
    }

}
