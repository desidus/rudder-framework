<?php

namespace Desidus\Rudder;

class Microdata {

    private $jsonld_attributes = [];
    private $opengraph_attributes = [];

    public $context; // JSON-LD
    public $namespace; // OPEN_GRAPH
    
    public $title;
    public $description;
    public $lang;

    public $redirect = false;
    public $data;

    public function __construct($context = null, $namespace = null) {
        $this->context = $context;
        $this->namespace = $namespace;
    }
    
    public function setType($jsonldType, $openGraphType) 
    {
        $this->setJSONLD('@type', $jsonldType);
        $this->setOpenGraph('og:type', $openGraphType);
    }

    public function setTitle($title, $alternateTitle = null) 
    {
        $this->title = $title;
        $this->setJSONLD('name', $title);
        $this->setJSONLD('alternateName', $alternateTitle);
        $this->setOpenGraph('og:title', $title);
    }

    public function setURL($url) 
    {
        $this->setJSONLD('url', $url);
        $this->setOpenGraph('og:url', $url);
    }

    public function setDescription($description) 
    {
        $this->description = $description;
        $this->setJSONLD('description', $description);
        $this->setOpenGraph('og:description', $description);
    }

    public function setLang($jsonLDLang, $openGraphLang) 
    {
        $this->lang = $openGraphLang;
        $this->setJSONLD('inLanguage', $jsonLDLang);
        $this->setOpenGraph('og:locale', $openGraphLang);
    }

    public function setImage($image, $caption, $type, $width, $height) 
    {
        $this->setJSONLD('image', [
            '@type' => 'ImageObject',
            'representativeOfPage' => 'True',
            'contentUrl' => $image,
            'url' => $image,
            'width' => $width,
            'height' => $height,
            'encodingFormat' => $type,
            'caption' => $caption
        ], true);
        $this->setOpenGraph('og:image', [
            'og:image' => $image,
            'og:image:type' => $type,
            'og:image:width' => $width,
            'og:image:height' => $height,
            'og:image:alt' => $caption
        ], true);
    }

    public function setBreadcrumb($items) 
    {
        $this->setJSONLD('breadcrumb', [
            '@type' => 'BreadcrumbList',
            'itemListElement' => array_map(function($item, $index) {
                return [
                    '@type' => 'ListItem', 
                    'position' => $index + 1, 
                    'item' => [ '@id' => $item['url'],  'name' => $item['name'] ]
                ];
            }, $items, array_keys($items))
        ]);
    }

    public function set($names, $values) 
    {
        $this->setJSONLD($names['json-ld'], is_array($values) ? $values['json-ld'] : $values);
        $this->setOpenGraph($names['open_graph'], is_array($values) ? $values['open_graph'] : $values);
    }

    public function setJSONLD($name, $value = null, $valueIsArray = false) 
    {
        $this->_set('jsonld_attributes', $name, $value, $valueIsArray);
    }

    public function setOpenGraph($name, $value = null, $valueIsArray = false) 
    {
        $this->_set('opengraph_attributes', $name, $value, $valueIsArray);
    }

    private function _set($type, $name, $value, $valueIsArray)
    {
        if (is_array($name)) {
            foreach ($name as $key => $val) {
                $this->_apply($type, $key, $val, $valueIsArray);
            }
        } else {
            $this->_apply($type, $name, $value, $valueIsArray);
        }
    }

    private function _apply($type, $name, $value, $valueIsArray) 
    {
        if(!$value) return;

        $attrs = &$this->$type;

        if ($valueIsArray && array_key_exists($name, $attrs)) {
            if (is_array($attrs[$name])) {
                array_push($attrs[$name], $value);
            } else {
                $attrs[$name] = [$attrs[$name], $value];
            }
        } else {
            $attrs[$name] = $value;
        }
    }

    public function unsetOpenGraph($keys)
    {
        $this->_unset('opengraph_attributes', $keys);
    }

    public function unsetJSONLD($keys)
    {
        $this->_unset('jsonld_attributes', $keys);
    }

    public function _unset($type, $keys)
    {
        $attrs = &$this->$type;
        $keys = is_array($keys) ? $keys : [$keys];

        foreach ($keys as $key) {
            if (array_key_exists($key, $attrs)) {
                unset($attrs[$key]);
            }
        }
    }

    public function getJSONLD($withoutContext = false) {
        if(!$withoutContext) {
            $json_ld = $this->jsonld_attributes;
            $json_ld['@context'] = $this->context;
            return $json_ld;
        } 
        return $this->jsonld_attributes;
    }
    public function getJSONLDtoJSON() { 
        return json_encode($this->getJSONLD(),  JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES /*| JSON_PRETTY_PRINT*/);
    }

    public function getOpenGraph() {
        return $this->opengraph_attributes;
    }

}