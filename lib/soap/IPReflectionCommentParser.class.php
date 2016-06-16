<?php

/**
 * Class for parsing the comment blocks for classes, functions
 * methods and properties.
 *
 * The class parses the commentblock and extracts certain
 * documentation tags and the (full/small) description
 *
 * @author David Kingma
 *
 * @version 0.1
 */

class IPReflectionCommentParser
{
    /**
     * @var string Contains the full comment text
     */
    public $comment;

    /**
     * @var object reference to the IPReflection(Class|Method|Property)
     */
    public $obj;

    /** @var bool */
    public $smallDescriptionDone = false;

    /** @var bool */
    public $fullDescriptionDone = false;

    /**
     * Constructor, initiateds the parse function.
     *
     * @param string Comment block
     * @param object Reference to the IPReflection(Class|Method|Property)
     */
    public function __construct($comment, $obj)
    {
        $this->comment = $comment;
        $this->obj = $obj;
        $this->parse();
    }
    /**
     * parses the comment, line for line.
     *
     * Will take the type of comment (class, property or function) as an
     * argument and split it up in lines.
     */
    protected function parse()
    {
        //reset object
        $descriptionDone = false;
        $this->fullDescriptionDone = false;

        //split lines
        $lines = explode("\n", $this->comment);

        //check lines for description or tags
        foreach ($lines as $line) {
            $pos = strpos($line, '* @');
            if (trim($line) == '/**' || trim($line) == '*/') { //skip the start and end line
            } elseif (!($pos === false)) { //comment
                $this->parseTagLine(substr($line, $pos + 3));
                $descriptionDone = true;
            } elseif (!$descriptionDone) {
                $this->parseDescription($line);
            }
        }
        //if full description is empty, put small description in full description
        if (trim(str_replace(array("\n", "\r"), array('', ''), $this->obj->fullDescription)) == '') {
            $this->obj->fullDescription = $this->obj->smallDescription;
        }
    }

    /**
     * Parses the description to the small and full description properties.
     *
     * @param string The description line
     */
    protected function parseDescription($descriptionLine)
    {
        if (strpos($descriptionLine, '*') <= 2) {
            $descriptionLine = substr($descriptionLine, (strpos($descriptionLine, '*') + 1));
        }

        //geen lege comment regel indien al in grote omschrijving
        if (trim(str_replace(array("\n", "\r"), array('', ''), $descriptionLine)) == '') {
            if ($this->obj->fullDescription == '') {
                $descriptionLine = '';
            }
            $this->smallDescriptionDone = true;
        }

        if (!$this->smallDescriptionDone) {
            //add to small description
            $this->obj->smallDescription .= $descriptionLine;
        } else {
            //add to full description
            $this->obj->fullDescription .= $descriptionLine;
        }
    }

    /**
     * Parses a tag line and extracts the tagname and values.
     *
     * @param string The tagline
     */
    protected function parseTagLine($tagLine)
    {
        $tagArr = explode(' ', $tagLine);
        $tag = array_shift($tagArr);

        switch (strtolower($tag)) {
            case 'abstract':
                $this->obj->abstract = true; break;
            case 'access':
                if (isset($tagArr[0])) {
                    $this->obj->isPrivate = (strtolower(trim($tagArr[0])) == 'private') ? true : false;
                }
                break;
            case 'author':
                $this->obj->author = implode(' ', $tagArr);
                break;
            case 'copyright':
                $this->obj->copyright = implode(' ', $tagArr);
                break;
            case 'deprecated':
            case 'deprec':
                $this->obj->deprecated = true;
                break;
            case 'extends': break;
            case 'global':
                if (isset($tagArr[0])) {
                    $this->obj->globals[] = $tagArr[0];
                }
                break;
            case 'param':
                $o = new stdClass();
                $o->type = trim(array_shift($tagArr));
                if ($o->type == 'object') {
                    $o->type = 'stdClass';
                }
                $o->comment = implode(' ', $tagArr);
                $this->obj->params[] = $o;
                break;
            case 'externalparam':
                // external parameter, to declare soap parameter that are not method
                // parameters
                $o = new stdClass();
                if (count($tagArr) < 2) {
                    throw new Exception('Not enough parameters on @externalparam '.$tagLine);
                }
                $o->type = trim(array_shift($tagArr));
                if ($o->type == 'object') {
                    $o->type = 'stdClass';
                }
                $o->name = ltrim(array_shift($tagArr), '$');
                $o->comment = implode(' ', $tagArr);
                $this->obj->externalParams[] = $o;
                break;
            case 'return':
                if (isset($tagArr[0])) {
                    $this->obj->return = trim($tagArr[0]);
                    if ($this->obj->return == 'object') {
                        $this->obj->return = 'stdClass';
                    }
                }
                break;
            case 'link':break;
            case 'see':break;
            case 'since':
                if (isset($tagArr[0])) {
                    $this->obj->since = trim($tagArr[0]);
                }
                break;
            case 'static':
                $this->obj->static = true; break;
            case 'throws':
                $this->obj->throws = implode(' ', $tagArr);
                break;
            case 'todo':
                $this->obj->todo[] = implode(' ', $tagArr);
                break;
            case 'var':
                $this->obj->type = trim(array_shift($tagArr));
                $comment = implode(' ', $tagArr);
                //check if its an optional property
                $this->obj->optional = strpos($comment, '[OPTIONAL]') !== false;
                $this->obj->autoincrement = strpos($comment, '[AUTOINCREMENT]') !== false;
                $this->obj->description = trim(str_replace(array('[OPTIONAL]', '[AUTOINCREMENT]'), '', $comment));
                break;
            case 'version':
                if (isset($tagArr[0])) {
                    $this->obj->version = $tagArr[0];
                }
                break;
            default:
                //do nothing
        }
    }
}
