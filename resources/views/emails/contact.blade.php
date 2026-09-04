<x-mail::message>
# Nová zpráva z kontaktního formuláře

**Jméno:** {{ $formData['name'] }}  
**E-mail:** {{ $formData['email'] }}  
**Předmět:** {{ $formData['subject'] }}

## Zpráva
{{ $formData['message'] }}

{{ config('app.name') }}
</x-mail::message>
