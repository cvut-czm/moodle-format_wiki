<?php
namespace Generator\MoodleHtml;


class Note implements \WikiRenderer\Generator\BlockOfRawLinesInterface {
    protected $htmlTagName = 'div';

    protected $lines = array();

    protected $id = '';

    public $class='';

    public function __construct(\WikiRenderer\Generator\Config $config) {
    }

    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @param string $content
     */
    public function addLine($content) {
        $this->lines[] = $content;
    }

    public function isEmpty() {
        return count($this->lines) == 0;
    }

    public function generate() {
        $class=trim(htmlspecialchars($this->class));
        switch ($class)
        {
            default: $class='primary'; break;
            case 'important': $class='warning'; break;
            case 'warning': $class='danger'; break;
            case 'tip': $class='success'; break;
        }
        if ($this->id) {
            $text = '<' . $this->htmlTagName . ' class="mr-5 ml-5 alert alert-'.$class.'" id="' . htmlspecialchars($this->id) . '">';
        } else {
            $text = '<' . $this->htmlTagName . ' class="mr-5 ml-5 alert alert-'.$class.'" >';
        }

        foreach ($this->lines as $k => $generator) {
            if ($k > 0) {
                $text .= "\n";
            }
            $text .= $generator->generate();
        }
        $text .= '</' . $this->htmlTagName . '>';

        return $text;
    }
}
