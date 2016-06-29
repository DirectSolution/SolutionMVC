<?php

namespace SolutionMvc\Library;

class GetClientLogo{
    
    public function readClientlogo($client) {

        $getSettings = new \SolutionMvc\Model\ConfSettings();
        $settings = $getSettings->getSettings($client);
        $filepath = LEG . "app/images/custom/$client/";
        if ($settings['noteslogo'] == '') {
            $jpgfile = $_SERVER['DOCUMENT_ROOT'] . $filepath . $client . ".jpg";
            $pngfile = $_SERVER['DOCUMENT_ROOT'] . $filepath . $client . ".png";
            $giffile = $_SERVER['DOCUMENT_ROOT'] . $filepath . $client . ".gif";
            if (file_exists($jpgfile)) {
                return "<img src=\"" . $filepath . $client . " border=\"0\" class=\"clientLogo\">\n";
            } elseif (file_exists($pngfile)) {
                return "<img src=\"" . $filepath . $client . ".png\" border=\"0\" class=\"clientLogo\">\n";
            } elseif (file_exists($giffile)) {
                return "<img src=\"" . $filepath . $client . ".gif\" border=\"0\" class=\"clientLogo\">\n";
            } else {
                return "";
            }
        } else {
            return "<img src=\"" . $filepath . $settings['noteslogo'] . "\" border=\"0\">\n";
        }
    }
    
}