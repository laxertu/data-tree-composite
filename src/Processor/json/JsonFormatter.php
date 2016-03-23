<?php
namespace laxertu\DataTree\Processor\json;

use laxertu\DataTree\Processor\AbstractProcessor;
use laxertu\DataTree\Processor\ProcessableInterface;

/**
 * Class JsonProcessor
 * @package DataTree\Processor
 * @see DataTree\tests\formatters\JsonFormatterTest
 */
class JsonFormatter extends AbstractProcessor
{

    public function buildContent(ProcessableInterface $message)
    {

        $content = '"'.$message->getName().'":'.$this->buildBody($message);

        # entire message is surrounded by {}
        if (!$message->getParent()) {
            $content = '{'.$content.'}';
        }
        return $content;
    }

    private function buildBody(ProcessableInterface $message)
    {

        # a simple value
        if ($this->isLeaf($message)) {

            $content = $this->buildLeafMessageBody($message->getValue());

        } else {

            $content = $this->buildCompositeMessageBody($message);

        }

        return $content;
    }

    private function buildLeafMessageBody($messageValue)
    {
        if (is_array($messageValue)) {

            $body = $this->formatArrayValue($messageValue);

        } else {
            $body = $this->formatStringValue($messageValue);
        }

        return $body;
    }

    private function formatStringValue($value)
    {
        # We want to allow clients to declare a message with a valid Json string as content.
        # Numbers and valid json strings comes without enclosure.
        if ((@json_decode($value) === null) && !is_numeric($value) && !is_null($value)) {
            $value = '"'.$value.'"';
        }

        if (is_null($value)) {
            $value = 'null';
        }

        return $value;
    }

    private function formatArrayValue($messageValue)
    {

        foreach ($messageValue as $index => $value) {
            $messageValue[$index] = $this->buildLeafMessageBody($value);
        }

        $body = '['.implode(',', $messageValue).']';
        return $body;
    }



    /**
     * @param ProcessableInterface $message
     * @return array|string
     */
    private function buildCompositeMessageBody(ProcessableInterface $message)
    {
        $content = '';
        $contentArray = [];

        if ($message->isAListOfTrees()) {

            foreach ($message->getChildren() as $child) {
                $contentArray[]= '{'.$this->buildContent($child).'}';
            }

            $content = '['.implode(',', $contentArray).']';

        } else {

            foreach ($message->getChildren() as $child) {
                $contentArray[]= $this->buildContent($child);
            }

            $content = '{'.implode(',', $contentArray).'}';
        }

        return $content;
    }
}
