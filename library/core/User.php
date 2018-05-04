<?php

/**
 * FR_User
 *
 * Grundlæggende bruger-klasse
 */
class FR_User {

    private $validUser = false;
    private $username = "";
    private $password = "";
    private $acl = array();

    public function __construct($userTicket = false) {
        if ($userTicket) {
            // Ny userTicket ...
            $tmp = base64_decode($userTicket);
            $tmpArray = explode(":", $tmp);
            if (is_array($tmpArray) && count($tmpArray) == 2) {
                $this->username = trim($tmpArray[0]);
                $this->password = trim($tmpArray[1]);
            }
            
            $this->validateUser();
            
            if ($this->isValidUser()) {
                $this->acl = $this->lookupAcl();
                $this->setSession();
            }
        } else {
            // Session eller Header AUTH
            $tmpSession = Session::GetOrFalse("userdata");
            if (isset($tmpSession['username']) && isset($tmpSession['password']) && isset($tmpSession['acl'])) {
                $this->username = $tmpSession['username'];
                $this->password = sha1($tmpSession['password']);
                $this->validateUser();
                if ($this->isValidUser()) {
                    $this->acl = isset($tmpSession['acl']) ? $tmpSession['acl'] : $this->lookupAcl();
                }
            } else {
                Session::delete("userdata");
                throw new Exception("Invalid userdata-session.");
            }
        }
    }


    private function lookupAcl():array {
        return ["User"];
    }

    private function setSession() {
        Session::set("username", $this->username);
        Session::set("userdata", array(
            "username" => $this->username,
            "password" => $this->password,
            "acl" => $this->acl,
        ));
    }

    /**
     * Kræver den angivne ACL
     * @param string $acl
     * @return $this
     */
    public function requireAcl($acl) {
        if (!in_array($acl, $this->acl)) {
            header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden");
            die("Ingen adgang, [" . $acl . "] påkræves.");
        }
        return $this;
    }

    /**
     * Kræver min. én af de angivne ACLs
     * @param array $accessGivingAcls
     * @return $this
     */
    public function requireAclByArray($accessGivingAcls) {
        $access = $this->hasAclAccessByArray($accessGivingAcls);
        if (!$access) {
            header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden");
            die("Ingen adgang, [" . implode(" eller ", $accessGivingAcls) . "] påkræves.");
        }
        return $this;
    }
    
    public function hasAclAccessByArray($accessGivingAcls){
        $access = false;

        foreach ($accessGivingAcls as $acl) {
            if (in_array($acl, $this->acl)) {
                $access = true;
            }
        }
        return $access;
    }

    private function validateUser() {

        $this->validUser = false;

        $this->validUser = true;

    }

    public function isValidUser() {
        return $this->validUser;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getPassword() {
        return $this->password;
    }


}
