<?php
//**********************************************************************
// Eksterne afhængigheder
//**********************************************************************
define('FR_EXT_RAPI_ACL', "https://annie.web.jbank.dk/api/user/acl");


/**
 * FR_EXT_WS_SERVER
 * @global string FR_EXT_WS_SERVER, den server hvor der udstilles webservices in-house.
 */
define('FR_EXT_WS_SERVER', "sdca2adl");


/**
 * FR_EXT_WS_KUNDEGRUPPER
 * @global string FR_EXT_WS_KUNDEGRUPPER, corebank webservice getKundegrupper.
 */
define('FR_EXT_WS_KUNDEGRUPPER', "{
		\"wsdl\":\"http://".FR_EXT_WS_SERVER."/portal/webservices/corebank.service.php?WSDL\",
		\"function\":\"getKundegrupper\"
		}");


/**
 * FR_EXT_WS_CUSTOMERSEARCH
 * @global string FR_EXT_WS_CUSTOMERSEARCH, corebank webservice searchCustomer.
 */
define('FR_EXT_WS_CUSTOMERSEARCH', "{
		\"wsdl\":\"http://".FR_EXT_WS_SERVER."/portal/webservices/corebank.service.php?WSDL\",
		\"function\":\"searchCustomer\"
		}");


/**
 * FR_EXT_WS_KUNDEHEADER
 * @global string FR_EXT_WS_KUNDEHEADER, kundeheader fra anna.web.jbank.dk.
 */
define('FR_EXT_WS_KUNDEHEADER', "{
		\"wsdl\":\"http://anna.web.jbank.dk/portal/webservices/customer.service.php?WSDL\",
		\"function\":\"getHTMLHeader\"
		}");





/**
 * FR_EXT_WS_ENGAGEMENT
 * @global string FR_EXT_WS_ENGAGEMENT, corebank webservice getEngagement.
 */
define('FR_EXT_WS_ENGAGEMENT', "{
		\"wsdl\":\"http://".FR_EXT_WS_SERVER."/portal/webservices/corebank.service.php?WSDL\",
		\"function\":\"getEngagement\"
		}");


/**
 * FR_EXT_WS_CREATEACCOUNT
 * @global string FR_EXT_WS_CREATEACCOUNT, corebank webservice createAccount.
 */
define('FR_EXT_WS_CREATEACCOUNT', "{
		\"wsdl\":\"http://".FR_EXT_WS_SERVER."/portal/webservices/corebank.service.php?WSDL\",
		\"function\":\"createAccount\"
		}");


/**
 * FR_EXT_WS_READACCOUNT
 * @global string FR_EXT_WS_READACCOUNT, corebank webservice readAccount.
 */
define('FR_EXT_WS_READACCOUNT', "{
		\"wsdl\":\"http://".FR_EXT_WS_SERVER."/portal/webservices/corebank.service.php?WSDL\",
		\"function\":\"readAccount\"
		}");



/**
 * FR_EXT_WS_READACCOUNT_SIMPLE
 * @global string FR_EXT_WS_READACCOUNT_SIMPLE, corebank webservice readAccountSimple.
 */
define('FR_EXT_WS_READACCOUNT_SIMPLE', "{
		\"wsdl\":\"http://".FR_EXT_WS_SERVER."/portal/webservices/corebank.service.php?WSDL\",
		\"function\":\"readAccountSimple\"
		}");


/**
 * FR_EXT_WS_LEOENGA
 * @global string FR_EXT_WS_LEOENGA, LEO engagement.
 */
define('FR_EXT_WS_LEOENGA', "{
		\"wsdl\":\"http://9217-domksprd01:81/sdc/leo/leo_sag.nsf/engagement?wsdl\",
		\"function\":\"getLeoData\"
		}");


/**
 * FR_EXT_WS_PRODUKTOVERSIGT
 * @global string FR_EXT_WS_PRODUKTOVERSIGT, Produktoversigten.
 */
define('FR_EXT_WS_PRODUKTOVERSIGT', "{
		\"wsdl\":\"http://anna.web.jbank.dk/portal/webservices/produktoversigt.service.php?wsdl\",
		\"function\":\"getAll\"
		}");


/**
 * FR_EXT_WS_EASYSHEET_CALL_USER_FUNCTION
 * @global string FR_EXT_WS_EASYSHEET_CALL_USER_FUNCTION, brugerfunktioner til EasySheet.
 */
define('FR_EXT_WS_EASYSHEET_CALL_USER_FUNCTION', "{
		\"wsdl\":\"http://anna.web.jbank.dk/portal/webservices/easysheet.service.php?wsdl\",
		\"function\":\"callUserFunction\"
		}");


/**
 * FR_EXT_WS_EASYSHEET_CALL_VALIDATE_FUNCTION
 * @global string FR_EXT_WS_EASYSHEET_CALL_VALIDATE_FUNCTION, validatefunktioner til EasySheet.
 */
define('FR_EXT_WS_EASYSHEET_CALL_VALIDATE_FUNCTION', "{
		\"wsdl\":\"http://anna.web.jbank.dk/portal/webservices/easysheet.service.php?wsdl\",
		\"function\":\"callValidateFunction\"
		}");


/**
 * FR_EXT_WS_EASYSHEET_GET_LETTER
 * @global string FR_EXT_WS_EASYSHEET_GET_LETTER, html-breve til EasySheet.
 */
define('FR_EXT_WS_EASYSHEET_GET_LETTER', "{
		\"wsdl\":\"http://anna.web.jbank.dk/portal/webservices/easysheet.service.php?wsdl\",
		\"function\":\"getLetter\"
		}");




/**
 * FR_EXT_RESTURL_EJENDOMME
 */
define('FR_EXT_RESTURL_EJENDOMME', "http://sdca2adl/portal/webservices/rest/getEjendommeByIp.php");
define('FR_EXT_RESTURL_SIKKERHEDER', "http://sdca2adl/portal/webservices/rest/getSikkerhederAndDetailsByIp.php");
define('FR_EXT_RESTURL_HAEFTELSER', "http://sdca2adl/portal/webservices/rest/getHaeftelserEffekt.php");
define('FR_EXT_RESTURL_EFFECTVALUE', "http://sdca2adl/portal/webservices/rest/getValueEffect.php");




