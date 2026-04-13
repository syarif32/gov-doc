@extends('layouts.admin')

@section('content')
    <div class="card border-0 shadow-sm overflow-hidden animate__animated animate__fadeIn"
        style="height: calc(100vh - 120px);">
        <div class="row g-0 h-100">

            <!-- SIDEBAR: User List -->
            <div class="col-md-4 col-lg-3 border-end bg-white h-100 d-flex flex-column">
                <div class="p-3 border-bottom">
                    <h5 class="fw-bold mb-0 text-primary">{{ __('Messaging') }}</h5>
                </div>
                <div class="overflow-auto flex-grow-1">
                    <div class="list-group list-group-flush">
                        @foreach ($users as $user)
                            <a href="{{ route('chat.start', $user->id) }}"
                                class="list-group-item list-group-item-action border-0 py-3 d-flex align-items-center {{ isset($conversation) && $conversation->users->contains($user->id) ? 'bg-primary-subtle border-start border-primary border-4' : '' }}">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold me-3"
                                    style="width: 45px; height: 45px;">
                                    {{ substr($user->full_name, 0, 1) }}
                                </div>
                                <div class="overflow-hidden">
                                    <div class="fw-bold text-truncate">{{ $user->full_name }}</div>
                                    <small
                                        class="text-muted d-block text-truncate">{{ $user->department->name ?? __('Staff') }}</small>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- MAIN: Chat Area -->
            <div class="col-md-8 col-lg-9 d-flex flex-column h-100 bg-white">
                @if (isset($conversation))
                    <!-- Chat Header -->
                    @php $otherUser = $conversation->users->where('id', '!=', auth()->id())->first(); @endphp
                    <div class="p-3 border-bottom d-flex align-items-center bg-white">
                        <div class="fw-bold fs-5 text-dark">{{ $otherUser->full_name }}</div>
                        <span
                            class="ms-3 badge bg-success-subtle text-success border border-success-subtle small">{{ __('Active Now') }}</span>
                    </div>

                    <!-- Messages area -->
                    <div id="chat-window" class="flex-grow-1 p-4 overflow-auto bg-light"
                        style="background-image: url('https://www.transparenttextures.com/patterns/cubes.png');">
                        @foreach ($messages as $msg)
                            <div class="d-flex mb-4 {{ $msg->sender_id == auth()->id() ? 'justify-content-end' : '' }}">
                                <div class="card p-2 px-3 shadow-sm {{ $msg->sender_id == auth()->id() ? 'bg-primary text-white' : 'bg-white text-dark' }}"
                                    style="max-width: 70%; border-radius: 15px; {{ $msg->sender_id == auth()->id() ? 'border-bottom-right-radius: 2px;' : 'border-bottom-left-radius: 2px;' }}">
                                    <div class="small fw-bold opacity-75 mb-1" style="font-size: 0.7rem;">
                                        {{ $msg->sender->full_name }}</div>
                                    <div class="message-text">{{ $msg->body }}</div>
                                    <div class="text-end mt-1 opacity-50" style="font-size: 0.6rem;">
                                        {{ $msg->created_at->format('H:i') }}</div>
                                </div>
                            </div>
                        @endforeach
                        <div id="new-messages"></div>
                    </div>

                    <!-- Input area -->
                    <div class="p-3 border-top bg-white">
                        <form id="chat-form" class="input-group shadow-sm rounded">
                            <input type="text" id="message-input" class="form-control border-0 bg-light p-3"
                                placeholder="{{ __('Type a message') }}..." autocomplete="off">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-send-fill fs-5"></i>
                            </button>
                        </form>
                    </div>
                @else
                    <!-- Empty State (When no user selected) -->
                    <div class="h-100 d-flex flex-column align-items-center justify-content-center text-center p-5">
                        <div class="bg-primary bg-opacity-10 p-5 rounded-circle mb-4">
                            <i class="bi bi-chat-right-dots text-primary display-1"></i>
                        </div>
                        <h4 class="fw-bold">{{ __('Select a colleague') }}</h4>
                        <p class="text-muted">
                            {{ __('Pick someone from the left menu to start a secure government conversation.') }}</p>
                    </div>
                @endif
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            @if (isset($conversation))
                const chatWindow = $('#chat-window');
                chatWindow.scrollTop(chatWindow[0].scrollHeight);

                $('#chat-form').on('submit', function(e) {
                    e.preventDefault();
                    let body = $('#message-input').val();
                    if (!body.trim()) return;

                    $.post('{{ route('chat.send', $conversation->id) }}', {
                        _token: '{{ csrf_token() }}',
                        body: body
                    }, function(data) {
                        $('#message-input').val('');
                        appendMessage(data.message, true);
                    });
                });

                function appendMessage(msg, isMe) {
                    let html = `
                <div class="d-flex mb-4 ${isMe ? 'justify-content-end' : ''} animate__animated animate__fadeInUp animate__faster">
                    <div class="card p-2 px-3 shadow-sm ${isMe ? 'bg-primary text-white' : 'bg-white'}" 
                         style="max-width: 70%; border-radius: 15px; ${isMe ? 'border-bottom-right-radius: 2px;' : 'border-bottom-left-radius: 2px;'}">
                        <div class="message-text">${msg.body}</div>
                        <div class="text-end mt-1 opacity-50" style="font-size: 0.6rem;">${new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</div>
                    </div>
                </div>
            `;
                    $('#new-messages').append(html);
                    chatWindow.scrollTop(chatWindow[0].scrollHeight);
                }
            @endif
        });
    </script>
@endpush
