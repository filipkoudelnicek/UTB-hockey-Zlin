@include('errors.layout', [
    'code' => 404,
    'title' => 'Tady se nehraje',
    'message' => 'Stránku, kterou hledáte, se nám nepodařilo najít. Zkontrolujte adresu nebo se vraťte na hlavní stránku.',
    'showBack' => true,
])
