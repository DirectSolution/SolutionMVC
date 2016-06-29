<?php

namespace SolutionMvc\Controller;

use SolutionMvc\Library\Helper,
    SolutionMvc\Core\Security,
    SolutionMvc\Audit\Model\Upload;

class UploadController {

    var $helper;
    protected $security;
    protected $upload;

    public function __construct() {
        $this->helper = new Helper();
        $this->security = new Security();
        $this->upload = new Upload();
    }

    public function UploadAuditImageAction() {
        $this->token = $this->security->DecodeSecurityToken($_POST['token']);
        return $this->upload->image(
//                        "/var/www/html/Filestore/", "images/" . $this->token->user->client . "/Audits/" . $_POST['audit_id'] . "/Assets/" . $_POST['asset_id'], $_FILES
                        "/home/git/htmlp/html/doug/portal.solutionhost.co.uk/web/apps/Audit/Filestore/", "images/" . $this->token->user->client . "/Audits/" . $_POST['audit_id'] . "/Assets/" . $_POST['asset_id'], $_FILES
        );
    }

    public function UploadAssetImageAction() {
        $this->token = $this->security->DecodeSecurityToken($_POST['token']);
        return $this->upload->image(
                        "/home/git/htmlp/html/doug/portal.solutionhost.co.uk/web/apps/Audit/Filestore/", "images/" . $this->token->user->client . "/Assets", $_FILES
        );
    }

}
