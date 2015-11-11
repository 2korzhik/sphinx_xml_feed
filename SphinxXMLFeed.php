<?php
/**
 * Created by PhpStorm.
 * User: kozhokar
 * Date: 11.11.15
 * Time: 12:27
 */

namespace korzh;

/**
 *  SphinxXMLFeed - efficiently generate XML for Sphinx's xmlpipe2 data adapter
 *  (c) 2009 Jetpack LLC http://jetpackweb.com
 */
class SphinxXMLFeed extends \XMLWriter
{
    private $fields = array();
    private $attributes = array();

    public function __construct($options = array())
    {
        $defaults = array(
            'indent' => false,
        );
        $options  = array_merge($defaults, $options);

        // Store the xml tree in memory
        $this->openMemory();

        if ($options['indent']) {
            $this->setIndent(true);
        }
    }

    public function setFields($fields)
    {
        $this->fields = $fields;
    }

    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

    public function addDocument($doc)
    {
        $this->startElement('sphinx:document');
        $this->writeAttribute('id', $doc['id']);

        foreach ($doc as $key => $value) {
            // Skip the id key since that is an element attribute
            if ($key == 'id') {
                continue;
            }

            $this->startElement($key);
            $this->text($value);
            $this->endElement();
        }

        $this->endElement();
        print $this->outputMemory();
    }

    public function beginOutput()
    {

        $this->startDocument('1.0', 'UTF-8');
        $this->startElement('sphinx:docset');
        $this->writeAttributeNS ("xmlns", "sphinx", null, "http://sphinxsearch.com/");
        $this->startElement('sphinx:schema');

        // add fields to the schema
        foreach ($this->fields as $field) {
            $this->startElement('sphinx:field');
            $this->writeAttribute('name', $field);
            $this->endElement();
        }

        // add attributes to the schema
        foreach ($this->attributes as $attributes) {
            $this->startElement('sphinx:attr');
            foreach ($attributes as $key => $value) {
                $this->writeAttribute($key, $value);
            }
            $this->endElement();
        }

        // end sphinx:schema
        $this->endElement();
        print $this->outputMemory();
    }

    public function endOutput()
    {
        // end sphinx:docset
        $this->endElement();
        print $this->outputMemory();
    }
}