<?php

namespace Lockminds\LaravelAuth\Mail\Auth;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Lockminds\LaravelAuth\Models\User;

class LogedIn extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;

    public User $account;

    public string $ipAddress;

    public string $browser;

    public Carbon $time;

    public $location;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, Request $request)
    {
        $this->user = $user;
        $this->account = $user;
        $this->ipAddress = $request->ip();
        $this->browser = $request->userAgent();
        $this->time = now();
        $this->location = \geoip($request->ip());
    }

    /**
     * Get the message envelope.
     *
     * @return Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Security Alert',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return Content
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'authentication-log::emails.new',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments(): array
    {
        return [];
    }
}
