<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Stancl\Tenancy\Database\Models\Tenant;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
        public Tenant $tenant,
        public string $password,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '¡Bienvenido a Arioli.dev! Tu acceso está listo',
        );
    }

    public function content(): Content
    {
        $publicDomain = $this->order->plan->product->public_domain;

        return new Content(
            markdown: 'emails.welcome',
            with: [
                'order'    => $this->order,
                'tenant'   => $this->tenant,
                'password' => $this->password,
                'loginUrl'  => 'https://' . config('app.cliente_domain') . '/login',
                'systemUrl' => 'https://' . $this->tenant->id . '.' . $publicDomain . '.' . config('app.tenant_domain'),
            ],
        );
    }
}