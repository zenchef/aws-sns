<?php
namespace Lab123\AwsSns\Channels;

use Lab123\AwsSns\Exceptions\CouldNotSendNotification;
use Illuminate\Notifications\Notification;
use Aws\Sns\SnsClient;
use Throwable;

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
     * @param Notification $notification
     *
     * @throws CouldNotSendNotification
     * @throws Throwable
     */
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toAwsSns($notifiable);
        $targets = [];
        $topics = [];

        if(!$message) { // no message
            return ;
        }

        $data = [
            'MessageStructure' => $message->messageStructure ?: 'string',
            'Message' => $message->getMessage()
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
     * @throws Throwable
     */
    private function call($data)
    {
        try {
            $response = $this->client->publish($data);
        } catch (Throwable $e) {
            if (isset($data['TargetArn'])) {
                $e->TargetArn = $data['TargetArn'];
            }
            throw $e;
        }

        $response = $response->toArray();

        if ($response["@metadata"]["statusCode"] != 200) {
            throw CouldNotSendNotification::serviceRespondedWithAnError();
        }
    }
}
