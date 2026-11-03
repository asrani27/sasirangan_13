@if(request()->is('admin*') || request()->is('home*') || request()->is('input*') || request()->is('data*') || request()->is('informasi*') || request()->is('report*'))
    @extends('layouts.admin')
@else
    @extends('layouts.public')
@endif
