@extends('layouts.admin')

@section('content')
    <div class="chat-container card border-0 shadow-sm overflow-hidden animate__animated animate__fadeIn">
        <div class="row g-0 h-100">

            <div class="col-md-4 col-lg-3 border-end bg-white h-100 d-flex flex-column z-index-1">
                <div class="p-4 border-bottom d-flex justify-content-between align-items-center bg-white">
                    <h5 class="fw-bold mb-0 text-dark d-flex align-items-center" style="letter-spacing: -0.5px;">
                        <i class="bi bi-chat-left-text-fill text-primary me-2"></i> {{ __('Messages') }}
                    </h5>
                    <span class="badge bg-light text-secondary border rounded-pill">{{ $users->count() }}</span>
                </div>

                <div class="chat-sidebar-scroll flex-grow-1 p-2">
                    <div class="list-group list-group-flush gap-1">
                        @foreach ($users as $user)
                            @php
                                $isActive = isset($conversation) && $conversation->users->contains($user->id);
                            @endphp
                            <a href="{{ route('chat.start', $user->id) }}" 
                               class="contact-item list-group-item list-group-item-action border-0 p-3 rounded-4 d-flex align-items-center transition-all {{ $isActive ? 'active-contact' : '' }}">
                                
                                <div class="position-relative me-3">
                                    <div class="avatar-circle-md {{ $isActive ? 'bg-white text-primary shadow-sm' : 'bg-primary bg-opacity-10 text-primary' }} rounded-circle d-flex align-items-center justify-content-center fw-bold">
                                        {{ strtoupper(substr($user->full_name, 0, 1)) }}
                                    </div>
                                    <span class="position-absolute bottom-0 end-0 p-1 bg-success border border-2 border-white rounded-circle">
                                        <span class="visually-hidden">Online</span>
                                    </span>
                                </div>
                                
                                <div class="overflow-hidden flex-grow-1">
                                    <div class="fw-semibold text-truncate {{ $isActive ? 'text-primary' : 'text-dark' }}" style="font-size: 0.95rem;">
                                        {{ $user->full_name }}
                                    </div>
                                    <small class="d-block text-truncate {{ $isActive ? 'text-primary opacity-75' : 'text-muted' }}" style="font-size: 0.8rem;">
                                        {{ $user->department->name ?? __('Staff') }}
                                    </small>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-md-8 col-lg-9 d-flex flex-column h-100 chat-bg position-relative">
                @if (isset($conversation))
                    @php $otherUser = $conversation->users->where('id', '!=', auth()->id())->first(); @endphp
                    
                    <div class="p-3 px-4 border-bottom d-flex align-items-center bg-white shadow-sm z-index-2">
                        <div class="avatar-circle-sm bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold me-3">
                            {{ strtoupper(substr($otherUser->full_name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="fw-bold fs-6 text-dark lh-1">{{ $otherUser->full_name }}</div>
                            <div class="d-flex align-items-center mt-1">
                                <span class="pulse-dot-small bg-success me-2"></span>
                                <span class="small text-muted fw-medium" style="font-size: 0.75rem;">{{ __('Secure Connection Active') }}</span>
                            </div>
                        </div>
                        <div class="ms-auto">
                            <button class="btn btn-sm btn-light btn-icon text-secondary rounded-circle" data-bs-toggle="tooltip" title="{{ __('Information') }}">
                                <i class="bi bi-info-circle"></i>
                            </button>
                        </div>
                    </div>

                    <div id="chat-window" class="chat-canvas flex-grow-1 p-4 px-md-5 overflow-auto">
                        <div class="text-center mb-4">
                            <span class="badge bg-white text-muted border shadow-sm rounded-pill px-3 py-2 small fw-medium">
                                <i class="bi bi-lock-fill me-1"></i> {{ __('End-to-end encrypted protocol') }}
                            </span>
                        </div>

                        @foreach ($messages as $msg)
                            @php $isMe = $msg->sender_id == auth()->id(); @endphp
                            <div class="message-wrapper {{ $isMe ? 'msg-me' : 'msg-them' }}">
                                <div class="message-bubble shadow-sm {{ $isMe ? 'bg-primary text-white' : 'bg-white text-dark border border-light' }}">
                                    @if(!$isMe)
                                        <div class="fw-bold mb-1" style="font-size: 0.7rem; color: #1a73e8;">{{ $msg->sender->full_name }}</div>
                                    @endif
                                    
                                    <div class="message-text lh-base" style="font-size: 0.95rem;">{{ $msg->body }}</div>
                                    
                                    <div class="message-time text-end mt-1 d-flex align-items-center justify-content-end {{ $isMe ? 'text-white-50' : 'text-muted opacity-75' }}" style="font-size: 0.65rem;">
                                        {{ $msg->created_at->format('H:i') }}
                                        @if($isMe)
                                            <i class="bi bi-check2-all ms-1"></i>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        <div id="new-messages"></div>
                    </div>

                    <div class="p-3 px-md-4 border-top bg-white z-index-2">
                        <form id="chat-form" class="d-flex align-items-center">
                            <button type="button" class="btn btn-light btn-icon rounded-circle text-secondary me-2 flex-shrink-0" title="Attach file">
                                <i class="bi bi-paperclip fs-5"></i>
                            </button>
                            
                            <div class="input-group-custom flex-grow-1 mx-2 position-relative">
                                <input type="text" id="message-input" class="form-control chat-input" placeholder="{{ __('Type a secure message...') }}" autocomplete="off" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-send rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 ms-2" id="send-btn">
                                <i class="bi bi-send-fill"></i>
                            </button>
                        </form>
                    </div>

                @else
                    <div class="h-100 d-flex flex-column align-items-center justify-content-center text-center p-5 bg-light">
                        <div class="empty-state-icon bg-white shadow-sm rounded-circle d-flex align-items-center justify-content-center mb-4">
                            <i class="bi bi-chat-quote text-primary" style="font-size: 3rem;"></i>
                        </div>
                        <h4 class="fw-bold text-dark">{{ __('GovConnect Messaging') }}</h4>
                        <p class="text-secondary" style="max-width: 400px;">
                            {{ __('Select a colleague from the sidebar to establish a secure, encrypted communication channel.') }}
                        </p>
                    </div>
                @endif
            </div>

        </div>
    </div>

    <style>
        :root {
            --chat-height: calc(100vh - 110px);
            --google-blue: #1a73e8;
            --chat-bg: #f0f2f5;
        }

        .chat-container {
            height: var(--chat-height);
            min-height: 600px;
            border-radius: 16px;
        }

        .z-index-1 { z-index: 1; }
        .z-index-2 { z-index: 2; }

        /* Avatars */
        .avatar-circle-md { width: 44px; height: 44px; font-size: 18px; }
        .avatar-circle-sm { width: 38px; height: 38px; font-size: 15px; }

        /* Sidebar Contacts */
        .active-contact {
            background-color: #e8f0fe !important; /* Google active light blue */
            box-shadow: inset 4px 0 0 var(--google-blue); /* Left accent indicator */
        }
        .contact-item:hover:not(.active-contact) {
            background-color: #f8f9fa;
        }

        /* Custom Webkit Scrollbar for perfection */
        .chat-sidebar-scroll, .chat-canvas {
            scrollbar-width: thin;
            scrollbar-color: #dadce0 transparent;
        }
        .chat-sidebar-scroll::-webkit-scrollbar, .chat-canvas::-webkit-scrollbar {
            width: 6px;
        }
        .chat-sidebar-scroll::-webkit-scrollbar-track, .chat-canvas::-webkit-scrollbar-track {
            background: transparent;
        }
        .chat-sidebar-scroll::-webkit-scrollbar-thumb, .chat-canvas::-webkit-scrollbar-thumb {
            background-color: #dadce0;
            border-radius: 10px;
        }

        /* Chat Background Pattern (Subtle dots instead of harsh cubes) */
        .chat-bg {
            background-color: var(--chat-bg);
            background-image: radial-gradient(#d1d5db 1px, transparent 0);
            background-size: 20px 20px;
        }

        /* Message Bubbles Geometry */
        .message-wrapper {
            display: flex;
            margin-bottom: 1.25rem;
            width: 100%;
        }
        
        .msg-me {
            justify-content: flex-end;
        }
        .msg-them {
            justify-content: flex-start;
        }

        .message-bubble {
            max-width: 75%;
            padding: 10px 16px;
            position: relative;
            word-wrap: break-word;
        }

        /* Asymmetric Corners */
        .msg-me .message-bubble {
            border-radius: 18px 18px 4px 18px;
            background: var(--google-blue);
        }
        
        .msg-them .message-bubble {
            border-radius: 18px 18px 18px 4px;
        }

        /* Input Area Geometry */
        .chat-input {
            border-radius: 24px;
            background-color: #f1f3f4;
            border: 1px solid transparent;
            padding: 12px 20px;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }
        
        .chat-input:focus {
            background-color: #fff;
            border-color: var(--google-blue);
            box-shadow: 0 1px 6px rgba(32,33,36,0.1);
            outline: none;
        }

        .btn-send {
            width: 46px;
            height: 46px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-send:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(26, 115, 232, 0.3);
        }
        .btn-send:active { transform: scale(0.95); }

        .btn-icon { width: 42px; height: 42px; display: inline-flex; align-items: center; justify-content: center; }

        .empty-state-icon { width: 100px; height: 100px; }

        /* Pulse Dot */
        .pulse-dot-small {
            display: inline-block; width: 8px; height: 8px; border-radius: 50%;
            box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.5);
            animation: pulse-soft 2s infinite cubic-bezier(0.66, 0, 0, 1);
        }
        @keyframes pulse-soft { to { box-shadow: 0 0 0 6px rgba(25, 135, 84, 0); } }
    </style>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            @if (isset($conversation))
                const chatWindow = $('#chat-window');
                
                // Auto scroll down smoothly on load
                chatWindow.scrollTop(chatWindow[0].scrollHeight);

                $('#chat-form').on('submit', function(e) {
                    e.preventDefault();
                    let inputField = $('#message-input');
                    let body = inputField.val();
                    let sendBtn = $('#send-btn');
                    
                    if (!body.trim()) return;

                    // UI feedback: disable button temporarily
                    sendBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');

                    $.post('{{ route('chat.send', $conversation->id) }}', {
                        _token: '{{ csrf_token() }}',
                        body: body
                    }, function(data) {
                        inputField.val('');
                        sendBtn.prop('disabled', false).html('<i class="bi bi-send-fill"></i>');
                        
                        // Append using the exact same HTML structure as above
                        appendMessage(data.message, true);
                    }).fail(function() {
                        // Error handling reset
                        sendBtn.prop('disabled', false).html('<i class="bi bi-send-fill"></i>');
                        alert('{{ __("Failed to send message. Please check your connection.") }}');
                    });
                });

                function appendMessage(msg, isMe) {
                    const time = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                    
                    // Match the updated CSS structure precisely
                    let html = `
                        <div class="message-wrapper ${isMe ? 'msg-me' : 'msg-them'} animate__animated animate__fadeInUp animate__faster">
                            <div class="message-bubble shadow-sm ${isMe ? 'bg-primary text-white' : 'bg-white text-dark border border-light'}">
                                <div class="message-text lh-base" style="font-size: 0.95rem;">${msg.body}</div>
                                <div class="message-time text-end mt-1 d-flex align-items-center justify-content-end ${isMe ? 'text-white-50' : 'text-muted opacity-75'}" style="font-size: 0.65rem;">
                                    ${time} ${isMe ? '<i class="bi bi-check2-all ms-1"></i>' : ''}
                                </div>
                            </div>
                        </div>
                    `;
                    $('#new-messages').append(html);
                    
                    // Smooth scroll animation to new message
                    chatWindow.animate({ scrollTop: chatWindow[0].scrollHeight }, 300);
                }
            @endif
        });
    </script>
@endpush