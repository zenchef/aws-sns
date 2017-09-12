<?php
namespace Lab123\AwsSns\Messages;

class AwsSnsMessage
{

    const STRUCTURE_JSON = 'json';
    const STRUCTURE_STRING = 'string';

    /**
     * @var string
     */
    public $default = "Zenchef notification";

    /**
     * @var string
     */
    public $topicArn = "";

    /**
     * @var array
     */
    public $metadata = [];

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
    public $endpoint = "";

    /**
     * @var string
     */
    public $category = "";

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
     *
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
     *
     * @return $this
     */
    public function targetArn($targetArn)
    {
        $this->targetArn = $targetArn;

        return $this;
    }

    /**
     * Set the Type.
     *
     * @param string $type
     *
     * @return $this
     */
    public function type($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Set the Type.
     *
     * @param string $key
     * @param mixed $val
     *
     * @return $this
     */
    public function metadata($key, $val)
    {
        $this->metadata[$key] = $val;

        return $this;
    }

    /**
     * Set the endpoint.
     *
     * @param string $category
     *
     * @return $this
     */
    public function category($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Set the endpoint.
     *
     * @param string $endpoint
     *
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
     *
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
     *
     * @return $this
     */
    public function subject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * APNS message formatted
     *
     * @return $this
     */
    private function APNSMessage()
    {
        return json_encode([
            "aps" => [
                "alert" => [
                    "body" => $this->message
                ],
                "badge" => 1,
                "category" => $this->category,
                "metadata" => $this->metadata
            ]
        ]);
    }

    /**
     * APNS message formatted
     *
     * @return $this
     */
    private function APNSSandboxMessage()
    {
        return $this->APNSMessage();
    }

    /**
     * GCM message formatted
     *
     * @return $this
     */
    private function GCMMessage()
    {
        return json_encode([
            "notification" => [
                "text" => $this->message,
                "data" => [
                    "category" => $this->category,
                    "metadata" => $this->metadata
                ]
            ]
        ]);
    }


    /**
     * Set the Message Structure.
     *
     * @param string $messageStructure
     *
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
        switch ($this->messageStructure) {
            case self::STRUCTURE_JSON :
                return json_encode([
                    "default" => $this->default,
                    "APNS" => $this->APNSMessage(),
                    "APNS_SANDBOX" => $this->APNSSandboxMessage(),
                    "GCM" => $this->GCMMessage()
                ]);
                break;
            default:
                return $this->message;
                break;
        }
    }
}