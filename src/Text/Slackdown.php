<?php

namespace Text;

class Slackdown {

  public static function getFilters() {
    return array(
      'italics' => 'Emphasis',
      'bold' => 'Strong Emphasis',
      'strike' => 'Strikethrough',
      'fmt_multiline' => 'Formatted Text - Multiple Lines',
      'fmt_inline' =>'Formatted Text - Inline',
      'blockquote_multiline' => 'Quote - Multiple Lines',
      'blockquote' => 'Quote'
    );
  }

  private static function getDefaults() {
    $keys = array_keys(self::getFilters());
    $values = array_fill(0, count($keys), TRUE);
    return array_combine($keys, $values);
  }

  public function __construct(array $options = array()) {
    $this->options = array_merge(self::getDefaults(), $options);
  }

  private function getActiveFilters() {
    return array_keys(array_filter($this->options));
  }

  private function applyRegex($text, $regex, $tag, $filtersStr=null) {
    return preg_replace_callback($regex, function($matches) use($tag, $filtersStr) {
      return "<$tag>" .
        $this->processFilters($matches[1], $filtersStr) .
      "</$tag>";
    }, $text);
  }

  private function processBold($text) {
    return $this->applyRegex($text, '/\*(.*?)\*/s', 'strong');
  }

  private function processItalics($text) {
    return $this->applyRegex($text, '/\_(.*?)\_/s', 'em');
  }

  private function processStrike($text) {
    return $this->applyRegex($text, '/~(.*?)~/s', 'strike');
  }

  private function processBlockquote($text) {
    return $this->applyRegex($text, '/^>(.*)$/m', 'blockquote', 'bold|italics|strike|fmt_inline');
  }

  private function processBlockquoteMultiline($text) {
    return $this->applyRegex($text, '/^>>>(.*)$/s', 'blockquote', 'bold|italics|strike|fmt_inline|blockquote', 3);
  }

  private function processFmtInline($text) {
    return $this->applyRegex($text, '/`(.*?)`/s', 'code');
  }

  private function processFmtMultiline($text) {
    return $this->applyRegex($text, '/```(.*?)```/s', 'code');
  }

  private function processFilter($text, $filter) {
    switch($filter) {
      case 'bold':
        return $this->processBold($text);
      case 'italics':
        return $this->processItalics($text);
      case 'strike':
        return $this->processStrike($text);
      case 'fmt_multiline':
        return $this->processFmtMultiline($text);
      case 'fmt_inline':
        return $this->processFmtInline($text);
      case 'blockquote_multiline':
        return $this->processBlockquoteMultiline($text);
      case 'blockquote':
        return $this->processBlockquote($text);
      default:
        return $text;
    }
  }

  private function processFilters($text, $filtersStr) {
    if (!$filtersStr) {
      return $text;
    }
    $filters = explode('|', $filtersStr);
    $filters = array_intersect($filters, $this->getActiveFilters());
    $output = $text;
    foreach ($filters as $filter) {
      $output = $this->processFilter($output, $filter);
    }
    return $output;
  }

  public function process($text) {
    $activeFiltersStr = implode('|', $this->getActiveFilters());
    $text = str_replace('\n', PHP_EOL, $text);
    return $this->processFilters($text, $activeFiltersStr);
  }

};
