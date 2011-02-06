<?php

class RSSCrawler
{
    private $reader;
    private $rss_xml;
    private $url = NULL;
    private $isrss = FALSE;
    private $hasrss = FALSE;
    private $isinit = FALSE;
    private $isitem = FALSE;

    private $item;
    private $item_num;
    private $item_cursor;
    
    private function fetch_to_assoc($rss)
    {
        $result = array();
        $counter = 0;
        while ($rss->read())
        {
            switch ($rss->nodeType)
            {
                case XMLReader::END_ELEMENT:return $result;
                case XMLReader::ELEMENT:
                    if ($rss->name == 'item' || $rss->name == 'category')
                        $result[$rss->name][$rss->name.'-'.$counter++] = 
                            $rss->isEmptyElement ? '' : $this->fetch_to_assoc($rss);
                    else
                        $result[$rss->name] = 
                            $rss->isEmptyElement ? '' : $this->fetch_to_assoc($rss);
                    $element_name = $rss->name;
                    if ($rss->hasAttributes)
                    {
                        while ($rss->moveToNextAttribute())
                        {
                            $element_attr[$rss->name] = $rss->value;
                        }
                        $result[$element_name]['attr'] = $element_attr;
                        unset($element_attr);
                    }
                    break;
                case XMLReader::TEXT:
                case XMLReader::CDATA:
                    $result['value'] = $rss->value;
            }
        }
        return $result;
    }
    
    private function store_xml()
    {
        $this->rss_xml = $this->fetch_to_assoc($this->reader);
    }

    private function init()
    {
        $this->store_xml();

        if (isset($this->rss_xml['rss']) && isset($this->rss_xml['rss']['channel']))
            $this->hasrss = TRUE;

        if (isset($this->rss_xml['rss']['channel']['item']))
        {
            $this->isitem = TRUE;
            $this->item = $this->rss_xml['rss']['channel']['item'];
            $this->item_num = count($this->item);
            $this->item_cursor = 0;
        }
        $this->isinit = TRUE;
    }

    public function open($url)
    {
        $this->reader = new XMLReader();
        if ($this->reader->open($url))
        {
            $this->url = $url;
            return TRUE;
        }
        return FALSE;
    }

    public function read_item()
    {
        if (!$this->isitem || $this->item_cursor >= $this->item_num)
            return NULL;

        return $this->item['item-'.$this->item_cursor++];
    }

    public function valid()
    {
        $xml = new XMLReader();
        $xml->open($this->url);
        $xml->setParserProperty(XMLReader::VALIDATE, TRUE);
        if ($xml->isValid())
        {
            $this->init();
            if ($this->hasrss)
                $this->isrss = TRUE;
        }
        $xml->close();
        return $this->isrss;
    }

    public function close()
    {
        if ($this->url)
            if($this->reader->close())
                return TRUE;
        return FALSE;
    }

    public function get_item_num()
    {
        if ($this->isitem)
            return $this->item_num;
        return 0;
    }

    public function get_rss_attributes()
    {
        if ($this->isrss)
            return $this->rss_xml['rss']['attr'];
        return NULL;
    }

    public function get_channel()
    {
        $channel = array();
        if ($this->isrss)
        {
            $channel = $this->rss_xml['rss']['channel'];
            unset($channel['item']);
        }
        return $channel;
    }

}

?>
