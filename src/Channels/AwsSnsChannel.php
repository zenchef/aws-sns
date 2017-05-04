<?php
namespace Lab123\AwsSns\Channels;

use Lab123\AwsSns\Exceptions\CouldNotSendNotification;
use Illuminate\Notifications\Notification;
use Aws\Sns\SnsClient;

class AwsSnsChannel
{

    public function __construct(SnsClient $client)
    {
        $this->client = $client;
    }

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     *
     * @throws \Lab123\AwsSns\Exceptions\CouldNotSendNotification
     */
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toAwsSns($notifiable);
        $targets = [];
        $topics = [];

        $data = [
            'MessageStructure' => $message->messageStructure ?: 'string',
            'Message' => $message->message
        ];

        if ($message->topicArn || $notifiable->routeNotificationFor('AwsSnsTopic')) {
            $topics = ($message->topicArn) ?: $notifiable->routeNotificationFor('AwsSnsTopic');
            $topics = is_array($topics) ? $topics : [$topics];
        }

        if ($message->targetArn || $notifiable->routeNotificationFor('AwsSnsTarget')) {
            $targets = ($message->targetArn) ?: $notifiable->routeNotificationFor('AwsSnsTarget');
            $targets = is_array($targets) ? $targets : [$targets];
        }

        if ((!sizeof($targets) && !sizeof($topics)) || !$message->message) {
            return;
        }

        foreach ($targets as $target) {
            $this->call(array_merge($data, ['TargetArn' => $target]));
        }

        foreach ($topics as $topic) {
            $this->call(array_merge($data, ['TopicArn' => $topic]));
        }
    }

    /**
     * @param $data
     *
     * @throws CouldNotSendNotification
     */
    private function call($data)
    {
        $response = $this->client->publish($data);

        $response = $response->toArray();

        if ($response["@metadata"]["statusCode"] != 200) {
            throw CouldNotSendNotification::serviceRespondedWithAnError();
        }
    }
}