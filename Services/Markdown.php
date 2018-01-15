<?php namespace Modules\Base\Services;

class Markdown
{
    protected $text;
    protected $html;

    public function __construct($text = null)
    {
        $this->setText($text);
    }

    public function setText($text)
    {
        $this->text = $text;
        $this->parse();
    }

    protected function parse()
    {
        $parser = new \Parsedown();
        $this->html = $parser->text($this->text);
    }

    public function __get($name)
    {
        switch($name) {
            case 'text':
                return $this->text;
                break;
            case 'html':
                return $this->html;
                break;
        }
    }

    public function __toString()
    {
        return $this->text;
    }
}