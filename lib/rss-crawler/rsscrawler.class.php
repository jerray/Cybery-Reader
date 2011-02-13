<?php

class RSSCrawler
{
    private $reader = NULL;
    private $rss_xml = NULL;
    private $url = NULL;
    private $isrss = FALSE;

    private $item_num = 0;
    private $item_cursor = 0;
    
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

    private function init()
    {
        $this->rss_xml = $this->fetch_to_assoc($this->reader);

        if (isset($this->rss_xml['rss']) && isset($this->rss_xml['rss']['channel']))
            $this->isrss = TRUE;

        if (isset($this->rss_xml['rss']['channel']['item']))
        {
            $this->item_num = count($this->rss_xml['rss']['channel']['item']);
            $this->item_cursor = 0;
        }
    }

    public function open($url)
    {
        if ($this->url)
            $this->close();

        $this->reader = $this->reader == NULL ? new XMLReader() : $this->reader;
        if ($this->reader->open($url))
        {
            $this->url = $url;
            return TRUE;
        }
        return FALSE;
    }

    public function read_item()
    {
        if (!$this->item_num || $this->item_cursor >= $this->item_num)
            return NULL;

        return $this->rss_xml['rss']['channel']['item']['item-'.$this->item_cursor++];
    }

    public function valid()
    {
        if ($this->isrss)
            return TRUE;
            
        $xml = new XMLReader();
        $xml->open($this->url);
        $xml->setParserProperty(XMLReader::VALIDATE, TRUE);
        if ($xml->isValid())
        {
            $this->init();
        }
        $xml->close();
        return $this->isrss;
    }

    public function close()
    {
        if ($this->url)
        {
            if($this->reader->close())
            {
                $this->url = NULL;
                $this->rss_xml = NULL;
                $this->isrss = FALSE;
                $this->item_num = 0;
                $this->item_cursor = 0;
                return TRUE;
            }
        }
        return FALSE;
    }

    public function get_item_num()
    {
        return $this->item_num;
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
    
    public function reset_read_cursor()
    {
        $this->item_cursor = 0;
    }
    
    public function set_read_cursor($num)
    {
        if (!is_int($num) || $num > $this->item_num || $num <= 0)
            return FALSE;
        $this->item_cursor = $num - 1;
        return TRUE;
    }

}

?>
