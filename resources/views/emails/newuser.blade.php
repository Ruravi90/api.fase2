@component('mail::message')
	Bienvenido {{ $user->name }}!
	<br>
	Usuario: {{ $user->username }}
	<br>
	Clave: {{ $user->password }}

	@component('mail::button', ['url' => config('app.url')])
		Inicial sesión
	@endcomponent
@endcomponent
