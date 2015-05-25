<?php

/**
 * Creates an html element, like in js.
 * @link http://davidwalsh.name/create-html-elements-php-htmlelement-class
 */
class PW_HTML_Element {
    private $type;
    private $unaryTagArray = array('input', 'img', 'hr', 'br', 'meta', 'link');
    private $attributeArray;
    private $innerHtml;

    /**
     * Constructor
     *
     * @param <type> $type
     * @param <type> $attributeArray
     * @param <type> $unaryTagArray
     */
    public function __construct($type, $attributeArray = array()) {
        $this->type = strtolower($type);

        foreach($attributeArray as $attribute => $value) {
            $this->setAttribute($attribute, $value);
        }

        return $this;
    }

    /**
     * Get one of the element's attributes
     *
     * @param <type> $attribute
     * @return <type>
     */
    public function getAttribute($attribute) {
        return $this->attributeArray[$attribute];
    }

    /**
     * Set an array, can pass an array or a key, value combination
     *
     * @param <type> $attribute
     * @param <type> $value
     */
    function setAttribute($attribute, $value = "") {
        if(!is_array($attribute)) {
            $this->attributeArray[$attribute] = $value;
        }
        else {
            $this->attributeArray = array_merge($this->attributeArray, $attribute);
        }

        return $this;
    }

    /**
     * Remove an attribute from an element
     *
     * @param <type> $attribute
     */
    function removeAttribute($attribute) {
        if(isset($this->attributeArray[$attribute])) {
            unset($this->attributeArray[$attribute]);
        }

        return $this;
    }

    /**
     * Clear all of the element's attributes
     */
    function clearAttributes() {
        $this->attributeArray = array();

        return $this;
    }

    /**
     * Insert an element into the current element
     *
     * @param <type> $object
     */
    function insert($object) {
        if(@get_class($object) == __class__) {
            $this->innerHtml .= $object->build();
        }

        return $this;
    }

    /**
     * Set the innerHtml of an element
     *
     * @param <type> $object
     * @return <type>
     */
    function update($object) {
        $this->innerHtml = $object;

        return $this;
    }

    /**
     * Builds the element
     *
     * @return <type>
     */
    function build() {
        // Start the tag
        $element = "<".$this->type;

        // Add attributes
        if(count($this->attributeArray)) {
            foreach($this->attributeArray as $key => $value) {
                $element .= " ".$key."=\"".$value."\"";
            }
        }

        // Close the element
        if(!in_array($this->type, $this->unaryTagArray)) {
            $element.= ">\n".$this->innerHtml."\n</".$this->type.">\n";
        }
        else {
            $element.= " />\n";
        }

        return $element;
    }

    /**
     * Echoes out the element
     *
     * @return <type>
     */
    function __toString() {
        return $this->build();
    }
}

?>