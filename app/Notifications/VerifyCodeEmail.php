<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * 邮件验证
 *
 * @author
 */
class VerifyCodeEmail extends Notification implements ShouldQueue
{
    use Queueable;

    /** @var int 队列重试次数，避免单次 SMTP 抖动丢失验证码 */
    public int $tries = 3;

    /** @var array<int, int> 失败退避秒数 */
    public array $backoff = [10, 30, 60];

    /** @var int 任务超时秒数 */
    public int $timeout = 30;

    protected string $code;

    protected int $minutes;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $code, int $minutes = 30)
    {
        $this->code = $code;
        $this->minutes = $minutes;
        $this->afterCommit = true;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [
            'mail',
        ];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        // 主题不放验证码，避免邮件网关日志留存敏感码。
        return (new MailMessage)
            ->subject('您的邮箱验证码')
            ->line('您好，您的验证码是:')
            ->line($this->code)
            ->line('本验证码在'.$this->minutes.'分钟内有效.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [];
    }
}
