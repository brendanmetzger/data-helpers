<?php


# classes using XML file as a read/write data resource can use XMLresource to do all the querying and bookeeping

trait XMLresource {
  
  protected $document,
            $xpath,
            $path;

  public function find($exp, ?DOMNode $context = null) {
    if ($result = $this->xpath->query($exp, $context)) {
      return $result;
    } else {
      echo "Problem with predictate: {$exp}";
    }
  }
  
  public function select($exp, ?DOMNode $context = null) {
    return $this->find($exp, $context)[0] ?? null; 
  }
  
  protected function load(string $fullpath, array $opts = ['validateOnParse' => true]) {
    $this->path = (object)(pathinfo($fullpath) + ['full' => $fullpath]);
    $this->document = new DOMDocument();
    
    $config = ['formatOutput' => true, 'preserveWhiteSpace'=> false] + $opts;

    foreach ($config as $prop => $flag) $this->document->{$prop} = $flag;
    
    $this->document->load($this->path->full);
        
    $this->xpath  = new DOMXpath($this->document);
  }
  
  public function save($compress = false) {
    if ($compress || $this->path->extension === 'gz') {
      file_put_contents("{$this->path->full}.gz", gzcompress($this->document->saveXML()));
    }
    else $this->document->save($this->path->full);
  }

}