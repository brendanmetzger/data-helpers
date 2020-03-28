<?php


# classes using XML file as a read/write data resource can use XMLresource to do all the querying and bookeeping

trait XMLresource {
  
  protected $document,
            $xpath,
            $filepath;

  public function find($exp, ?DOMNode $context = null) {
    return $this->xpath->query($exp, $context);
  }
  public function select($exp, ?DOMNode $context = null) {
    return $this->find($exp, $context)[0] ?? null; 
  }
  
  protected function load(string $filepath) {
    $this->filepath = $filepath;
    $this->document = new DOMDocument();
    
    $this->document->formatOutput       = true;
    $this->document->preserveWhiteSpace = false;
    $this->document->validateOnParse    = true;
    $this->document->load($this->filepath);
    
    $this->xpath  = new DOMXpath($this->document);
  }
  
  public function save() {
    $this->document->save($this->filepath);
  }

}