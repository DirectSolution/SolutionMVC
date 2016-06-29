<?php

namespace SolutionMvc\Audit\Controller;

use SolutionMvc\Core\Controller,
    SolutionMvc\Audit\Model\ReportSettings,
    SolutionMvc\Audit\Model\Settings,
    SolutionMvc\Core\Security,
    SolutionMvc\Core\Response,
    Fisharebest\PhpPolyfill\Php54;

/**
 * Description of AuditController
 *
 * @author doug
 */
class SettingsController extends Controller {

    protected $report;
    protected $response;
    protected $settings;
    protected $security;

    public function __construct() {

        parent::__construct();
        $this->response = new Response();
        $this->settings = new Settings();
        $this->report = new ReportSettings();
        $this->security = new Security();
        $this->token = $this->getToken();
    }

    public function getReportSettingsAction() {


        $settings = $this->report->getReportSettingsByClient($this->token->user->client);
        $this->response->settings = array(
            "id" => $settings['id'],
            "colours" => array(
                "head" => $settings['colourHead'],
                "foot" => $settings['colourFoot'],
                "left" => $settings['colourLeft'],
                "right" => $settings['colourRight']
            ),
            "margins" => array(
                "head" => (int) $settings['heightHead'],
                "foot" => (int) $settings['heightFoot'],
                "left" => (int) $settings['widthLeft'],
                "right" => (int) $settings['widthRight'],
            ),
            "hideImages" => $settings['hide_images']
        );
        $this->response->headers = http_response_code(200);
        return print(json_encode($this->response));
    }

    public function getSettings($client) {

        print $client;
        $settings = $this->settings->getSettingsByClient($client);

        return array(
            "defaultAudit" => $settings['Audits_id']
        );
    }

    public function setDefaultAction() {
                
        if ($this->security->getToken()) {
            $rq = $this->requestObject();
            $this->settings->setDefault($rq['id'], $this->token->user->client);
            if ($this->requestType() == "ajax") {
                $this->response->setHeaders(200);
                $this->response->setStatus("success");
                $this->response->setMessage("Default audit successfully changed");
                return print json_encode($this->response);
            } else {
                $this->setSession("success", "Default audit successfully changed.");
                $this->response->setHeaders(header('Location: http://doug.portal.solutionhost.co.uk/apps2/public/Audit/Audit/'));
            }
        } else {
            return "fail";
        }
    }

}
