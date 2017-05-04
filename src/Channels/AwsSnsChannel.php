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

        $data = [
            'MessageStructure' => $message->messageStructure ?: 'string',
            'Message' => $message->message
        ];

        if($message->topicArn || $notifiable->routeNotificationFor('AwsSnsTopic')) {
            $data['TopicArn'] = ($message->topicArn) ?: $notifiable->routeNotificationFor('AwsSnsTopic');
        }

        if($message->targetArn || $notifiable->routeNotificationFor('AwsSnsTarget')) {
            $data['TargetArn'] = ($message->targetArn) ?: $notifiable->routeNotificationFor('AwsSnsTarget');
        }

        if ((! $message->topicArn && ! $message->targetArn) || ! $message->message) {
            return;
        }

        $response = $this->client->publish($data);

        $response = $response->toArray();

        if ($response["@metadata"]["statusCode"] != 200) {
            throw CouldNotSendNotification::serviceRespondedWithAnError();
        }
    }
}