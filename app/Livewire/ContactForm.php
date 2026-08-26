<?php

namespace App\Livewire;

use App\Mail\ContactFormMail;
use App\Models\Setting;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ContactForm extends Component
{
    #[Validate('required|min:3')]
    public string $name = '';

    #[Validate('required|email')]
    public string $email = '';

    #[Validate('nullable')]
    public string $phone = '';

    #[Validate('required|min:20')]
    public string $message = '';

    public function submitForm()
    {
        $validatedData = $this->validate();

        try {
            $recipient = Setting::get('site_email', config('mail.from.address'));

            $mail = new ContactFormMail($validatedData);

            $mailer = Mail::to($recipient);

            $mailer->send($mail);

            $this->reset(['name', 'email', 'phone', 'message']);

            session()->flash('message', 'Děkujeme za Vaši zprávu. Brzy se Vám ozveme.');

        } catch (\Exception $e) {
            session()->flash('error', 'Nastal problém při odesílání. Zkuste to prosím později.');
        }
    }

    public function render()
    {
        return view('livewire.contact-form');
    }
}
