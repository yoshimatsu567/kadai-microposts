@if(Auth::user()->is_favorite($micropost->id))
    {{-- お気に入りをやめる --}}
    {!! Form::open(['route' => ['favorites.unfavorite', $micropost->id], 'method' => 'delete']) !!}
        {!! Form::submit('Unfavorite', ['class' => "btn btn-danger btn-sm"]) !!}
    {!! Form::close() !!}    
@else
    {{-- お気に入りにする --}}
    {!! Form::open(['route' => ['favorites.favorite', $micropost->id]]) !!}
        {!! Form::submit('Favorite', ['class' => "btn btn-success btn-sm"]) !!}
    {!! Form::close() !!}
@endif
   
