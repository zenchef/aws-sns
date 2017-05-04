<?php
namespace Lab123\AwsSns\Messages;

class AwsSnsMessage
{

    const STRUCTURE_JSON = 'json';
    const STRUCTURE_STRING = 'string';

    /**
     * @var string
     */
    public $topicArn = "";

    /**
     * @var string
     */
    public $targetArn = "";

    /**
     * @var string
     */
    public $type = "";

    /**
     * @var string
     */
    public $phoneNumber = "";

    /**
     * @var string
     */
    public $endpoint = "";

    /**
     * @var string
     */
    public $message = "";

    /**
     * @var string
     */
    public $subject = "";

    /**
     * @var string
     */
    public $messageStructure = "string";

    /**
     * Create a new message instance.
     *
     * @param string $message
     */
    public function __construct($message = '')
    {
        $this->message = $message;
    }

    /**
     * Set the topicArn.
     *
     * @param string|array $topicArn
     * @return $this
     */
    public function topicArn($topicArn)
    {
        $this->topicArn = $topicArn;

        return $this;
    }

    /**
     * Set the targetArn.
     *
     * @param string $targetArn
     * @return $this
     */
    public function targetArn($targetArn)
    {
        $this->targetArn = $targetArn;

        return $this;
    }

    /**
     * Set the Phone Number.
     *
     * @param string $phoneNumber
     * @return $this
     */
    public function phoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * Set the Type.
     *
     * @param string $type
     * @return $this
     */
    public function type($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Set the endpoint.
     *
     * @param string $endpoint
     * @return $this
     */
    public function endpoint($endpoint)
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    /**
     * Set the message content.
     *
     * @param string $message
     * @return $this
     */
    public function message($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Set the subject.
     *
     * @param string $subject
     * @return $this
     */
    public function subject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Set the Message Structure.
     *
     * @param string $messageStructure
     * @return $this
     */
    public function messageStructure($messageStructure)
    {
        $this->messageStructure = in_array($messageStructure, [self::STRUCTURE_JSON, self::STRUCTURE_STRING]) ? $messageStructure : self::STRUCTURE_STRING;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        switch($this->messageStructure) {
            case self::STRUCTURE_JSON :
                return json_encode(["default" => "Zenchef notification", "APNS" => json_encode($this->message), "APNS_SANDBOX" => json_encode($this->message)]);
                break;
            default:
                return $this->message;
                break;
        }
    }
}