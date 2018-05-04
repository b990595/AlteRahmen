<?php

abstract class FR_RestController extends FR_RestModule
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

    protected function getAllParams()
    {
        return SystemInternals::GetReguestData();
    }

    public function authenticate()
    {
        $access = false;

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

        // **************************************************

        if ($access && $this->user->isValidUser()) {
            $this->user->requireAcl("User");
            return true;
        } else {
            return false;
        }

    }

    protected function renderRaw($data)
    {
        try {
            header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
            $this->presenter = "raw";
            $presenter = FR_Presenter::factory($this->presenter, $this);
            $presenter->display($data);
            return $this;
        } catch (Exception $error) {
            throw new Exception($error->getMessage());
        }
    }

    /*
      protected function render200OK($data = null, $message = "") {
      return $this->render($data, 200, "OK", $message);
      }

      protected function render201Created($data = null, $message = "") {
      return $this->render($data, 201, "Created", $message);
      }

      protected function render204NoContent() {
      return $this->render(null, 204, "No Content");
      }

      protected function render400BadRequest() {
      return $this->render(null, 400, "Bad Request");
      }

      protected function render403Forbidden() {
      return $this->render(null, 403, "Forbidden");
      }

      protected function render404NotFound() {
      return $this->render(null, 404, "Not Found");
      }

      protected function render405MethodNotAllowed() {
      return $this->render(null, 405, "Method Not Allowed");
      }

      protected function render500InternalServerError() {
      return $this->render(null, 500, "Internal Server Error");
      }

      private function render($data, $code, $text, $message = "") {
      try {
      header($_SERVER["SERVER_PROTOCOL"] . " " . $code . " " . $text);
      $this->presenter = "json";
      $presenter = FR_Presenter::factory($this->presenter, $this);
      $presenter->display($data, $code, $text, $message);
      return $this;
      } catch (Exception $error) {
      throw new Exception($error->getMessage());
      }
      }
     */

    public function __destruct()
    {
        parent::__destruct();
    }

}
