<?php


# classes using XML file as a read/write data resource can use XMLresource to do all the querying and bookeeping

trait XMLresource {
  
  protected $document,
            $xpath,
            $path;

  public function find($exp, ?DOMNode $context = null)
  {
    if (! $result = $this->xpath->query($exp, $context))
      throw new Exception("Problem with xpath predicate: {$exp}");
    
    return $result;
  }
  
  public function eval($exp, ?DOMNode $context = null)
  {
    return trim($this->xpath->evaluate("string({$exp})", $context));
  }
  
  public function select($exp, ?DOMNode $context = null)
  {
    return $this->find($exp, $context)[0] ?? null; 
  }
  
  protected function load(string $fullpath, array $opts = ['validateOnParse' => true])
  {
    $this->path      = (object)(pathinfo($fullpath) + ['full' => realpath($fullpath)]);
    $this->path->uri = ($this->path->extension == 'gz' ? 'compress.zlib' : 'file') . "://{$this->path->full}";

    $this->setDOM(file_get_contents($this->path->uri), $opts);
  }
  
  protected function setDOM(string $xml, array $opts = [])
  {
    $document_props = ['formatOutput' => true, 'preserveWhiteSpace'=> false] + $opts;
    $this->document = new DOMDocument('1.0', 'UTF-8');
    
    foreach ($document_props as $prop => $flag) $this->document->{$prop} = $flag;
    
    $this->document->loadXML($xml);    
    $this->xpath  = new DOMXpath($this->document);
  }
  
  public function save($compress = false) {
    if ($compress || $this->path->extension === 'gz') {
      file_put_contents("compress.zlib://{$this->path->full}.gz", $this->document->saveXML());
    }
    else $this->document->save($this->path->full);
  }

}