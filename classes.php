<?php

/**
 * Serialize ints to alpha and vice versa
 * Rationale: xml ids cannot start with numbers, but there are 500+ valid characters; makes for
 *            shorter (visually) serialized IDs. Works with same default sort as numerical digits
 * reference: https://www.w3.org/TR/REC-xml/#NT-Name
 * ":" | [A-Z] | "_" | [a-z] | [#xC0-#xD6] | [#xD8-#xF6] | [#xF8-#x2FF] | [#x370-#x37D] | [#x37F-#x1FFF] | [#x200C-#x200D] | [#x2070-#x218F] | [#x2C00-#x2FEF] | [#x3001-#xD7FF] | [#xF900-#xFDCF] | [#xFDF0-#xFFFD] | [#x10000-#xEFFFF]
 */

class Serial {
  const UTF = [
    'A-Z' => ["\u{041}", "\u{05a}"],
    'a-z' => ["\u{061}",  "\u{07a}"],
    [0xC0,  0xD6], // À-Ö
    [0xD8,  0xF6], // Ø-ö
    [0xF8,  0x2FF],// ø-˿
    [0x1401,0x1676],  // Unified Canadian Aboriginal Syllabics
    'runes'   => [0x16A0, 0x16F0], 
    'braille' => ["\u{02800}","\u{028FF}"], // (interesting for encoding, but out of range for @id)

  ];
  protected $codex, $base;
  private function __construct($input) {
    $this->codex = array_merge(range(...self::UTF['A-Z']), range(...self::UTF['a-z']));
    // $this->codex = $this->mb_range(...self::UTF['braille']);
    $this->base  = count($this->codex);
  }
  
  static public function id ($input) {
    $method = 'from'.gettype($input);
    $instance = new self($input);
    return $instance->{$method}($input);
  }
  
  protected function fromInteger(int $in, string $out = '') { 
    do  {
      $d   = floor($in / $this->base);
      $r   = $in % $this->base;
      $in  = $d;
      $out = $this->codex[$r] . $out;
    } while ($in > 0);

    return $out;
  }
  
  protected function fromString(string $in, int $out = 0) {
    $codex = array_flip($this->codex);
    foreach (array_reverse(preg_split('//u', $in, null, PREG_SPLIT_NO_EMPTY)) as $exp => $val)
      $out += ($this->base ** $exp) * $codex[$val];
    return $out;
  }
  
  static public function mb_range($start, $end, array $output = []) {

    if ($start == $end) return [$start];  // no range given

    // get unicodes of start and end
    list(, $_current, $_end) = unpack("N*", mb_convert_encoding($start . $end, "UTF-32BE", "UTF-8"));

    $cursor = $_end <=> $_current; // determine ascending or decending
  
    do {
      $output[]  = mb_convert_encoding(pack("N*", $_current), "UTF-8", "UTF-32BE");
      $_current += $cursor;
    } while ($_current != $_end);
    $output[] = $end;
    return $output;
  }
  
}

# for times when you are staring at a black streen with blinking cursor..

class Benchmark {
  public $tasks   = 0;
  private $mark   = [];
  private $splits = [];
  
  public function __construct($key = 'start') {
    $this->split($key);
  }
  
  public function split($key, $split = false) {
    $this->mark[$key] = microtime(true);
    if ($split) $this->split[$key] = $this->mark[$key] - $this->mark[$split];
    return $split ? $this->split[$key] : $this->mark[$key];
  }
  
  // use task count to draw a progress bar, useful for slower processes with lots of stuff to do
  public function progress($index, $msg = '', $total = 50):string  {
    $prog = $index / $this->tasks;
    $crlf = ($prog === 1) ? "\n" : "\r";
    return sprintf("% 5.1f%% [%-{$total}s] %s %s", $prog * 100, str_repeat('#', round($total * $prog)), $msg, $crlf);
  }
  
}