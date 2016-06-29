<?php

namespace SolutionMvc\Controller;


use SolutionMvc\Core\Controller,
    SolutionMvc\Model\Newsfeed;

class NewsfeedController Extends Controller {
    
    
    
    public function getNewsAction(){
        $news = new Newsfeed();
        $feeds = array(
            "BBC" => array(
                "name" => "BBC",
                "feed" => "http://feeds.bbci.co.uk/news/rss.xml?edition=uk",
                "bg" => "#BB1919",
            ),
            "HSE" => array(
                "name" => "HSE",
                "feed" => "http://news.hse.gov.uk/feed/",
                "bg" => "#A70632",
            )
        );
        
        return print json_encode($news->getNewsFeed($feeds));
        
    }
    
    
}