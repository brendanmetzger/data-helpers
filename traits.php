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
  
  protected function load(string $filepath, array $opts = ['validateOnParse' => true]) {
    $this->filepath = $filepath;
    $this->document = new DOMDocument();
    
    $config = ['formatOutput' => true, 'preserveWhiteSpace'=> false] + $opts;

    foreach ($config as $prop => $flag) $this->document->{$prop} = $flag;

    $this->document->load($this->filepath);
        
    $this->xpath  = new DOMXpath($this->document);
  }
  
  public function save() {
    $this->document->save($this->filepath);
  }

}