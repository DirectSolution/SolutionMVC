<?php

namespace SolutionMvc\Model;

class Newsfeed {

    public function getNewsFeed($feeds) {

        $article = array();

        foreach ($feeds as $feed) {
            $get_feed = new \SimpleXMLElement($feed['feed'], LIBXML_NOCDATA, true);
            $json_decode_feed = json_decode(json_encode($get_feed->xpath('channel')), TRUE);

            foreach ($json_decode_feed[0]['item'] AS $newsItem) {
                $newsItem['description'] = preg_replace("/<img[^>]+\>/i", null, $newsItem['description']);
                $newsItem['feed'] = $feed['name'];
                $newsItem['bg'] = $feed['bg'];
                $newsItem['pubDate'] = date("D d M h:i A", strtotime($newsItem['pubDate']));
                $article[] = $newsItem;
            }
        }
        return $this->shuffle_assoc($article);
    }

    public function shuffle_assoc($list) {
        if (!is_array($list)) {
            return $list;
        }
        $keys = array_keys($list);
        shuffle($keys);
        $random = array();
        foreach ($keys as $key) {
            $random[$key] = $list[$key];
        }
        return $random;
    }

}
