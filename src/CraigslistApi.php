<?php

namespace Craigslist;

use \Craigslist\CraigslistRequest;
use Sunra\PhpSimple\HtmlDomParser;

class CraigslistApi
{
    public $remove_duplicates;
    protected $ids = [];
    protected $titles = [];

    /**
     * Does Craigslist RSS search request
     * Can handle array of Request and remove duplicate responses
     * @param mixed array or \Aesmith\Craigslist\Request $request
     * @param boolean $remove_duplicates
     * @throws \Exception
     * @return \Aesmith\Craigslist\Response
     */
    public function get( $request, $remove_duplicates = true )
    {
        if ($request instanceof CraigslistRequest) {
            return $this->getRSS($request);
        } else if (is_array($request)) {
            $results = [];
            foreach ($request as $req) {
                $results = array_merge($results, $this->getRSS($req));
            }
            return $results;
        } else {
            throw new \Exception('Request failed, valid data not given.');
        }
    }

    // @todo - Break this out into much more manageable pieces
    private function getRSS( CraigslistRequest $request )
    {
        $body = file_get_contents($request->url());
        $listings = simplexml_load_string(utf8_encode($body));
            
        $results = [];
        foreach ($listings as $item) {	    
            $id = substr($item->link, -15, -5);

            if (!is_numeric($id)) continue;

            if ($this->remove_duplicates) {
                if (in_array($id, $this->ids) || 
                    in_array((string) $item->title, $this->titles)) {
                    continue;
                }
                $this->ids[] = $id;
                $this->titles[] = (string) $item->title;
            }

            $results[$id] = [
                'id' => $id,
                'link' => (string) $item->link,
                'title' => (string) $item->title,
                'description' => (string) $item->description
            ];

            if ($request->follow()) {
                $results[$id]['content'] = [];
                $dom = HtmlDomParser::file_get_html($item->link);

                @$results[$id]['date'] = $dom->find('time', 0)->datetime;
                @$results[$id]['page_title'] = $dom->find('.postingtitletext', 0)->innertext;
                @$results[$id]['location'] = str_replace(['(',')'], '', $dom->find('.postingtitletext small', 0)->innertext);
                @$results[$id]['price'] = $dom->find('.price', 0)->innertext;
                @$results[$id]['body'] = $dom->find('.postingbody, #postingbody, #postingBody', 0)->innertext;
                foreach ($dom->find('.attrgroup span') as $attr) {
                    $results[$id]['attributes'][] = $attr->innertext;
                }

                foreach ($request->selectors as $selector) {
                    $target = $selector['target'];
                    foreach ($dom->find($selector['element']) as $k=>$attr) {
                        if (isset($selector['limit']) && $k > $selector['limit']-1) continue;
                        $results[$id][$selector['label']][] = $attr->$target;
                    }
                }
            }
        }
        return $results;
    }

}
