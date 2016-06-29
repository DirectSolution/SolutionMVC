<?php

namespace SolutionMvc\Library;

require_once LEG . "mandrill/transport.class.php";

use SolutionMvc\Model\Client,
    SolutionMvc\Model\User,
    SolutionMvc\Model\ConfSettings;

class Mandril {

    protected $user;
    protected $client;
    protected $settings;

    public function __construct() {

        $this->client = new Client();
        $this->user = new User();
        $this->settings = new ConfSettings();
    }

    public function SendMandrillEmail($template, $to, $cid, $uid, $vars, $account = "sales", $subject = "", $attachment = "", $from = "", $images = "") {


        if ($cid != "" && $uid != "") {
            $client = $this->client->getClientById($cid);
            $address = $client['address1'] . "<br />";
            if ($client['address2'] != "") {
                $address .= $client['address2'] . "<br />";
            }
            $address .= $client['city'] . "<br />";
            $address .= $client['county'] . "<br />";
            $address .= $client['postcode'];
            $company = $client['company'];
            $user = $this->user->getOneUserById($uid);
            $email = $user['email'];
        }
################################################################################
        try {
            $md = new \MandrillConnect($template);
            if ($from == "") {
                $md->fromEmail($email, $company);
            } else {
                $md->fromEmail($from['email'], $from['name']);
            }
            foreach ($to as $eaddress) {
                $md->toEmail($eaddress['email'], $eaddress['name'], $eaddress['type']);
            }
            $md->Subject($subject);
            if ($cid != "" && $uid != "") {
                preg_match("/src=\"([^\"]*)/si", $this->settings->getSettings($cid), $m);
                if (substr($m[1], 0, 1) == "/") {
                    $logo = "http://" . $_SERVER['HTTP_HOST'] . $m[1];
                } else {
                    $logo = $m[1];
                }
                $md->getLogo($logo);
            }
            if ($from['email'] == "holidays@eldirect.co.uk") {
                $md->getLogo('https://portal.solutionhost.co.uk/apps/ffe/images/email_header.png');
            } else if ($vars['TYPE'] == "EL") {
                $md->getLogo("http://www.hsdirect.co.uk/images/el_direct_3.png");
            } else if ($vars['TYPE'] == "HS") {
                $md->getLogo("http://www.hsdirect.co.uk/images/SFP.jpg");
            }
            $md->setAccount($account);
            if (count($attachment) > 0) {
                foreach ($attachment as $attachee) {
                    if (substr($attachee, 0, 1) == "/") {
                        $attachee = "https://" . $_SERVER['HTTP_HOST'] . $attachee;
                    }
                    $md->attach($attachee['file'], $attachee['Title']);
                }
            }
            $valArray = Array();
            foreach ($vars as $key => $var) {
                $val['name'] = $key;
                $val['content'] = $var;
                $valArray[] = $val;
                unset($val);
            }
            $md->Vars($valArray);
            $html = $md->send();
            return true;
        } catch (Mandrill_Error $e) {
            echo 'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage();
            throw $e;
        }
        return;
    }

}
