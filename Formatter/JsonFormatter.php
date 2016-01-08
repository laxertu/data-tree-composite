<?php
namespace MessageComposite\Formatter;
use MessageComposite\MessageInterface;

/**
 * Class JsonFormatter
 * @package MessageComposite\Formatter
 * @see MessageComposite\tests\formatters\JsonFormatterTest
 */
class JsonFormatter extends  AbstractFormatter
{

    public function buildContent(MessageInterface $message)
    {

        $content = '"'.$message->getName().'":'.$this->buildBody($message);

        # entire message is surrounded by {}
        if(!$message->getParent()) {
            $content = '{'.$content.'}';
        }
        return $content;
    }

    private function buildBody(MessageInterface $message)
    {

        # a simple value
        if($message->isLeaf()) {

            $content = $this->buildLeafMessageBody($message->getBody());

        } else {
            $content = [];
            foreach($message->getChildren() as $child) {
                $content[]= $this->buildContent($child);
            }
            #composites' children are surrounded by {}
            $content = '{'.implode(',', $content).'}';

        }

        return $content;
    }

    /**
     * We want to allow clients declaring a message with a valid Json string as content.
     *
     * @param $body
     * @return string
     */
    private function buildLeafMessageBody($body)
    {
        #numbers and valid json strings comes without enclosure
        if((@json_decode($body) === null) && !is_numeric($body)){
            $body = '"'.$body.'"';
        }

        return $body;
    }
} 